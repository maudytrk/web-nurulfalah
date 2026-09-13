<?php
/**
 * Halaman Manajemen Akun Admin / User
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Halaman Admin & Otorisasi Super Admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// Khusus super_admin yang berhak mengelola akun admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    $_SESSION['error'] = "Akses ditolak! Fitur Kelola Admin hanya dapat diakses oleh Super Admin.";
    header("Location: index.php");
    exit;
}

// 2. Panggil Koneksi Database
require_once '../koneksi.php';

// 3. Ambil Semua Data User / Admin dari Database
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY role ASC, created_at DESC");
    $daftar_admin = $stmt->fetchAll();
} catch (PDOException $e) {
    $daftar_admin = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Kelola Akun Admin - YPI Nurul Falah</title>

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
            <li class="nav-item">
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>FAQ PPDB</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Pengaturan</div>
            <li class="nav-item active">
                <a class="nav-link" href="kelola_admin.php">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Kelola Admin</span>
                </a>
            </li>
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
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Akun Admin</h1>
                        <button class="btn btn-success btn-icon-split shadow-sm" data-toggle="modal" data-target="#modalTambah">
                            <span class="icon text-white-50"><i class="fas fa-user-plus"></i></span>
                            <span class="text">Tambah Admin Baru</span>
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

                    <!-- DataTables Admin -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-islamic">Daftar Pengguna Panel Admin</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="5%" class="text-center">No</th>
                                            <th>Username</th>
                                            <th>Nama Lengkap</th>
                                            <th width="15%">Role Hak Akses</th>
                                            <th width="12%">Unit Akses</th>
                                            <th width="15%">Tanggal Dibuat</th>
                                            <th width="15%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($daftar_admin as $row): ?>
                                            <tr>
                                                <td class="text-center align-middle"><?= $no++; ?></td>
                                                <td class="align-middle font-weight-bold text-dark">
                                                    <?= htmlspecialchars($row['username']); ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?= htmlspecialchars($row['nama_lengkap']); ?>
                                                </td>
                                                <td class="align-middle">
                                                    <?php if ($row['role'] === 'super_admin'): ?>
                                                        <span class="badge badge-success p-2"><i class="fas fa-shield-alt mr-1"></i>Super Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-info p-2"><i class="fas fa-user-tag mr-1"></i>Admin Unit</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-secondary p-2"><?= htmlspecialchars($row['unit_akses'] === 'all' ? 'Semua Unit' : $row['unit_akses']); ?></span>
                                                </td>
                                                <td class="align-middle"><?= date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                                <td class="text-center align-middle">
                                                    <!-- Tombol Edit Modal -->
                                                    <button class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-username="<?= htmlspecialchars($row['username']); ?>"
                                                            data-nama="<?= htmlspecialchars($row['nama_lengkap']); ?>"
                                                            data-role="<?= htmlspecialchars($row['role']); ?>"
                                                            data-unit="<?= htmlspecialchars($row['unit_akses']); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    <!-- Tombol Hapus Modal -->
                                                    <?php if ((int)$row['id'] !== (int)$_SESSION['admin_id']): ?>
                                                        <button class="btn btn-danger btn-sm btn-hapus" 
                                                                data-id="<?= $row['id']; ?>"
                                                                data-username="<?= htmlspecialchars($row['username']); ?>">
                                                            <i class="fas fa-trash"></i> Hapus
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-secondary btn-sm" disabled title="Tidak dapat menghapus akun sendiri yang sedang aktif">
                                                            <i class="fas fa-lock"></i> Aktif
                                                        </button>
                                                    <?php endif; ?>
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

    <!-- MODAL TAMBAH ADMIN -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="admin_proses.php" method="POST">
                    <input type="hidden" name="action" value="tambah">
                    
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Tambah Akun Admin Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" placeholder="Contoh: admin_mi" required>
                                <small class="form-text text-muted">Username digunakan untuk login ke sistem.</small>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password..." required>
                                <small class="form-text text-muted">Password akan dienkripsi secara aman (Password Hash BCRYPT).</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Contoh: Ahmad Fauzi, S.Pd." required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Role Hak Akses <span class="text-danger">*</span></label>
                                <select name="role" class="form-control" required>
                                    <option value="admin_unit">Admin Unit</option>
                                    <option value="super_admin">Super Admin (Akses Penuh)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Unit Akses <span class="text-danger">*</span></label>
                                <select name="unit_akses" class="form-control" required>
                                    <option value="all">Semua Unit (Yayasan / General)</option>
                                    <option value="RA">RA (Raudhatul Athfal)</option>
                                    <option value="MI">MI (Madrasah Ibtidaiyah)</option>
                                    <option value="SMPI">SMPI (SMP Islam)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save mr-1"></i> Simpan Akun Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT ADMIN -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="admin_proses.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit mr-2"></i>Edit Data Akun Admin</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" id="edit_username" class="form-control" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Ganti Password <small class="text-muted">(Biarkan kosong jika tidak diubah)</small></label>
                                <input type="password" name="password" class="form-control" placeholder="Password baru...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Role Hak Akses <span class="text-danger">*</span></label>
                                <select name="role" id="edit_role" class="form-control" required>
                                    <option value="admin_unit">Admin Unit</option>
                                    <option value="super_admin">Super Admin (Akses Penuh)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Unit Akses <span class="text-danger">*</span></label>
                                <select name="unit_akses" id="edit_unit" class="form-control" required>
                                    <option value="all">Semua Unit (Yayasan / General)</option>
                                    <option value="RA">RA (Raudhatul Athfal)</option>
                                    <option value="MI">MI (Madrasah Ibtidaiyah)</option>
                                    <option value="SMPI">SMPI (SMP Islam)</option>
                                </select>
                            </div>
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

    <!-- MODAL HAPUS ADMIN -->
    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="admin_proses.php" method="POST">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" id="hapus_id">
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus Admin</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus akun admin <strong id="hapus_username"></strong>? Pengguna tersebut tidak akan bisa login kembali ke sistem.
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-user-minus mr-1"></i> Ya, Hapus Akun</button>
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
                const id       = $(this).data('id');
                const username = $(this).data('username');
                const nama     = $(this).data('nama');
                const role     = $(this).data('role');
                const unit     = $(this).data('unit');

                $('#edit_id').val(id);
                $('#edit_username').val(username);
                $('#edit_nama').val(nama);
                $('#edit_role').val(role);
                $('#edit_unit').val(unit);

                $('#modalEdit').modal('show');
            });

            // Populate Modal Hapus
            $('.btn-hapus').on('click', function() {
                const id       = $(this).data('id');
                const username = $(this).data('username');

                $('#hapus_id').val(id);
                $('#hapus_username').text(username);

                $('#modalHapus').modal('show');
            });
        });
    </script>
</body>
</html>
