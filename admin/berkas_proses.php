<?php
/**
 * Engine Backend CRUD Berkas PPDB & Upload File PDF ke Folder /uploads/berkas/
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
    header("Location: berkas_ppdb.php");
    exit;
}

// 3. Panggil Koneksi Database
require_once '../koneksi.php';

$upload_dir = '../uploads/';

// Helper Function Upload Berkas PDF
function uploadPdfBerkas($file, $target_dir) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_tmp  = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Validasi Khusus PDF
        if ($file_ext !== 'pdf') {
            return ['status' => false, 'message' => 'Format berkas tidak diizinkan! Harus berupa file PDF.'];
        }

        // Validasi Ukuran File (Maksimal 10MB)
        if ($file_size > 10 * 1024 * 1024) {
            return ['status' => false, 'message' => 'Ukuran berkas PDF terlalu besar! Maksimal 10 MB.'];
        }

        // Buat folder jika belum tersedia
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate nama file unik
        $new_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);

        if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
            return ['status' => true, 'filename' => $new_filename];
        } else {
            return ['status' => false, 'message' => 'Gagal mengunggah berkas PDF ke direktori server.'];
        }
    }
    return ['status' => true, 'filename' => null];
}

$action = $_POST['action'] ?? '';

switch ($action) {

    // ==========================================
    // ACTION 1: TAMBAH BERKAS (INSERT)
    // ==========================================
    case 'tambah':
        $nama_berkas  = trim($_POST['nama_berkas'] ?? '');
        $jenis_berkas = trim($_POST['jenis_berkas'] ?? 'formulir');
        $target_unit  = trim($_POST['target_unit'] ?? 'RA');

        if (empty($nama_berkas) || empty($target_unit) || !isset($_FILES['file_pdf']) || $_FILES['file_pdf']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Nama Berkas, Target Unit, dan Berkas PDF wajib diisi!";
            header("Location: berkas_ppdb.php");
            exit;
        }

        $upload_result = uploadPdfBerkas($_FILES['file_pdf'], $upload_dir);

        if (!$upload_result['status']) {
            $_SESSION['error'] = $upload_result['message'];
            header("Location: berkas_ppdb.php");
            exit;
        }

        $nama_file = $upload_result['filename'];

        try {
            $stmt = $pdo->prepare("INSERT INTO berkas_ppdb (nama_berkas, jenis_berkas, target_unit, nama_file, tanggal_upload) VALUES (:nama_berkas, :jenis_berkas, :target_unit, :nama_file, CURDATE())");
            $stmt->execute([
                ':nama_berkas'  => $nama_berkas,
                ':jenis_berkas' => $jenis_berkas,
                ':target_unit'  => $target_unit,
                ':nama_file'    => $nama_file
            ]);

            $_SESSION['success'] = "Berkas PPDB baru berhasil diunggah!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menyimpan data ke database: " . $e->getMessage();
        }

        header("Location: berkas_ppdb.php");
        exit;

    // ==========================================
    // ACTION 2: EDIT BERKAS (UPDATE)
    // ==========================================
    case 'edit':
        $id           = (int)($_POST['id'] ?? 0);
        $nama_berkas  = trim($_POST['nama_berkas'] ?? '');
        $jenis_berkas = trim($_POST['jenis_berkas'] ?? 'formulir');
        $target_unit  = trim($_POST['target_unit'] ?? 'RA');

        if ($id <= 0 || empty($nama_berkas) || empty($target_unit)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: berkas_ppdb.php");
            exit;
        }

        try {
            // Cek file PDF lama
            $stmt_old = $pdo->prepare("SELECT nama_file FROM berkas_ppdb WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Data berkas tidak ditemukan!";
                header("Location: berkas_ppdb.php");
                exit;
            }

            $nama_file = $old_data['nama_file'];

            // Jika ada file PDF baru yang diunggah
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadPdfBerkas($_FILES['file_pdf'], $upload_dir);

                if (!$upload_result['status']) {
                    $_SESSION['error'] = $upload_result['message'];
                    header("Location: berkas_ppdb.php");
                    exit;
                }

                // Hapus berkas PDF lama dari server jika file baru berhasil di-upload
                if (!empty($nama_file) && file_exists($upload_dir . $nama_file)) {
                    unlink($upload_dir . $nama_file);
                }

                $nama_file = $upload_result['filename'];
            }

            $stmt_update = $pdo->prepare("UPDATE berkas_ppdb SET nama_berkas = :nama_berkas, jenis_berkas = :jenis_berkas, target_unit = :target_unit, nama_file = :nama_file WHERE id = :id");
            $stmt_update->execute([
                ':nama_berkas'  => $nama_berkas,
                ':jenis_berkas' => $jenis_berkas,
                ':target_unit'  => $target_unit,
                ':nama_file'    => $nama_file,
                ':id'           => $id
            ]);

            $_SESSION['success'] = "Data berkas PPDB berhasil diperbarui!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui database: " . $e->getMessage();
        }

        header("Location: berkas_ppdb.php");
        exit;

    // ==========================================
    // ACTION 3: HAPUS BERKAS (DELETE)
    // ==========================================
    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT nama_file FROM berkas_ppdb WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    // Hapus file PDF dari folder penyimpanan
                    if (!empty($data['nama_file']) && file_exists($upload_dir . $data['nama_file'])) {
                        unlink($upload_dir . $data['nama_file']);
                    }

                    // Hapus baris dari database
                    $stmt_delete = $pdo->prepare("DELETE FROM berkas_ppdb WHERE id = :id");
                    $stmt_delete->execute([':id' => $id]);

                    $_SESSION['success'] = "Berkas PPDB berhasil dihapus dari sistem!";
                } else {
                    $_SESSION['error'] = "Data berkas tidak ditemukan!";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "ID data tidak valid!";
        }

        header("Location: berkas_ppdb.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: berkas_ppdb.php");
        exit;
}