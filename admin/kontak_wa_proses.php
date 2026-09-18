<?php
/**
 * Backend CRUD Kontak WhatsApp Panitia PPDB
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kontak_wa.php");
    exit;
}

verify_csrf_token();

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'tambah':
        $nama_kontak = trim($_POST['nama_kontak'] ?? '');
        $nomor_wa    = trim($_POST['nomor_wa'] ?? '');
        $target_unit = trim($_POST['target_unit'] ?? 'RA');

        enforce_unit_access($target_unit, 'kontak_wa.php');

        // Format sanitasi nomor HP: Ubah 08xxx menjadi 628xxx
        $nomor_wa_clean = preg_replace('/[^0-9]/', '', $nomor_wa);
        if (substr($nomor_wa_clean, 0, 1) === '0') {
            $nomor_wa_clean = '62' . substr($nomor_wa_clean, 1);
        }

        if (empty($nama_kontak) || empty($nomor_wa_clean) || empty($target_unit)) {
            $_SESSION['error'] = "Nama Kontak, Nomor WA, dan Target Unit wajib diisi!";
            header("Location: kontak_wa.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO kontak_wa (nama_kontak, nomor_wa, target_unit) VALUES (:nama_kontak, :nomor_wa, :target_unit)");
            $stmt->execute([
                ':nama_kontak' => $nama_kontak,
                ':nomor_wa'    => $nomor_wa_clean,
                ':target_unit' => $target_unit
            ]);

            $_SESSION['success'] = "Kontak WhatsApp baru berhasil ditambahkan!";
        } catch (PDOException $e) {
            error_log("Insert Kontak WA Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat menyimpan kontak WhatsApp.";
        }

        header("Location: kontak_wa.php");
        exit;

    case 'edit':
        $id          = (int)($_POST['id'] ?? 0);
        $nama_kontak = trim($_POST['nama_kontak'] ?? '');
        $nomor_wa    = trim($_POST['nomor_wa'] ?? '');
        $target_unit = trim($_POST['target_unit'] ?? 'RA');

        enforce_unit_access($target_unit, 'kontak_wa.php');

        $nomor_wa_clean = preg_replace('/[^0-9]/', '', $nomor_wa);
        if (substr($nomor_wa_clean, 0, 1) === '0') {
            $nomor_wa_clean = '62' . substr($nomor_wa_clean, 1);
        }

        if ($id <= 0 || empty($nama_kontak) || empty($nomor_wa_clean) || empty($target_unit)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: kontak_wa.php");
            exit;
        }

        try {
            $stmt_old = $pdo->prepare("SELECT target_unit FROM kontak_wa WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Kontak tidak ditemukan!";
                header("Location: kontak_wa.php");
                exit;
            }

            enforce_unit_access($old_data['target_unit'], 'kontak_wa.php');

            $stmt = $pdo->prepare("UPDATE kontak_wa SET nama_kontak = :nama_kontak, nomor_wa = :nomor_wa, target_unit = :target_unit WHERE id = :id");
            $stmt->execute([
                ':nama_kontak' => $nama_kontak,
                ':nomor_wa'    => $nomor_wa_clean,
                ':target_unit' => $target_unit,
                ':id'          => $id
            ]);

            $_SESSION['success'] = "Kontak WhatsApp berhasil diperbarui!";
        } catch (PDOException $e) {
            error_log("Update Kontak WA Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui kontak WhatsApp.";
        }

        header("Location: kontak_wa.php");
        exit;

    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT target_unit FROM kontak_wa WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    enforce_unit_access($data['target_unit'], 'kontak_wa.php');

                    $stmt_del = $pdo->prepare("DELETE FROM kontak_wa WHERE id = :id");
                    $stmt_del->execute([':id' => $id]);

                    $_SESSION['success'] = "Kontak WhatsApp berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Kontak tidak ditemukan!";
                }
            } catch (PDOException $e) {
                error_log("Delete Kontak WA Error: " . $e->getMessage());
                $_SESSION['error'] = "Terjadi kesalahan saat menghapus kontak WhatsApp.";
            }
        } else {
            $_SESSION['error'] = "ID tidak valid!";
        }

        header("Location: kontak_wa.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: kontak_wa.php");
        exit;
}
