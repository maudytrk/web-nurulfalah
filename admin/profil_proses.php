<?php
/**
 * Backend Engine Update Profil Sekolah
 * YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profil.php");
    exit;
}

verify_csrf_token();

$nama_yayasan = trim($_POST['nama_yayasan'] ?? 'YPI Nurul Falah');
$sejarah      = trim($_POST['sejarah'] ?? '');
$visi         = trim($_POST['visi'] ?? '');
$misi         = trim($_POST['misi'] ?? '');
$fasilitas    = trim($_POST['fasilitas'] ?? '');
$profil_ra    = trim($_POST['profil_ra'] ?? '');
$profil_mi    = trim($_POST['profil_mi'] ?? '');
$profil_smpi  = trim($_POST['profil_smpi'] ?? '');

if (empty($nama_yayasan) || empty($sejarah) || empty($visi) || empty($misi)) {
    $_SESSION['error'] = "Nama Yayasan, Sejarah, Visi, dan Misi wajib diisi!";
    header("Location: profil.php");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO profil_sekolah (id, nama_yayasan, sejarah, visi, misi, fasilitas, profil_ra, profil_mi, profil_smpi)
                           VALUES (1, :nama_yayasan, :sejarah, :visi, :misi, :fasilitas, :profil_ra, :profil_mi, :profil_smpi)
                           ON DUPLICATE KEY UPDATE
                           nama_yayasan = :nama_yayasan2,
                           sejarah = :sejarah2,
                           visi = :visi2,
                           misi = :misi2,
                           fasilitas = :fasilitas2,
                           profil_ra = :profil_ra2,
                           profil_mi = :profil_mi2,
                           profil_smpi = :profil_smpi2");

    $stmt->execute([
        ':nama_yayasan'  => $nama_yayasan,
        ':sejarah'       => $sejarah,
        ':visi'          => $visi,
        ':misi'          => $misi,
        ':fasilitas'     => $fasilitas,
        ':profil_ra'     => $profil_ra,
        ':profil_mi'     => $profil_mi,
        ':profil_smpi'   => $profil_smpi,
        ':nama_yayasan2' => $nama_yayasan,
        ':sejarah2'      => $sejarah,
        ':visi2'         => $visi,
        ':misi2'         => $misi,
        ':fasilitas2'    => $fasilitas,
        ':profil_ra2'    => $profil_ra,
        ':profil_mi2'    => $profil_mi,
        ':profil_smpi2'  => $profil_smpi
    ]);

    $_SESSION['success'] = "Data Profil Sekolah berhasil disimpan!";
} catch (PDOException $e) {
    error_log("Update Profil Error: " . $e->getMessage());
    $_SESSION['error'] = "Terjadi kesalahan saat menyimpan profil sekolah.";
}

header("Location: profil.php");
exit;
