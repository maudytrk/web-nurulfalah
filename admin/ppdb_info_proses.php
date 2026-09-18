<?php
/**
 * Backend Engine Update Informasi PPDB Dinamis per Unit
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ppdb_info.php");
    exit;
}

verify_csrf_token();

$unit              = trim($_POST['unit'] ?? '');
$tahun_ajaran      = trim($_POST['tahun_ajaran'] ?? '2026/2027');
$gelombang         = trim($_POST['gelombang'] ?? '');
$jam_kbm           = trim($_POST['jam_kbm'] ?? '');
$biaya_pendaftaran = trim($_POST['biaya_pendaftaran'] ?? '');
$persyaratan       = trim($_POST['persyaratan'] ?? '');
$keunggulan        = trim($_POST['keunggulan'] ?? '');
$seragam           = trim($_POST['seragam'] ?? '');
$rincian_biaya     = trim($_POST['rincian_biaya'] ?? '');

enforce_unit_access($unit, 'ppdb_info.php');

if (!in_array($unit, ['RA', 'MI', 'SMPI']) || empty($persyaratan) || empty($jam_kbm)) {
    $_SESSION['error'] = "Data unit tidak valid atau kolom wajib masih kosong!";
    header("Location: ppdb_info.php?unit=" . urlencode($unit));
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO ppdb_info (unit, tahun_ajaran, gelombang, jam_kbm, biaya_pendaftaran, persyaratan, keunggulan, seragam, rincian_biaya)
                           VALUES (:unit, :tahun_ajaran, :gelombang, :jam_kbm, :biaya_pendaftaran, :persyaratan, :keunggulan, :seragam, :rincian_biaya)
                           ON DUPLICATE KEY UPDATE
                           tahun_ajaran = :tahun_ajaran2,
                           gelombang = :gelombang2,
                           jam_kbm = :jam_kbm2,
                           biaya_pendaftaran = :biaya_pendaftaran2,
                           persyaratan = :persyaratan2,
                           keunggulan = :keunggulan2,
                           seragam = :seragam2,
                           rincian_biaya = :rincian_biaya2");
                           
    $stmt->execute([
        ':unit'               => $unit,
        ':tahun_ajaran'       => $tahun_ajaran,
        ':gelombang'          => $gelombang,
        ':jam_kbm'            => $jam_kbm,
        ':biaya_pendaftaran'  => $biaya_pendaftaran,
        ':persyaratan'        => $persyaratan,
        ':keunggulan'         => $keunggulan,
        ':seragam'            => $seragam,
        ':rincian_biaya'      => $rincian_biaya,
        ':tahun_ajaran2'      => $tahun_ajaran,
        ':gelombang2'         => $gelombang,
        ':jam_kbm2'           => $jam_kbm,
        ':biaya_pendaftaran2' => $biaya_pendaftaran,
        ':persyaratan2'       => $persyaratan,
        ':keunggulan2'        => $keunggulan,
        ':seragam2'           => $seragam,
        ':rincian_biaya2'     => $rincian_biaya
    ]);

    $_SESSION['success'] = "Informasi PPDB unit <strong>$unit</strong> berhasil diperbarui!";
} catch (PDOException $e) {
    error_log("Update PPDB Info Error: " . $e->getMessage());
    $_SESSION['error'] = "Terjadi kesalahan saat memperbarui informasi PPDB.";
}

header("Location: ppdb_info.php?unit=" . urlencode($unit));
exit;
