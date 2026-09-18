<?php
/**
 * Halaman Pengelolaan Informasi PPDB Dinamis per Unit
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 */

session_start();

require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

// Ambil data PPDB untuk RA, MI, SMPI
try {
    $stmt = $pdo->query("SELECT * FROM ppdb_info ORDER BY FIELD(unit, 'RA', 'MI', 'SMPI')");
    $ppdb_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $ppdb_data = [];
    foreach ($ppdb_list as $item) {
        $ppdb_data[$item['unit']] = $item;
    }
} catch (PDOException $e) {
    error_log("Fetch PPDB Info Error: " . $e->getMessage());
    $ppdb_data = [];
}

$active_tab = $_GET['unit'] ?? (get_user_unit_access() !== 'all' ? get_user_unit_access() : 'RA');
if (!in_array($active_tab, ['RA', 'MI', 'SMPI'])) {
    $active_tab = 'RA';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kelola Informasi PPDB - YPI Nurul Falah</title>

    <!-- Font Awesome & Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .bg-islamic { background-color: #1B5E20 !important; }
        .text-islamic { color: #1B5E20 !important; }
        .nav-tabs .nav-link.active {
            color: #1B5E20 !important;
            font-weight: bold;
            border-bottom: 3px solid #1B5E20 !important;
        }
    </style>
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-islamic sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon"><i class="fa-solid fa-mosque text-warning"></i></div>
                <div class="sidebar-brand-text mx-2">Nurul Falah</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Manajemen Konten</div>
            <li class="nav-item">
                <a class="nav-link" href="pengumuman.php">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Pengumuman & KBM</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kalender.php">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Kalender Akademik</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="galeri.php">
                    <i class="fas fa-fw fa-images"></i>
                    <span>Galeri Foto</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="berkas_ppdb.php">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Berkas PPDB</span>
                </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="ppdb_info.php">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Informasi PPDB</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profil.php">
                    <i class="fas fa-fw fa-school"></i>
                    <span>Profil Sekolah</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="kontak_wa.php">
                    <i class="fab fa-fw fa-whatsapp"></i>
                    <span>Kontak WhatsApp</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>FAQ PPDB</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Pengaturan</div>
            <?php if (is_super_admin()): ?>
            <li class="nav-item">
                <a class="nav-link" href="kelola_admin.php">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Kelola Admin</span>
                </a>
            </li>
            <?php endif; ?>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                    <i class="fas fa-fw fa-sign-out-alt text-danger"></i>
                    <span>Keluar (Logout)</span>
                </a>
            </li>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">

                <!-- Topbar Header -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars text-success"></i>
                    </button>
                    <h1 class="h5 mb-0 text-gray-800 font-weight-bold d-none d-sm-inline-block">
                        Portal Admin YPI Nurul Falah
                    </h1>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                                    <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>
                                </span>
                                <img class="img-profile rounded-circle" src="https://startbootstrap.github.io/startbootstrap-sb-admin-2/img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Pengelolaan Informasi PPDB Dinamis</h1>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-1"></i> <?= $_SESSION['success']; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-1"></i> <?= $_SESSION['error']; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Tab Unit -->
                    <ul class="nav nav-tabs mb-4" id="unitTab" role="tablist">
                        <?php 
                        $units = ['RA' => 'RA (Raudhatul Athfal)', 'MI' => 'MI (Madrasah Ibtidaiyah)', 'SMPI' => 'SMPI (SMP Islam)'];
                        foreach ($units as $u_code => $u_name):
                            if (!can_access_unit($u_code)) continue;
                        ?>
                            <li class="nav-item">
                                <a class="nav-link <?= ($active_tab === $u_code) ? 'active' : ''; ?>" href="?unit=<?= $u_code; ?>">
                                    <i class="fas fa-school mr-1"></i> <?= $u_name; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php 
                    $curr_data = $ppdb_data[$active_tab] ?? [
                        'unit' => $active_tab,
                        'tahun_ajaran' => '2026/2027',
                        'gelombang' => 'Gelombang 1',
                        'persyaratan' => '',
                        'jam_kbm' => '',
                        'keunggulan' => '',
                        'seragam' => '',
                        'biaya_pendaftaran' => '',
                        'rincian_biaya' => ''
                    ];
                    ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-islamic">Informasi PPDB Unit <?= $active_tab; ?></h6>
                            <span class="badge badge-success px-3 py-2">Tahun Ajaran <?= htmlspecialchars($curr_data['tahun_ajaran']); ?></span>
                        </div>
                        <div class="card-body">
                            <form action="ppdb_info_proses.php" method="POST">
                                <?= csrf_field(); ?>
                                <input type="hidden" name="unit" value="<?= htmlspecialchars($active_tab); ?>">

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Tahun Ajaran <span class="text-danger">*</span></label>
                                        <input type="text" name="tahun_ajaran" class="form-control" value="<?= htmlspecialchars($curr_data['tahun_ajaran']); ?>" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Gelombang & Tanggal Pendaftaran <span class="text-danger">*</span></label>
                                        <input type="text" name="gelombang" class="form-control" value="<?= htmlspecialchars($curr_data['gelombang']); ?>" placeholder="Contoh: Gelombang 1 (Januari - Mei 2026)" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Jam Operasional KBM <span class="text-danger">*</span></label>
                                        <input type="text" name="jam_kbm" class="form-control" value="<?= htmlspecialchars($curr_data['jam_kbm']); ?>" placeholder="Contoh: 07.00 - 12.30 WIB (Senin - Sabtu)" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Biaya Pendaftaran / Form <span class="text-danger">*</span></label>
                                        <input type="text" name="biaya_pendaftaran" class="form-control" value="<?= htmlspecialchars($curr_data['biaya_pendaftaran']); ?>" placeholder="Contoh: Rp 150.000" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Persyaratan Pendaftaran <span class="text-danger">*</span> <small class="text-muted">(Gunakan baris baru untuk setiap poin)</small></label>
                                    <textarea name="persyaratan" class="form-control" rows="5" required><?= htmlspecialchars($curr_data['persyaratan']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Keunggulan Unit Sekolah <small class="text-muted">(Gunakan baris baru untuk setiap poin)</small></label>
                                    <textarea name="keunggulan" class="form-control" rows="4"><?= htmlspecialchars($curr_data['keunggulan']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Ketentuan Seragam Sekolah <small class="text-muted">(Gunakan baris baru untuk setiap hari)</small></label>
                                    <textarea name="seragam" class="form-control" rows="4"><?= htmlspecialchars($curr_data['seragam']); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Rincian Biaya & Diskon/Potongan <small class="text-muted">(Opsional)</small></label>
                                    <textarea name="rincian_biaya" class="form-control" rows="4" placeholder="Tuliskan keterangan infaq/SPP/diskon pendaftaran awal..."><?= htmlspecialchars($curr_data['rincian_biaya']); ?></textarea>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm"><i class="fas fa-save mr-2"></i>Simpan Informasi PPDB</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>

            </div>

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; 2026 YPI Nurul Falah. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

</body>
</html>
