<?php
/**
 * Engine Backend CRUD FAQ PPDB
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
    header("Location: faq.php");
    exit;
}

// 3. Panggil Koneksi Database
require_once '../koneksi.php';

$action = $_POST['action'] ?? '';

switch ($action) {

    // ==========================================
    // ACTION 1: TAMBAH FAQ (INSERT)
    // ==========================================
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

            $_SESSION['success'] = "FAQ baru berhasil ditambahkan!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menyimpan data ke database: " . $e->getMessage();
        }

        header("Location: faq.php");
        exit;

    // ==========================================
    // ACTION 2: EDIT FAQ (UPDATE)
    // ==========================================
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
                ':id'          => $id
            ]);

            $_SESSION['success'] = "Data FAQ berhasil diperbarui!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui database: " . $e->getMessage();
        }

        header("Location: faq.php");
        exit;

    // ==========================================
    // ACTION 3: HAPUS FAQ (DELETE)
    // ==========================================
    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM faq_ppdb WHERE id = :id");
                $stmt->execute([':id' => $id]);

                $_SESSION['success'] = "FAQ berhasil dihapus dari sistem!";
            } catch (PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
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