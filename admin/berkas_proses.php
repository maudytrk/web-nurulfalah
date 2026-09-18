<?php
/**
 * Backend Engine CRUD Berkas PPDB (Formulir & Brosur PDF)
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: berkas_ppdb.php");
    exit;
}

verify_csrf_token();

$upload_dir = '../uploads/';
$action     = $_POST['action'] ?? '';

switch ($action) {

    case 'tambah':
        $nama_berkas  = trim($_POST['nama_berkas'] ?? '');
        $jenis_berkas = trim($_POST['jenis_berkas'] ?? 'formulir');
        $target_unit  = trim($_POST['target_unit'] ?? 'RA');

        enforce_unit_access($target_unit, 'berkas_ppdb.php');

        if (empty($nama_berkas) || empty($target_unit)) {
            $_SESSION['error'] = "Nama Berkas dan Target Unit wajib diisi!";
            header("Location: berkas_ppdb.php");
            exit;
        }

        if (!isset($_FILES['file_pdf']) || $_FILES['file_pdf']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Silakan pilih berkas PDF untuk diunggah!";
            header("Location: berkas_ppdb.php");
            exit;
        }

        $val = validate_uploaded_file($_FILES['file_pdf'], ['pdf'], 10);
        if (!$val[0]) {
            $_SESSION['error'] = $val[1];
            header("Location: berkas_ppdb.php");
            exit;
        }

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $nama_file = time() . '_' . bin2hex(random_bytes(4)) . '.pdf';

        if (!move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $nama_file)) {
            $_SESSION['error'] = "Gagal mengunggah berkas PDF ke server.";
            header("Location: berkas_ppdb.php");
            exit;
        }

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
            error_log("Insert Berkas Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat menyimpan berkas PPDB.";
        }

        header("Location: berkas_ppdb.php");
        exit;

    case 'edit':
        $id           = (int)($_POST['id'] ?? 0);
        $nama_berkas  = trim($_POST['nama_berkas'] ?? '');
        $jenis_berkas = trim($_POST['jenis_berkas'] ?? 'formulir');
        $target_unit  = trim($_POST['target_unit'] ?? 'RA');

        enforce_unit_access($target_unit, 'berkas_ppdb.php');

        if ($id <= 0 || empty($nama_berkas) || empty($target_unit)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: berkas_ppdb.php");
            exit;
        }

        try {
            $stmt_old = $pdo->prepare("SELECT nama_file, target_unit FROM berkas_ppdb WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Data berkas PPDB tidak ditemukan!";
                header("Location: berkas_ppdb.php");
                exit;
            }

            enforce_unit_access($old_data['target_unit'], 'berkas_ppdb.php');
            $nama_file = $old_data['nama_file'];

            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $val = validate_uploaded_file($_FILES['file_pdf'], ['pdf'], 10);
                if (!$val[0]) {
                    $_SESSION['error'] = $val[1];
                    header("Location: berkas_ppdb.php");
                    exit;
                }

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $new_file = time() . '_' . bin2hex(random_bytes(4)) . '.pdf';

                if (move_uploaded_file($_FILES['file_pdf']['tmp_name'], $upload_dir . $new_file)) {
                    if (!empty($nama_file) && file_exists($upload_dir . $nama_file)) {
                        @unlink($upload_dir . $nama_file);
                    }
                    $nama_file = $new_file;
                }
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
            error_log("Update Berkas Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui berkas PPDB.";
        }

        header("Location: berkas_ppdb.php");
        exit;

    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT nama_file, target_unit FROM berkas_ppdb WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    enforce_unit_access($data['target_unit'], 'berkas_ppdb.php');

                    if (!empty($data['nama_file']) && file_exists($upload_dir . $data['nama_file'])) {
                        @unlink($upload_dir . $data['nama_file']);
                    }

                    $stmt_delete = $pdo->prepare("DELETE FROM berkas_ppdb WHERE id = :id");
                    $stmt_delete->execute([':id' => $id]);

                    $_SESSION['success'] = "Berkas PPDB berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Data berkas tidak ditemukan!";
                }
            } catch (PDOException $e) {
                error_log("Delete Berkas Error: " . $e->getMessage());
                $_SESSION['error'] = "Terjadi kesalahan saat menghapus berkas PPDB.";
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