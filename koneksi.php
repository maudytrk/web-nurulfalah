<?php
/**
 * File Konfigurasi Koneksi Database
 * Sistem Informasi Portal PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

// Konfigurasi Parameter Database
$host     = "localhost";
$user     = "root";
$pass     = ""; // Default XAMPP biasanya dikosongkan
$dbname   = "db_nurul_falah";
$charset  = "utf8mb4";

// Opsi Konfigurasi PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Menampilkan exception jika terjadi error SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mengembalikan data dalam bentuk Array Asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Menggunakan prepared statement asli (Keamanan SQL Injection)
];

// Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    // Membuat koneksi ke database
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error yang jelas
    die("Koneksi ke database gagal: " . $e->getMessage());
}

/**
 * Variabel $pdo sekarang siap digunakan di seluruh file PHP lain.
 * Contoh cara pemanggilan file ini di file lain:
 * require_once 'koneksi.php';
 */
?>