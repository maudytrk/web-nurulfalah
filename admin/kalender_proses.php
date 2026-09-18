<?php
/**
 * Backend CRUD Kalender Akademik & Agenda KBM
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kalender.php");
    exit;
}

verify_csrf_token();

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'tambah':
        $judul_agenda    = trim($_POST['judul_agenda'] ?? '');
        $tanggal_mulai   = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = !empty($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : null;
        $target_unit     = trim($_POST['target_unit'] ?? 'Yayasan');
        $keterangan      = trim($_POST['keterangan'] ?? '');

        enforce_unit_access($target_unit, 'kalender.php');

        if (empty($judul_agenda) || empty($tanggal_mulai) || empty($target_unit)) {
            $_SESSION['error'] = "Judul Agenda, Tanggal Mulai, dan Target Unit wajib diisi!";
            header("Location: kalender.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO kalender_akademik (judul_agenda, tanggal_mulai, tanggal_selesai, target_unit, keterangan) VALUES (:judul_agenda, :tanggal_mulai, :tanggal_selesai, :target_unit, :keterangan)");
            $stmt->execute([
                ':judul_agenda'    => $judul_agenda,
                ':tanggal_mulai'   => $tanggal_mulai,
                ':tanggal_selesai' => $tanggal_selesai,
                ':target_unit'    => $target_unit,
                ':keterangan'      => $keterangan
            ]);

            $_SESSION['success'] = "Agenda akademik baru berhasil ditambahkan!";
        } catch (PDOException $e) {
            error_log("Insert Kalender Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat menyimpan agenda akademik.";
        }

        header("Location: kalender.php");
        exit;

    case 'edit':
        $id              = (int)($_POST['id'] ?? 0);
        $judul_agenda    = trim($_POST['judul_agenda'] ?? '');
        $tanggal_mulai   = trim($_POST['tanggal_mulai'] ?? '');
        $tanggal_selesai = !empty($_POST['tanggal_selesai']) ? trim($_POST['tanggal_selesai']) : null;
        $target_unit     = trim($_POST['target_unit'] ?? 'Yayasan');
        $keterangan      = trim($_POST['keterangan'] ?? '');

        enforce_unit_access($target_unit, 'kalender.php');

        if ($id <= 0 || empty($judul_agenda) || empty($tanggal_mulai) || empty($target_unit)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: kalender.php");
            exit;
        }

        try {
            $stmt_old = $pdo->prepare("SELECT target_unit FROM kalender_akademik WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Agenda tidak ditemukan!";
                header("Location: kalender.php");
                exit;
            }

            enforce_unit_access($old_data['target_unit'], 'kalender.php');

            $stmt = $pdo->prepare("UPDATE kalender_akademik SET judul_agenda = :judul_agenda, tanggal_mulai = :tanggal_mulai, tanggal_selesai = :tanggal_selesai, target_unit = :target_unit, keterangan = :keterangan WHERE id = :id");
            $stmt->execute([
                ':judul_agenda'    => $judul_agenda,
                ':tanggal_mulai'   => $tanggal_mulai,
                ':tanggal_selesai' => $tanggal_selesai,
                ':target_unit'    => $target_unit,
                ':keterangan'      => $keterangan,
                ':id'             => $id
            ]);

            $_SESSION['success'] = "Agenda akademik berhasil diperbarui!";
        } catch (PDOException $e) {
            error_log("Update Kalender Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui agenda akademik.";
        }

        header("Location: kalender.php");
        exit;

    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT target_unit FROM kalender_akademik WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    enforce_unit_access($data['target_unit'], 'kalender.php');

                    $stmt_del = $pdo->prepare("DELETE FROM kalender_akademik WHERE id = :id");
                    $stmt_del->execute([':id' => $id]);

                    $_SESSION['success'] = "Agenda akademik berhasil dihapus!";
                } else {
                    $_SESSION['error'] = "Agenda tidak ditemukan!";
                }
            } catch (PDOException $e) {
                error_log("Delete Kalender Error: " . $e->getMessage());
                $_SESSION['error'] = "Terjadi kesalahan saat menghapus agenda akademik.";
            }
        } else {
            $_SESSION['error'] = "ID tidak valid!";
        }

        header("Location: kalender.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: kalender.php");
        exit;
}
