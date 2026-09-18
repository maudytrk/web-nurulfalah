<?php
/**
 * Skrip Penanganan Proses Unduh File PDF (Formulir & Brosur PPDB)
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

// Matikan display_errors pada mode production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Panggil file koneksi database (opsional jika ingin mencatat log unduhan di kemudian hari)
require_once 'koneksi.php';

// 1. Periksa apakah parameter 'file' tersedia di URL
if (!isset($_GET['file']) || empty(trim($_GET['file']))) {
    http_response_code(400);
    die("<h3 style='color:red; text-align:center; font-family:sans-serif; margin-top:50px;'>Error 400: Permintaan Tidak Valid. Nama file tidak ditentukan.</h3>");
}

// 2. Ambil dan bersihkan nama file dari parameter URL
$requested_file = trim($_GET['file']);

// 3. KEAMANAN: Cegah Directory Traversal Attack (misal: ../../etc/passwd)
// basename() memastikan hanya mengambil nama file saja tanpa path direktori
$safe_filename = basename($requested_file);

// 4. Tentukan folder lokasi berkas fisik disimpan di server
$upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$file_path  = $upload_dir . $safe_filename;

// Cek folder berkas jika tidak ditemukan di root uploads/
if (!file_exists($file_path)) {
    $file_path = $upload_dir . 'berkas' . DIRECTORY_SEPARATOR . $safe_filename;
}

// 5. Validasi: Periksa apakah file fisik benar-benar ada di folder uploads/
if (!file_exists($file_path)) {
    http_response_code(404);
    die("<h3 style='color:red; text-align:center; font-family:sans-serif; margin-top:50px;'>Error 404: File tidak ditemukan di server.<br><small style='color:#555;'>Pastikan file '{$safe_filename}' sudah diunggah ke folder 'uploads/'.</small></h3>");
}

// 6. Validasi: Pastikan berkas yang diunduh ber-ekstensi PDF
$file_extension = strtolower(pathinfo($safe_filename, PATHINFO_EXTENSION));
if ($file_extension !== 'pdf') {
    http_response_code(403);
    die("<h3 style='color:red; text-align:center; font-family:sans-serif; margin-top:50px;'>Error 403: Akses Ditolak. Hanya file berformat PDF yang diperbolehkan untuk diunduh.</h3>");
}

// 7. Bersihkan output buffer sebelum mengirim header file
if (ob_get_level()) {
    ob_end_clean();
}

// 8. Set Header HTTP untuk Pengunduhan Berkas PDF Secara Aman
header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $safe_filename . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// 9. Baca dan alirkan file ke browser user untuk diunduh
readfile($file_path);
exit;