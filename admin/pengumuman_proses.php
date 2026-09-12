<?php
/**
 * Engine Backend CRUD Pengumuman & Berkas Lampiran
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Akses Backend Admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 2. Hanya menerima request via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pengumuman.php");
    exit;
}

// 3. Panggil Koneksi Database
require_once '../koneksi.php';

$upload_dir = '../uploads/pengumuman/';

// Helper Function Upload File
function uploadFileLampiran($file, $target_dir) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_tmp  = $file['tmp_name'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];

        if (!in_array($file_ext, $allowed_ext)) {
            return ['status' => false, 'message' => 'Format file tidak diizinkan! Hanya PDF, JPG, PNG, DOC, dan DOCX.'];
        }

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $new_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);

        if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
            return ['status' => true, 'filename' => $new_filename];
        } else {
            return ['status' => false, 'message' => 'Gagal mengunggah file ke server.'];
        }
    }
    return ['status' => true, 'filename' => null];
}

$action = $_POST['action'] ?? '';

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

        if (empty($judul) || empty($target_unit) || empty($isi_pengumuman)) {
            $_SESSION['error'] = "Judul, Target Unit, dan Isi Pengumuman wajib diisi!";
            header("Location: pengumuman_tambah.php");
            exit;
        }

        $upload_result = uploadFileLampiran($_FILES['file_lampiran'] ?? null, $upload_dir);

        if (!$upload_result['status']) {
            $_SESSION['error'] = $upload_result['message'];
            header("Location: pengumuman_tambah.php");
            exit;
        }

        $nama_file = $upload_result['filename'];

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
            $_SESSION['error'] = "Gagal menyimpan ke database: " . $e->getMessage();
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

        if ($id <= 0 || empty($judul) || empty($target_unit) || empty($isi_pengumuman)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: pengumuman.php");
            exit;
        }

        try {
            // Cek file lama
            $stmt_old = $pdo->prepare("SELECT file_lampiran FROM pengumuman WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Data pengumuman tidak ditemukan!";
                header("Location: pengumuman.php");
                exit;
            }

            $nama_file = $old_data['file_lampiran'];

            // Ganti file baru jika di-upload
            if (isset($_FILES['file_lampiran']) && $_FILES['file_lampiran']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadFileLampiran($_FILES['file_lampiran'], $upload_dir);

                if (!$upload_result['status']) {
                    $_SESSION['error'] = $upload_result['message'];
                    header("Location: pengumuman.php");
                    exit;
                }

                // Hapus berkas lama jika berkas baru berhasil naik
                if (!empty($nama_file) && file_exists($upload_dir . $nama_file)) {
                    unlink($upload_dir . $nama_file);
                }

                $nama_file = $upload_result['filename'];
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
            $_SESSION['error'] = "Gagal memperbarui database: " . $e->getMessage();
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
                $stmt = $pdo->prepare("SELECT file_lampiran FROM pengumuman WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    // Hapus berkas dari server jika ada
                    if (!empty($data['file_lampiran']) && file_exists($upload_dir . $data['file_lampiran'])) {
                        unlink($upload_dir . $data['file_lampiran']);
                    }

                    $stmt_delete = $pdo->prepare("DELETE FROM pengumuman WHERE id = :id");
                    $stmt_delete->execute([':id' => $id]);

                    $_SESSION['success'] = "Pengumuman dan berkas terlampir berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Data pengumuman tidak ditemukan!";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
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