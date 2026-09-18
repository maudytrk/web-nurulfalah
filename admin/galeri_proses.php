<?php
/**
 * Backend CRUD Galeri Foto Sekolah & Ekskul
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: galeri.php");
    exit;
}

verify_csrf_token();

$upload_dir = '../uploads/galeri/';
$action     = $_POST['action'] ?? '';

switch ($action) {

    case 'tambah':
        $judul_kegiatan = trim($_POST['judul_kegiatan'] ?? '');
        $jenis_ekskul   = trim($_POST['jenis_ekskul'] ?? '');
        $target_unit    = trim($_POST['target_unit'] ?? 'RA');

        enforce_unit_access($target_unit, 'galeri.php');

        if (empty($judul_kegiatan) || empty($target_unit)) {
            $_SESSION['error'] = "Judul Kegiatan dan Target Unit wajib diisi!";
            header("Location: galeri.php");
            exit;
        }

        if (!isset($_FILES['file_gambar']) || $_FILES['file_gambar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Silakan pilih berkas gambar foto kegiatan!";
            header("Location: galeri.php");
            exit;
        }

        $val = validate_uploaded_file($_FILES['file_gambar'], ['jpg', 'jpeg', 'png', 'webp'], 5);
        if (!$val[0]) {
            $_SESSION['error'] = $val[1];
            header("Location: galeri.php");
            exit;
        }

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext       = strtolower(pathinfo($_FILES['file_gambar']['name'], PATHINFO_EXTENSION));
        $nama_file = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        if (!move_uploaded_file($_FILES['file_gambar']['tmp_name'], $upload_dir . $nama_file)) {
            $_SESSION['error'] = "Gagal mengunggah foto ke server.";
            header("Location: galeri.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO galeri (judul_kegiatan, nama_file_foto, jenis_ekskul, target_unit, tanggal_unggah) VALUES (:judul_kegiatan, :nama_file_foto, :jenis_ekskul, :target_unit, NOW())");
            $stmt->execute([
                ':judul_kegiatan' => $judul_kegiatan,
                ':nama_file_foto' => $nama_file,
                ':jenis_ekskul'   => $jenis_ekskul,
                ':target_unit'    => $target_unit
            ]);

            $_SESSION['success'] = "Foto galeri baru berhasil diunggah!";
        } catch (PDOException $e) {
            error_log("Insert Galeri Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat menyimpan data galeri ke database.";
        }

        header("Location: galeri.php");
        exit;

    case 'edit':
        $id             = (int)($_POST['id'] ?? 0);
        $judul_kegiatan = trim($_POST['judul_kegiatan'] ?? '');
        $jenis_ekskul   = trim($_POST['jenis_ekskul'] ?? '');
        $target_unit    = trim($_POST['target_unit'] ?? 'RA');

        enforce_unit_access($target_unit, 'galeri.php');

        if ($id <= 0 || empty($judul_kegiatan) || empty($target_unit)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: galeri.php");
            exit;
        }

        try {
            $stmt_old = $pdo->prepare("SELECT nama_file_foto, target_unit FROM galeri WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Data foto galeri tidak ditemukan!";
                header("Location: galeri.php");
                exit;
            }

            enforce_unit_access($old_data['target_unit'], 'galeri.php');
            $nama_file = $old_data['nama_file_foto'];

            if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] === UPLOAD_ERR_OK) {
                $val = validate_uploaded_file($_FILES['file_gambar'], ['jpg', 'jpeg', 'png', 'webp'], 5);
                if (!$val[0]) {
                    $_SESSION['error'] = $val[1];
                    header("Location: galeri.php");
                    exit;
                }

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $ext      = strtolower(pathinfo($_FILES['file_gambar']['name'], PATHINFO_EXTENSION));
                $new_file = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

                if (move_uploaded_file($_FILES['file_gambar']['tmp_name'], $upload_dir . $new_file)) {
                    if (!empty($nama_file) && file_exists($upload_dir . $nama_file)) {
                        @unlink($upload_dir . $nama_file);
                    }
                    $nama_file = $new_file;
                }
            }

            $stmt_update = $pdo->prepare("UPDATE galeri SET judul_kegiatan = :judul_kegiatan, nama_file_foto = :nama_file_foto, jenis_ekskul = :jenis_ekskul, target_unit = :target_unit WHERE id = :id");
            $stmt_update->execute([
                ':judul_kegiatan' => $judul_kegiatan,
                ':nama_file_foto' => $nama_file,
                ':jenis_ekskul'   => $jenis_ekskul,
                ':target_unit'    => $target_unit,
                ':id'             => $id
            ]);

            $_SESSION['success'] = "Data foto galeri berhasil diperbarui!";
        } catch (PDOException $e) {
            error_log("Update Galeri Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui galeri.";
        }

        header("Location: galeri.php");
        exit;

    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT nama_file_foto, target_unit FROM galeri WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    enforce_unit_access($data['target_unit'], 'galeri.php');

                    if (!empty($data['nama_file_foto']) && file_exists($upload_dir . $data['nama_file_foto'])) {
                        @unlink($upload_dir . $data['nama_file_foto']);
                    }

                    $stmt_delete = $pdo->prepare("DELETE FROM galeri WHERE id = :id");
                    $stmt_delete->execute([':id' => $id]);

                    $_SESSION['success'] = "Foto galeri berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Data foto tidak ditemukan!";
                }
            } catch (PDOException $e) {
                error_log("Delete Galeri Error: " . $e->getMessage());
                $_SESSION['error'] = "Terjadi kesalahan saat menghapus foto galeri.";
            }
        } else {
            $_SESSION['error'] = "ID data tidak valid!";
        }

        header("Location: galeri.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: galeri.php");
        exit;
}