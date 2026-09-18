<?php
/**
 * Helper CSRF Protection
 * YPI Nurul Falah
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate atau ambil CSRF Token dari Session
 */
function get_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render input field hidden untuk CSRF Token
 */
function csrf_field() {
    $token = get_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Verifikasi CSRF Token dari input POST
 */
function verify_csrf_token() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            $_SESSION['error'] = "Validasi keamanan (CSRF Token) gagal. Silakan muat ulang halaman dan coba lagi.";
            $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
            header("Location: " . $redirect);
            exit;
        }
    }
}
