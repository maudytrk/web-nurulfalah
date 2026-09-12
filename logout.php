<?php
/**
 * Skrip Logout & Pembersihan Session Admin
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

// 1. Inisialisasi/Hubungkan ke session yang sedang aktif
session_start();

// 2. Kosongkan semua variabel session
$_SESSION = array();

// 3. Hapus cookie session jika ada (mencegah session hijacking)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 4. Hancurkan session secara penuh di server
session_destroy();

// 5. Mulai session baru sebentar hanya untuk mengirim pesan notifikasi (opsional tapi bagus untuk UX)
session_start();
$_SESSION['success'] = "Anda telah berhasil keluar dari sistem.";

// 6. Redirect kembali ke halaman login
header("Location: login.php");
exit;