<?php
/**
 * Halaman Form Input Pengumuman Baru
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Halaman Admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once '../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tambah Pengumuman Baru - YPI Nurul Falah</title>

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

    <!-- Page Wrapper -->
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
            <li class="nav-item active">
                <a class="nav-link" href="pengumuman.php">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Pengumuman & KBM</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-images"></i>
                    <span>Galeri Foto</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-file-pdf"></i>
                    <span>Berkas PPDB</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" onclick="return confirm('Yakin ingin keluar?');">
                    <i class="fas fa-fw fa-sign-out-alt text-danger"></i>
                    <span>Keluar (Logout)</span>
                </a>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
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

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Form Tambah Pengumuman</h1>
                        <a href="pengumuman.php" class="btn btn-secondary btn-icon-split shadow-sm">
                            <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
                            <span class="text">Kembali ke Daftar</span>
                        </a>
                    </div>

                    <!-- Alert Session jika redirect dari backend gagal -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle mr-1"></i> <?= $_SESSION['error']; ?>
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Form Input Card Mengarah ke pengumuman_proses.php -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-islamic">Isi Detail Pengumuman Baru</h6>
                        </div>
                        <div class="card-body">
                            <form action="pengumuman_proses.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="tambah">
                                
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Judul Pengumuman <span class="text-danger">*</span></label>
                                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Jadwal Pelaksanaan Ujian Tengah Semester KBM 2026" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Tanggal Publikasi <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                                    </div>
                                     <div class="col-md-6 form-group">
                                        <label class="font-weight-bold text-dark">Target Unit / Akses <span class="text-danger">*</span></label>
                                        <select name="target_unit" class="form-control" required>
                                            <option value="">-- Pilih Target Unit --</option>
                                            <option value="Yayasan">Yayasan / Semua Unit</option>
                                            <option value="RA">RA (Raudhatul Athfal)</option>
                                            <option value="MI">MI (Madrasah Ibtidaiyah)</option>
                                            <option value="SMPI">SMPI (SMP Islam)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Isi Teks Pengumuman <span class="text-danger">*</span></label>
                                    <textarea name="isi_pengumuman" class="form-control" rows="6" placeholder="Tuliskan detail informasi pengumuman secara rinci..." required></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">File Lampiran (PDF / Gambar / Doc) <small class="text-muted">(Opsional)</small></label>
                                    <input type="file" name="file_lampiran" class="form-control-file border p-2 rounded" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                    <small class="form-text text-muted">Format yang didukung: PDF, JPG, PNG, DOCX. Maksimal 5 MB.</small>
                                </div>

                                <hr class="mt-4">

                                <div class="d-flex justify-content-end">
                                    <a href="pengumuman.php" class="btn btn-secondary mr-2">Batal</a>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane mr-1"></i> Publikasikan Pengumuman</button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; 2026 Maudy Tri Kusuma - YPI Nurul Falah</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

</body>
</html>