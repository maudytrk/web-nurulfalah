<?php
/**
 * Engine Backend CRUD FAQ PPDB
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: faq.php");
    exit;
}

verify_csrf_token();

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'tambah':
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $jawaban    = trim($_POST['jawaban'] ?? '');
        $urutan     = (int)($_POST['urutan'] ?? 1);

        if (empty($pertanyaan) || empty($jawaban)) {
            $_SESSION['error'] = "Pertanyaan dan Jawaban wajib diisi!";
            header("Location: faq.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO faq_ppdb (pertanyaan, jawaban, urutan) VALUES (:pertanyaan, :jawaban, :urutan)");
            $stmt->execute([
                ':pertanyaan' => $pertanyaan,
                ':jawaban'    => $jawaban,
                ':urutan'     => $urutan
            ]);

            $_SESSION['success'] = "Pertanyaan FAQ baru berhasil ditambahkan!";
        } catch (PDOException $e) {
            error_log("Insert FAQ Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat menyimpan FAQ ke database.";
        }

        header("Location: faq.php");
        exit;

    case 'edit':
        $id         = (int)($_POST['id'] ?? 0);
        $pertanyaan = trim($_POST['pertanyaan'] ?? '');
        $jawaban    = trim($_POST['jawaban'] ?? '');
        $urutan     = (int)($_POST['urutan'] ?? 1);

        if ($id <= 0 || empty($pertanyaan) || empty($jawaban)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: faq.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE faq_ppdb SET pertanyaan = :pertanyaan, jawaban = :jawaban, urutan = :urutan WHERE id = :id");
            $stmt->execute([
                ':pertanyaan' => $pertanyaan,
                ':jawaban'    => $jawaban,
                ':urutan'     => $urutan,
                ':id'         => $id
            ]);

            $_SESSION['success'] = "Data FAQ berhasil diperbarui!";
        } catch (PDOException $e) {
            error_log("Update FAQ Error: " . $e->getMessage());
            $_SESSION['error'] = "Terjadi kesalahan saat memperbarui FAQ.";
        }

        header("Location: faq.php");
        exit;

    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM faq_ppdb WHERE id = :id");
                $stmt->execute([':id' => $id]);

                $_SESSION['success'] = "Pertanyaan FAQ berhasil dihapus!";
            } catch (PDOException $e) {
                error_log("Delete FAQ Error: " . $e->getMessage());
                $_SESSION['error'] = "Terjadi kesalahan saat menghapus FAQ.";
            }
        } else {
            $_SESSION['error'] = "ID data tidak valid!";
        }

        header("Location: faq.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: faq.php");
        exit;
}