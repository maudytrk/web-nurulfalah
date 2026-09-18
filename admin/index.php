<?php
/**
 * Dashboard Utama Administrator
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Halaman: Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 2. Panggil koneksi database (keluar 1 folder ke direktori utama)
require_once '../koneksi.php';

// 3. Ambil Statistik Data dari Database
try {
    // Total Pengumuman / Info KBM
    $stmt1 = $pdo->query("SELECT COUNT(*) FROM pengumuman");
    $total_pengumuman = $stmt1->fetchColumn();

    // Total Foto Galeri
    $stmt2 = $pdo->query("SELECT COUNT(*) FROM galeri");
    $total_galeri = $stmt2->fetchColumn();

    // Total Berkas PPDB (Formulir & Brosur)
    $stmt3 = $pdo->query("SELECT COUNT(*) FROM berkas_ppdb");
    $total_berkas = $stmt3->fetchColumn();

    // Total FAQ PPDB
    $stmt4 = $pdo->query("SELECT COUNT(*) FROM faq_ppdb");
    $total_faq = $stmt4->fetchColumn();

} catch (PDOException $e) {
    // Fallback nilai 0 jika tabel belum siap/error
    $total_pengumuman = $total_galeri = $total_berkas = $total_faq = 0;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard Admin - YPI Nurul Falah</title>

    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- SB Admin 2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .bg-islamic {
            background-color: #1B5E20 !important;
        }
        .border-left-islamic {
            border-left: 0.25rem solid #1B5E20 !important;
        }
        .text-islamic {
            color: #1B5E20 !important;
        }
    </style>
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- ================= SIDEBAR ================= -->
        <ul class="navbar-nav bg-islamic sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon">
                    <i class="fa-solid fa-mosque text-warning"></i>
                </div>
                <div class="sidebar-brand-text mx-2">Nurul Falah</div>
            </a>

            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Manajemen Konten</div>

            <!-- Nav Item - Pengumuman -->
            <li class="nav-item">
                <a class="nav-link" href="pengumuman.php">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Pengumuman & KBM</span>
                </a>
            </li>

            <!-- Nav Item - Kalender Akademik -->
            <li class="nav-item">
                <a class="nav-link" href="kalender.php">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Kalender Akademik</span>
                </a>
            </li>

            <!-- Nav Item - Galeri -->
            <li class="nav-item">
                <a class="nav-link" href="galeri.php">
                    <i class="fas fa-fw fa-images"></i>
                    <span>Galeri Foto</span>
                </a>
            </li>

            <!-- Nav Item - Berkas PPDB -->
            <li class="nav-item">
                <a class="nav-link" href="berkas_ppdb.php">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Berkas PPDB</span>
                </a>
            </li>

            <!-- Nav Item - Informasi PPDB -->
            <li class="nav-item">
                <a class="nav-link" href="ppdb_info.php">
                    <i class="fas fa-fw fa-info-circle"></i>
                    <span>Informasi PPDB</span>
                </a>
            </li>

            <!-- Nav Item - Profil Sekolah -->
            <li class="nav-item">
                <a class="nav-link" href="profil.php">
                    <i class="fas fa-fw fa-school"></i>
                    <span>Profil Sekolah</span>
                </a>
            </li>

            <!-- Nav Item - Kontak WhatsApp -->
            <li class="nav-item">
                <a class="nav-link" href="kontak_wa.php">
                    <i class="fab fa-fw fa-whatsapp"></i>
                    <span>Kontak WhatsApp</span>
                </a>
            </li>

            <!-- Nav Item - FAQ -->
            <li class="nav-item">
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>FAQ PPDB</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">Pengaturan</div>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
            <!-- Nav Item - Kelola Admin -->
            <li class="nav-item">
                <a class="nav-link" href="kelola_admin.php">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Kelola Admin</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Nav Item - Logout -->
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                    <i class="fas fa-fw fa-sign-out-alt text-danger"></i>
                    <span>Keluar (Logout)</span>
                </a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar Header -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars text-success"></i>
                    </button>

                    <!-- Topbar Title -->
                    <h1 class="h5 mb-0 text-gray-800 font-weight-bold d-none d-sm-inline-block">
                        Portal Admin YPI Nurul Falah
                    </h1>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small font-weight-bold">
                                    <?= htmlspecialchars($_SESSION['admin_name']); ?>
                                </span>
                                <img class="img-profile rounded-circle" src="https://startbootstrap.github.io/startbootstrap-sb-admin-2/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../index.php" target="_blank">
                                    <i class="fas fa-globe fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Lihat Website Utama
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?');">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-danger"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Dashboard Overview</h1>
                        <a href="../index.php" target="_blank" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                            <i class="fas fa-external-link-alt fa-sm text-white-50 mr-1"></i> Buka Website Publik
                        </a>
                    </div>

                    <!-- Content Row (Statistik Cards) -->
                    <div class="row">

                        <!-- Card: Pengumuman & KBM -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-islamic shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-islamic text-uppercase mb-1">
                                                Pengumuman & Info KBM</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pengumuman; ?> Data</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Galeri Foto -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Foto Galeri Sekolah</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_galeri; ?> Foto</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-images fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: Berkas PPDB -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Berkas PDF PPDB</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_berkas; ?> File</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-pdf fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card: FAQ PPDB -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Daftar FAQ PPDB</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_faq; ?> Pertanyaan</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-question-circle fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Content Row (Selamat Datang Box) -->
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-white">
                                    <h6 class="m-0 font-weight-bold text-islamic">
                                        Selamat Datang di System Control Panel
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <h5>Assalamu'alaikum, <strong><?= htmlspecialchars($_SESSION['admin_name']); ?></strong>!</h5>
                                    <p class="text-muted">
                                        Anda login sebagai <code><?= htmlspecialchars($_SESSION['role']); ?></code>. Melalui panel ini, Anda memiliki akses penuh untuk mengelola pengumuman KBM, galeri foto kegiatan, file unduhan brosur/formulir PPDB, serta pertanyaan FAQ untuk unit RA, MI, dan SMPI YPI Nurul Falah.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; 2026 YPI Nurul Falah. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

</body>
</html>