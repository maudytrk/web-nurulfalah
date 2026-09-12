<?php
/**
 * Halaman Kelola Daftar Pertanyaan & Jawaban FAQ PPDB
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Halaman Admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 2. Panggil Koneksi Database
require_once '../koneksi.php';

// 3. Query Data FAQ
try {
    $stmt = $pdo->query("SELECT * FROM faq_ppdb ORDER BY urutan ASC, id ASC");
    $daftar_faq = $stmt->fetchAll();
} catch (PDOException $e) {
    $daftar_faq = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manajemen FAQ PPDB - YPI Nurul Falah</title>

    <!-- Font Awesome Icons & Google Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- SB Admin 2 CSS & DataTables -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css" rel="stylesheet">

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
            <li class="nav-item">
                <a class="nav-link" href="pengumuman.php">
                    <i class="fas fa-fw fa-bullhorn"></i>
                    <span>Pengumuman & KBM</span>
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
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>FAQ PPDB</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Pengaturan</div>
            <li class="nav-item">
                <a class="nav-link" href="../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                    <i class="fas fa-fw fa-sign-out-alt text-danger"></i>
                    <span>Keluar (Logout)</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
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

                <!-- Main Content -->
                <div class="container-fluid">

                    <!-- Page Heading & Tombol Tambah -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kelola FAQ PPDB</h1>
                        <button class="btn btn-success btn-icon-split shadow-sm" data-toggle="modal" data-target="#modalTambah">
                            <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                            <span class="text">Tambah Pertanyaan Baru</span>
                        </button>
                    </div>

                    <!-- Alert Notifikasi Session -->
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

                    <!-- DataTables FAQ -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-islamic">Daftar Pertanyaan & Jawaban FAQ</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="8%" class="text-center">Urutan</th>
                                            <th width="35%">Pertanyaan</th>
                                            <th>Jawaban</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($daftar_faq as $row): ?>
                                            <tr>
                                                <td class="text-center align-middle font-weight-bold"><?= (int)$row['urutan']; ?></td>
                                                <td class="align-middle font-weight-bold text-dark">
                                                    <?= htmlspecialchars($row['pertanyaan']); ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?= nl2br(htmlspecialchars($row['jawaban'])); ?>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <button class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-pertanyaan="<?= htmlspecialchars($row['pertanyaan']); ?>"
                                                            data-jawaban="<?= htmlspecialchars($row['jawaban']); ?>"
                                                            data-urutan="<?= (int)$row['urutan']; ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    <button class="btn btn-danger btn-sm btn-hapus" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-pertanyaan="<?= htmlspecialchars($row['pertanyaan']); ?>">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>&copy; 2026 YPI Nurul Falah. All Rights Reserved.</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- MODAL TAMBAH FAQ -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="faq_proses.php" method="POST">
                    <input type="hidden" name="action" value="tambah">
                    
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-question-circle mr-2"></i>Tambah Pertanyaan FAQ Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="pertanyaan" class="form-control" placeholder="Contoh: Kapan pendaftaran PPDB gelombang 1 dibuka?" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Jawaban <span class="text-danger">*</span></label>
                            <textarea name="jawaban" class="form-control" rows="4" placeholder="Tuliskan penjelasan jawaban secara lengkap..." required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Urutan Tampil</label>
                            <input type="number" name="urutan" class="form-control" value="1" min="1">
                            <small class="form-text text-muted">Angka kecil tampil paling atas.</small>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-save btn-success"><i class="fas fa-save mr-1"></i> Simpan FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT FAQ -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="faq_proses.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Data FAQ</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Pertanyaan <span class="text-danger">*</span></label>
                            <input type="text" name="pertanyaan" id="edit_pertanyaan" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Jawaban <span class="text-danger">*</span></label>
                            <textarea name="jawaban" id="edit_jawaban" class="form-control" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Urutan Tampil</label>
                            <input type="number" name="urutan" id="edit_urutan" class="form-control" min="1">
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white"><i class="fas fa-save mr-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL HAPUS FAQ -->
    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="faq_proses.php" method="POST">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" id="hapus_id">
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus FAQ</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus FAQ <strong id="hapus_pertanyaan"></strong>?
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Ya, Hapus FAQ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();

            // Populate Modal Edit
            $('.btn-edit').on('click', function() {
                const id         = $(this).data('id');
                const pertanyaan = $(this).data('pertanyaan');
                const jawaban    = $(this).data('jawaban');
                const urutan     = $(this).data('urutan');

                $('#edit_id').val(id);
                $('#edit_pertanyaan').val(pertanyaan);
                $('#edit_jawaban').val(jawaban);
                $('#edit_urutan').val(urutan);

                $('#modalEdit').modal('show');
            });

            // Populate Modal Hapus
            $('.btn-hapus').on('click', function() {
                const id         = $(this).data('id');
                const pertanyaan = $(this).data('pertanyaan');

                $('#hapus_id').val(id);
                $('#hapus_pertanyaan').text(pertanyaan);

                $('#modalHapus').modal('show');
            });
        });
    </script>
</body>
</html>