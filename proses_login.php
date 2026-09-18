<?php
/**
 * Skrip Validasi Kredensial Admin & Penciptaan Session
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// Panggil file koneksi database
require_once 'koneksi.php';

// Pastikan skrip diproses hanya via method POST dari tombol submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_login'])) {
    
    // Ambil input dan bersihkan dari whitespace
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username dan Password wajib diisi!";
        header("Location: login.php");
        exit;
    }

    try {
        // PERUBAHAN DI SINI: Mengubah 'admin' menjadi 'users'
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi keberadaan akun dan kecocokan password
        if ($user && password_verify($password, $user['password'])) {
            // Buat Session Login Admin
            session_regenerate_id(true); // Keamanan dari session fixation attack
            $_SESSION['login_admin'] = true;
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_name']  = $user['nama_lengkap'] ?? $user['username'];
            $_SESSION['username']    = $user['username'];
            $_SESSION['role']        = $user['role'] ?? 'admin_unit';
            $_SESSION['unit_akses']  = $user['unit_akses'] ?? 'all';

            // Redirect ke Dashboard Admin
            header("Location: admin/index.php");
            exit;
        } else {
            $_SESSION['error'] = "Username atau password yang Anda masukkan salah!";
            header("Location: login.php");
            exit;
        }

    } catch (PDOException $e) {
        // Tangani jika terjadi kesalahan pada database
        $_SESSION['error'] = "Terjadi kesalahan sistem: " . $e->getMessage();
        header("Location: login.php");
        exit;
    }

} else {
    // Jika mencoba akses langsung file ini via URL tanpa POST
    header("Location: login.php");
    exit;
}