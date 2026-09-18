<?php
/**
 * Engine Backend CRUD Pengumuman & Berkas Lampiran
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pengumuman.php");
    exit;
}

verify_csrf_token();

$upload_dir = '../uploads/pengumuman/';
$action     = $_POST['action'] ?? '';

switch ($action) {

    // ==========================================
    // ACTION 1: TAMBAH DATA (INSERT)
    // ==========================================
    case 'tambah':
        $judul          = trim($_POST['judul'] ?? '');
        $tanggal        = trim($_POST['tanggal'] ?? date('Y-m-d'));
        $target_unit    = trim($_POST['target_unit'] ?? 'Yayasan');
        $isi_pengumuman = trim($_POST['isi_pengumuman'] ?? '');
        $id_user        = (int)($_SESSION['admin_id'] ?? 1);

        enforce_unit_access($target_unit, 'pengumuman_tambah.php');

        if (empty($judul) || empty($target_unit) || empty($isi_pengumuman)) {
            $_SESSION['error'] = "Judul, Target Unit, dan Isi Pengumuman wajib diisi!";
            header("Location: pengumuman_tambah.php");
            exit;
        }

        $nama_file = null;
        if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === UPLOAD_ERR_OK) {
            $val = validate_uploaded_file($_FILES['file_lampiran'], ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 10);
            if (!$val[0]) {
                $_SESSION['error'] = $val[1];
                header("Location: pengumuman_tambah.php");
                exit;
            }

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $ext       = strtolower(pathinfo($_FILES['file_lampiran']['name'], PATHINFO_EXTENSION));
            $nama_file = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            if (!move_uploaded_file($_FILES['file_lampiran']['tmp_name'], $upload_dir . $nama_file)) {
                $_SESSION['error'] = "Gagal mengunggah file lampiran ke server.";
                header("Location: pengumuman_tambah.php");
                exit;
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, isi_pengumuman, tanggal_post, target_unit, file_lampiran, id_user) VALUES (:judul, :isi_pengumuman, :tanggal_post, :target_unit, :file_lampiran, :id_user)");
            $stmt->execute([
                ':judul'          => $judul,
                ':isi_pengumuman' => $isi_pengumuman,
                ':tanggal_post'   => $tanggal,
                ':target_unit'    => $target_unit,
                ':file_lampiran'  => $nama_file,
                ':id_user'        => $id_user
            ]);

            $_SESSION['success'] = "Pengumuman baru berhasil diterbitkan!";
        } catch (PDOException $e) {
            error_log("Insert Pengumuman Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat menyimpan pengumuman ke database.";
        }

        header("Location: pengumuman.php");
        exit;

    // ==========================================
    // ACTION 2: EDIT DATA (UPDATE)
    // ==========================================
    case 'edit':
        $id             = (int)($_POST['id'] ?? 0);
        $judul          = trim($_POST['judul'] ?? '');
        $tanggal        = trim($_POST['tanggal'] ?? date('Y-m-d'));
        $target_unit    = trim($_POST['target_unit'] ?? 'Yayasan');
        $isi_pengumuman = trim($_POST['isi_pengumuman'] ?? '');

        enforce_unit_access($target_unit, 'pengumuman.php');

        if ($id <= 0 || empty($judul) || empty($target_unit) || empty($isi_pengumuman)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: pengumuman.php");
            exit;
        }

        try {
            $stmt_old = $pdo->prepare("SELECT file_lampiran, target_unit FROM pengumuman WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Data pengumuman tidak ditemukan!";
                header("Location: pengumuman.php");
                exit;
            }

            enforce_unit_access($old_data['target_unit'], 'pengumuman.php');

            $nama_file = $old_data['file_lampiran'];

            if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === UPLOAD_ERR_OK) {
                $val = validate_uploaded_file($_FILES['file_lampiran'], ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'], 10);
                if (!$val[0]) {
                    $_SESSION['error'] = $val[1];
                    header("Location: pengumuman.php");
                    exit;
                }

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $ext      = strtolower(pathinfo($_FILES['file_lampiran']['name'], PATHINFO_EXTENSION));
                $new_file = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

                if (move_uploaded_file($_FILES['file_lampiran']['tmp_name'], $upload_dir . $new_file)) {
                    if (!empty($nama_file) && file_exists($upload_dir . $nama_file)) {
                        @unlink($upload_dir . $nama_file);
                    }
                    $nama_file = $new_file;
                }
            }

            $stmt_update = $pdo->prepare("UPDATE pengumuman SET judul = :judul, target_unit = :target_unit, isi_pengumuman = :isi_pengumuman, file_lampiran = :file_lampiran, tanggal_post = :tanggal_post WHERE id = :id");
            $stmt_update->execute([
                ':judul'          => $judul,
                ':target_unit'    => $target_unit,
                ':isi_pengumuman' => $isi_pengumuman,
                ':file_lampiran'  => $nama_file,
                ':tanggal_post'   => $tanggal,
                ':id'             => $id
            ]);

            $_SESSION['success'] = "Pengumuman berhasil diperbarui!";
        } catch (PDOException $e) {
            error_log("Update Pengumuman Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui pengumuman.";
        }

        header("Location: pengumuman.php");
        exit;

    // ==========================================
    // ACTION 3: HAPUS DATA (DELETE)
    // ==========================================
    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT file_lampiran, target_unit FROM pengumuman WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    enforce_unit_access($data['target_unit'], 'pengumuman.php');

                    if (!empty($data['file_lampiran']) && file_exists($upload_dir . $data['file_lampiran'])) {
                        @unlink($upload_dir . $data['file_lampiran']);
                    }

                    $stmt_delete = $pdo->prepare("DELETE FROM pengumuman WHERE id = :id");
                    $stmt_delete->execute([':id' => $id]);

                    $_SESSION['success'] = "Pengumuman dan berkas terlampir berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Data pengumuman tidak ditemukan!";
                }
            } catch (PDOException $e) {
                error_log("Delete Pengumuman Error: " . $e->getMessage());
                $_SESSION['error'] = "Terjadi kesalahan saat menghapus data.";
            }
        } else {
            $_SESSION['error'] = "ID data tidak valid!";
        }

        header("Location: pengumuman.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: pengumuman.php");
        exit;
}