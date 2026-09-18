<?php
/**
 * Halaman Tabel Data Pengumuman & KBM Admin
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Halaman Admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 2. Panggil Koneksi Database & Helpers
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();

// 3. Ambil Data Pengumuman dari Database (Sesuai Role Akses)
try {
    if (is_super_admin() || get_user_unit_access() === 'all') {
        $stmt = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal_post DESC, id DESC");
    } else {
        $user_unit = get_user_unit_access();
        $stmt = $pdo->prepare("SELECT * FROM pengumuman WHERE target_unit = :unit OR target_unit = 'Yayasan' ORDER BY tanggal_post DESC, id DESC");
        $stmt->execute([':unit' => $user_unit]);
    }
    $daftar_pengumuman = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error Fetch Pengumuman: " . $e->getMessage());
    $daftar_pengumuman = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manajemen Pengumuman - YPI Nurul Falah</title>

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
            <li class="nav-item active">
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

            <!-- Nav Item - FAQ & Kontak -->
            <li class="nav-item">
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>FAQ PPDB</span>
                </a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Pengaturan</div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'super_admin'): ?>
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

                    <!-- Page Heading & Link ke Form Tambah -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Pengumuman & KBM</h1>
                        <a href="pengumuman_tambah.php" class="btn btn-success btn-icon-split shadow-sm">
                            <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                            <span class="text">Tambah Pengumuman</span>
                        </a>
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

                    <!-- DataTables Pengumuman -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-islamic">Daftar Pengumuman Terpublikasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="5%">No</th>
                                            <th>Judul Pengumuman</th>
                                            <th width="10%">Target Unit</th>
                                            <th>Isi Pengumuman</th>
                                            <th width="12%">Lampiran</th>
                                            <th width="13%">Tanggal Terbit</th>
                                            <th width="13%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($daftar_pengumuman as $row): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td class="font-weight-bold text-dark"><?= htmlspecialchars($row['judul']); ?></td>
                                                <td>
                                                    <span class="badge badge-info p-2"><?= htmlspecialchars($row['target_unit']); ?></span>
                                                </td>
                                                <td><?= nl2br(htmlspecialchars(substr($row['isi_pengumuman'], 0, 90))) . (strlen($row['isi_pengumuman']) > 90 ? '...' : ''); ?></td>
                                                <td class="text-center">
                                                    <?php if (!empty($row['file_lampiran'])): ?>
                                                        <a href="../uploads/pengumuman/<?= htmlspecialchars($row['file_lampiran']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-file-download mr-1"></i> Lihat File
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted font-italic" style="font-size: 0.85rem;">Tidak ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d M Y', strtotime($row['tanggal_post'])); ?></td>
                                                <td class="text-center">
                                                    <!-- Tombol Edit Modal -->
                                                    <button class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-judul="<?= htmlspecialchars($row['judul']); ?>"
                                                            data-unit="<?= htmlspecialchars($row['target_unit']); ?>"
                                                            data-tanggal="<?= date('Y-m-d', strtotime($row['tanggal_post'])); ?>"
                                                            data-isi="<?= htmlspecialchars($row['isi_pengumuman']); ?>"
                                                            data-file="<?= htmlspecialchars($row['file_lampiran'] ?? ''); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    <!-- Tombol Hapus Modal -->
                                                    <button class="btn btn-danger btn-sm btn-hapus" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-judul="<?= htmlspecialchars($row['judul']); ?>">
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

    <!-- MODAL EDIT DATA -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="pengumuman_proses.php" method="POST" enctype="multipart/form-data">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Data Pengumuman</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Judul Pengumuman</label>
                            <input type="text" name="judul" id="edit_judul" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Tanggal Publikasi</label>
                                <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Target Unit / Akses</label>
                                <select name="target_unit" id="edit_unit" class="form-control" required>
                                    <option value="Yayasan">Yayasan / Semua Unit</option>
                                    <option value="RA">RA (Raudhatul Athfal)</option>
                                    <option value="MI">MI (Madrasah Ibtidaiyah)</option>
                                    <option value="SMPI">SMPI (SMP Islam)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Isi Pengumuman</label>
                            <textarea name="isi_pengumuman" id="edit_isi" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Ganti File Lampiran <small class="text-muted">(Biarkan kosong jika tidak diganti)</small></label>
                            <input type="file" name="file_lampiran" class="form-control-file border p-2 rounded" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small id="info_file_lama" class="form-text text-info mt-1"></small>
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

    <!-- MODAL HAPUS DATA -->
    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="pengumuman_proses.php" method="POST">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" id="hapus_id">
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus pengumuman <strong id="hapus_judul"></strong>? Berkas fisik yang terlampir juga akan terhapus secara permanen.
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Ya, Hapus Data</button>
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

            // Populate & Trigger Modal Edit
            $('.btn-edit').on('click', function() {
                const id      = $(this).data('id');
                const judul   = $(this).data('judul');
                const unit    = $(this).data('unit');
                const tanggal = $(this).data('tanggal');
                const isi     = $(this).data('isi');
                const file    = $(this).data('file');

                $('#edit_id').val(id);
                $('#edit_judul').val(judul);
                $('#edit_unit').val(unit);
                $('#edit_tanggal').val(tanggal);
                $('#edit_isi').val(isi);

                if (file !== '') {
                    $('#info_file_lama').text('File saat ini: ' + file);
                } else {
                    $('#info_file_lama').text('File saat ini: (Tidak ada lampiran)');
                }

                $('#modalEdit').modal('show');
            });

            // Populate & Trigger Modal Hapus
            $('.btn-hapus').on('click', function() {
                const id    = $(this).data('id');
                const judul = $(this).data('judul');

                $('#hapus_id').val(id);
                $('#hapus_judul').text(judul);

                $('#modalHapus').modal('show');
            });
        });
    </script>
</body>
</html>