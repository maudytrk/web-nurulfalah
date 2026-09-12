<?php
/**
 * Halaman Manajemen Berkas PPDB (Formulir & Brosur PDF per Unit)
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

// 3. Ambil Filter Unit jika ada
$unit_filter = isset($_GET['unit']) ? trim($_GET['unit']) : 'all';

// 4. Query Data Berkas PPDB
try {
    if ($unit_filter !== 'all' && in_array($unit_filter, ['ra', 'mi', 'smpi'])) {
        $stmt = $pdo->prepare("SELECT * FROM berkas_ppdb WHERE unit_akses = :unit ORDER BY created_at DESC");
        $stmt->execute([':unit' => $unit_filter]);
    } else {
        $stmt = $pdo->query("SELECT * FROM berkas_ppdb ORDER BY created_at DESC");
    }
    $daftar_berkas = $stmt->fetchAll();
} catch (PDOException $e) {
    $daftar_berkas = [];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Manajemen Berkas PPDB - YPI Nurul Falah</title>

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
            <li class="nav-item active">
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
                        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Manajemen Berkas PPDB</h1>
                        <button class="btn btn-success btn-icon-split shadow-sm" data-toggle="modal" data-target="#modalTambah">
                            <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
                            <span class="text">Unggah Berkas Baru</span>
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

                    <!-- Filter Unit -->
                    <div class="card shadow mb-4">
                        <div class="card-body py-3">
                            <div class="form-inline align-items-center">
                                <label class="font-weight-bold mr-3 text-dark"><i class="fas fa-filter text-success mr-1"></i> Filter Unit:</label>
                                <a href="berkas_ppdb.php?unit=all" class="btn btn-sm <?= $unit_filter === 'all' ? 'btn-success' : 'btn-outline-success'; ?> mr-2">Semua Unit</a>
                                <a href="berkas_ppdb.php?unit=ra" class="btn btn-sm <?= $unit_filter === 'ra' ? 'btn-success' : 'btn-outline-success'; ?> mr-2">RA</a>
                                <a href="berkas_ppdb.php?unit=mi" class="btn btn-sm <?= $unit_filter === 'mi' ? 'btn-success' : 'btn-outline-success'; ?> mr-2">MI</a>
                                <a href="berkas_ppdb.php?unit=smpi" class="btn btn-sm <?= $unit_filter === 'smpi' ? 'btn-success' : 'btn-outline-success'; ?>">SMPI</a>
                            </div>
                        </div>
                    </div>

                    <!-- DataTables Berkas -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-islamic">Daftar Formulir & Brosur PPDB</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr class="bg-light">
                                            <th width="5%" class="text-center">No</th>
                                            <th>Nama Berkas / Dokumen</th>
                                            <th width="12%">Kategori</th>
                                            <th width="10%">Target Unit</th>
                                            <th width="12%" class="text-center">File PDF</th>
                                            <th width="15%">Tanggal Unggah</th>
                                            <th width="13%" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; foreach ($daftar_berkas as $row): ?>
                                            <tr>
                                                <td class="text-center align-middle"><?= $no++; ?></td>
                                                <td class="align-middle font-weight-bold text-dark">
                                                    <?= htmlspecialchars($row['nama_berkas']); ?>
                                                    <?php if (!empty($row['keterangan'])): ?>
                                                        <small class="d-block text-muted font-weight-normal mt-1"><?= htmlspecialchars(substr($row['keterangan'], 0, 80)) . (strlen($row['keterangan']) > 80 ? '...' : ''); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-secondary p-2"><?= strtoupper(htmlspecialchars($row['kategori'] ?? 'formulir')); ?></span>
                                                </td>
                                                <td class="align-middle">
                                                    <span class="badge badge-info p-2"><?= strtoupper(htmlspecialchars($row['unit_akses'])); ?></span>
                                                </td>
                                                <td class="text-center align-middle">
                                                    <?php 
                                                        $pdf_path = "../uploads/berkas/" . htmlspecialchars($row['file_pdf']);
                                                        if (!empty($row['file_pdf']) && file_exists($pdf_path)): 
                                                    ?>
                                                        <a href="<?= $pdf_path; ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-file-pdf mr-1"></i> Unduh PDF
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted font-italic" style="font-size: 0.8rem;">File Tidak Ada</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="align-middle"><?= date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                                <td class="text-center align-middle">
                                                    <!-- Tombol Edit Modal -->
                                                    <button class="btn btn-warning btn-sm btn-edit" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-nama="<?= htmlspecialchars($row['nama_berkas']); ?>"
                                                            data-kategori="<?= htmlspecialchars($row['kategori'] ?? 'formulir'); ?>"
                                                            data-unit="<?= htmlspecialchars($row['unit_akses']); ?>"
                                                            data-keterangan="<?= htmlspecialchars($row['keterangan'] ?? ''); ?>"
                                                            data-file="<?= htmlspecialchars($row['file_pdf']); ?>">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    
                                                    <!-- Tombol Hapus Modal -->
                                                    <button class="btn btn-danger btn-sm btn-hapus" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-nama="<?= htmlspecialchars($row['nama_berkas']); ?>">
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

    <!-- MODAL TAMBAH BERKAS -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="berkas_proses.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="tambah">
                    
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-file-upload mr-2"></i>Unggah Berkas PPDB Baru</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Berkas / Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="nama_berkas" class="form-control" placeholder="Contoh: Formulir Pendaftaran RA 2026/2027" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Kategori Dokumen <span class="text-danger">*</span></label>
                                <select name="kategori" class="form-control" required>
                                    <option value="formulir">Formulir Pendaftaran</option>
                                    <option value="brosur">Brosur PPDB</option>
                                    <option value="panduan">Panduan / Syarat PPDB</option>
                                    <option value="rincian_biaya">Rincian Biaya</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Target Unit <span class="text-danger">*</span></label>
                                <select name="unit_akses" class="form-control" required>
                                    <option value="all">Semua Unit (Umum)</option>
                                    <option value="ra">RA (Raudhatul Athfal)</option>
                                    <option value="mi">MI (Madrasah Ibtidaiyah)</option>
                                    <option value="smpi">SMPI (SMP Islam)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Keterangan / Deskripsi Singkat</label>
                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Tuliskan petunjuk atau keterangan singkat terkait penggunaan dokumen ini..."></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Pilih Berkas PDF <span class="text-danger">*</span></label>
                            <input type="file" name="file_pdf" class="form-control-file border p-2 rounded" accept=".pdf" required>
                            <small class="form-text text-muted">Format berkas khusus PDF. Maksimal ukuran file 10 MB.</small>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-upload mr-1"></i> Unggah Berkas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT BERKAS -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="berkas_proses.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-2"></i>Edit Data Berkas PPDB</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">Nama Berkas / Dokumen <span class="text-danger">*</span></label>
                            <input type="text" name="nama_berkas" id="edit_nama" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Kategori Dokumen <span class="text-danger">*</span></label>
                                <select name="kategori" id="edit_kategori" class="form-control" required>
                                    <option value="formulir">Formulir Pendaftaran</option>
                                    <option value="brosur">Brosur PPDB</option>
                                    <option value="panduan">Panduan / Syarat PPDB</option>
                                    <option value="rincian_biaya">Rincian Biaya</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Target Unit <span class="text-danger">*</span></label>
                                <select name="unit_akses" id="edit_unit" class="form-control" required>
                                    <option value="all">Semua Unit (Umum)</option>
                                    <option value="ra">RA (Raudhatul Athfal)</option>
                                    <option value="mi">MI (Madrasah Ibtidaiyah)</option>
                                    <option value="smpi">SMPI (SMP Islam)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Keterangan / Deskripsi Singkat</label>
                            <textarea name="keterangan" id="edit_keterangan" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Ganti Berkas PDF <small class="text-muted">(Biarkan kosong jika tidak diganti)</small></label>
                            <input type="file" name="file_pdf" class="form-control-file border p-2 rounded" accept=".pdf">
                            <small id="info_pdf_lama" class="form-text text-info mt-1"></small>
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

    <!-- MODAL HAPUS BERKAS -->
    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="berkas_proses.php" method="POST">
                    <input type="hidden" name="action" value="hapus">
                    <input type="hidden" name="id" id="hapus_id">
                    
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i>Konfirmasi Hapus Berkas</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    
                    <div class="modal-body">
                        Apakah Anda yakin ingin menghapus berkas <strong id="hapus_nama"></strong>? File PDF fisik pada folder penyimpanan juga akan dihapus permanen.
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash mr-1"></i> Ya, Hapus Berkas</button>
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
                const nama       = $(this).data('nama');
                const kategori   = $(this).data('kategori');
                const unit       = $(this).data('unit');
                const keterangan = $(this).data('keterangan');
                const file       = $(this).data('file');

                $('#edit_id').val(id);
                $('#edit_nama').val(nama);
                $('#edit_kategori').val(kategori);
                $('#edit_unit').val(unit);
                $('#edit_keterangan').val(keterangan);

                if (file !== '') {
                    $('#info_pdf_lama').text('File PDF saat ini: ' + file);
                } else {
                    $('#info_pdf_lama').text('File PDF saat ini: (Tidak ada)');
                }

                $('#modalEdit').modal('show');
            });

            // Populate Modal Hapus
            $('.btn-hapus').on('click', function() {
                const id   = $(this).data('id');
                const nama = $(this).data('nama');

                $('#hapus_id').val(id);
                $('#hapus_nama').text(nama);

                $('#modalHapus').modal('show');
            });
        });
    </script>
</body>
</html>