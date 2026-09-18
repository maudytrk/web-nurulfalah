<?php
/**
 * Helper Otorisasi Role Admin & Keamanan File Upload
 * YPI Nurul Falah
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Pastikan Pengguna Sudah Log In Sebagai Admin
 */
function check_admin_auth() {
    if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Cek apakah user ber-role super_admin
 */
function is_super_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin';
}

/**
 * Ambil unit_akses pengguna (misal: 'RA', 'MI', 'SMPI', atau 'all')
 */
function get_user_unit_access() {
    return $_SESSION['unit_akses'] ?? 'all';
}

/**
 * Periksa apakah admin diizinkan mengakses / memanipulasi data unit tertentu
 */
function can_access_unit($target_unit) {
    if (is_super_admin()) {
        return true;
    }
    $user_unit = get_user_unit_access();
    if ($user_unit === 'all') {
        return true;
    }
    if ($target_unit === 'Yayasan' || $target_unit === 'all' || empty($target_unit)) {
        return true;
    }
    return strcasecmp($user_unit, $target_unit) === 0;
}

/**
 * Enforce otorisasi unit di backend. Jika tidak diizinkan, kembalikan error.
 */
function enforce_unit_access($target_unit, $redirect_page = 'index.php') {
    if (!can_access_unit($target_unit)) {
        $_SESSION['error'] = "Akses Ditolak! Anda hanya memiliki wewenang untuk unit " . htmlspecialchars(get_user_unit_access()) . ".";
        header("Location: " . $redirect_page);
        exit;
    }
}

/**
 * Validasi File Upload secara aman (Ekstensi + MIME Type + Max Size)
 */
function validate_uploaded_file($file, $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'pdf'], $max_size_mb = 5) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return [false, "Gagal mengunggah file atau tidak ada file yang dipilih."];
    }

    $max_bytes = $max_size_mb * 1024 * 1024;
    if ($file['size'] > $max_bytes) {
        return [false, "Ukuran file terlalu besar! Maksimal " . $max_size_mb . " MB."];
    }

    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_exts)) {
        return [false, "Format file '." . htmlspecialchars($file_ext) . "' tidak diizinkan! Format yang diperbolehkan: " . implode(', ', $allowed_exts)];
    }

    // Validasi MIME Type dengan finfo
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $valid_mimes = [
            'jpg'  => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png'  => ['image/png'],
            'webp' => ['image/webp'],
            'pdf'  => ['application/pdf', 'application/x-pdf']
        ];

        $allowed_mime_types = [];
        foreach ($allowed_exts as $ext) {
            if (isset($valid_mimes[$ext])) {
                $allowed_mime_types = array_merge($allowed_mime_types, $valid_mimes[$ext]);
            }
        }

        if (!in_array($mime, $allowed_mime_types)) {
            return [false, "Tipe MIME file ($mime) tidak sesuai dengan ekstensi file!"];
        }
    }

    // Jika gambar, pastikan getimagesize() tidak false
    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'webp'])) {
        if (@getimagesize($file['tmp_name']) === false) {
            return [false, "File yang diunggah sanitasinya tidak valid sebagai foto/gambar."];
        }
    }

    return [true, "OK"];
}
