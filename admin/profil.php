<?php
/**
 * Halaman Pengelolaan Profil Sekolah (Sejarah, Visi, Misi, Fasilitas, & Profil Unit)
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 */

session_start();

require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

try {
    $stmt = $pdo->query("SELECT * FROM profil_sekolah WHERE id = 1");
    $profil = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$profil) {
        $profil = [
            'nama_yayasan' => 'YPI Nurul Falah',
            'sejarah' => '',
            'visi' => '',
            'misi' => '',
            'fasilitas' => '',
            'profil_ra' => '',
            'profil_mi' => '',
            'profil_smpi' => ''
        ];
    }
} catch (PDOException $e) {
    error_log("Fetch Profil Error: " . $e->getMessage());
    $profil = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kelola Profil Sekolah - YPI Nurul Falah</title>

    <!-- Font Awesome & Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .bg-islamic { background-color: #1B5E20 !important; }
        .text-islamic { color: #1B5E20 !important; }
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
            <li class="nav-item">
                <a class="nav-link" href="ppdb_info.php">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Informasi PPDB</span>
                </a>
            </li>
            <li class="nav-item active">
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
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola Informasi Profil Sekolah</h1>
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

                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-islamic">Formulir Informasi Utama Sekolah</h6>
                        </div>
                        <div class="card-body">
                            <form action="profil_proses.php" method="POST">
                                <?= csrf_field(); ?>

                                <div class="form-group">
                                    <label class="font-weight-bold">Nama Yayasan / Lembaga <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_yayasan" class="form-control" value="<?= htmlspecialchars($profil['nama_yayasan'] ?? 'YPI Nurul Falah'); ?>" required>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Sejarah Singkat Sekolah <span class="text-danger">*</span></label>
                                    <textarea name="sejarah" class="form-control" rows="4" required><?= htmlspecialchars($profil['sejarah'] ?? ''); ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Visi Sekolah <span class="text-danger">*</span></label>
                                        <textarea name="visi" class="form-control" rows="4" required><?= htmlspecialchars($profil['visi'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Misi Sekolah <span class="text-danger">*</span> <small class="text-muted">(Gunakan baris baru untuk tiap poin)</small></label>
                                        <textarea name="misi" class="form-control" rows="4" required><?= htmlspecialchars($profil['misi'] ?? ''); ?></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Fasilitas Utama Sekolah <small class="text-muted">(Gunakan baris baru untuk tiap fasilitas)</small></label>
                                    <textarea name="fasilitas" class="form-control" rows="4"><?= htmlspecialchars($profil['fasilitas'] ?? ''); ?></textarea>
                                </div>

                                <hr class="my-4">
                                <h5 class="font-weight-bold text-islamic mb-3"><i class="fas fa-layer-group mr-2"></i>Deskripsi Profil Per Unit</h5>

                                <div class="form-group">
                                    <label class="font-weight-bold">Deskripsi Profil Unit RA (Raudhatul Athfal)</label>
                                    <textarea name="profil_ra" class="form-control" rows="3"><?= htmlspecialchars($profil['profil_ra'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Deskripsi Profil Unit MI (Madrasah Ibtidaiyah)</label>
                                    <textarea name="profil_mi" class="form-control" rows="3"><?= htmlspecialchars($profil['profil_mi'] ?? ''); ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold">Deskripsi Profil Unit SMPI (SMP Islam)</label>
                                    <textarea name="profil_smpi" class="form-control" rows="3"><?= htmlspecialchars($profil['profil_smpi'] ?? ''); ?></textarea>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm"><i class="fas fa-save mr-2"></i>Simpan Profil Sekolah</button>
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
