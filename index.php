<?php
/**
 * Halaman Depan Publik (Landing Page)
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */


// Panggil file koneksi database
require_once 'koneksi.php';

// 1. Ambil 6 Pengumuman Terbaru
try {
    $stmt_pengumuman = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal_post DESC LIMIT 6");
    $daftar_pengumuman = $stmt_pengumuman->fetchAll();
} catch (PDOException $e) {
    $daftar_pengumuman = [];
}

// 2. Ambil Galeri Ekskul Terbaru
try {
    $stmt_galeri = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_unggah DESC LIMIT 8");
    $daftar_galeri = $stmt_galeri->fetchAll();
} catch (PDOException $e) {
    $daftar_galeri = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YPI Nurul Falah - Portal Informasi PPDB & KBM</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        /* Color Palette Hijau Islami */
        :root {
            --primary-green: #1B5E20;
            --secondary-green: #2E7D32;
            --accent-gold: #FFB300;
            --light-green: #E8F5E9;
        }

        .bg-islamic-green {
            background-color: var(--primary-green) !important;
        }

        .text-islamic-green {
            color: var(--primary-green) !important;
        }

        .btn-islamic {
            background-color: var(--primary-green);
            color: white;
            font-weight: 600;
            border: none;
        }

        .btn-islamic:hover {
            background-color: var(--secondary-green);
            color: white;
        }

        .btn-accent {
            background-color: var(--accent-gold);
            color: #333;
            font-weight: 600;
            border: none;
        }

        .btn-accent:hover {
            background-color: #FFA000;
            color: #333;
        }

        /* Hero Banner Slider */
        .hero-carousel .carousel-item {
            height: 480px;
            background-size: cover;
            background-position: center;
        }

        .hero-overlay {
            background: rgba(13, 92, 58, 0.75);
            height: 100%;
            display: flex;
            align-items: center;
        }

        /* Card Styling */
        .card-unit {
            border: none;
            border-top: 5px solid var(--primary-green);
            transition: transform 0.3s ease;
        }

        .card-unit:hover {
            transform: translateY(-5px);
        }

        .badge-unit {
            font-size: 0.8rem;
            padding: 5px 10px;
        }
    </style>
</head>
<body>

    <!-- ================= 1. NAVBAR ================= -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-islamic-green sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fa-solid fa-mosque fa-2x me-2 text-warning"></i>
                <div>
                    <span class="fw-bold d-block leading-none">YPI NURUL FALAH</span>
                    <small style="font-size: 0.75rem;">RA - MI - SMPI</small>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#profil">Profil Unit</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pengumuman">Informasi KBM</a></li>
                    <li class="nav-item"><a class="nav-link" href="#galeri">Galeri Ekskul</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-accent btn-sm px-3 shadow-sm rounded-pill mt-2 mt-lg-0" href="ppdb.php">
                            <i class="fa-solid fa-user-plus me-1"></i> Info PPDB
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill mt-2 mt-lg-0" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ================= 2. HERO BANNER SLIDER ================= -->
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200');">
                <div class="hero-overlay">
                    <div class="container text-white">
                        <h1 class="fw-bold display-5 mb-3">Selamat Datang di YPI Nurul Falah</h1>
                        <p class="lead mb-4">Membentuk Generasi Islami, Cerdas, dan Berakhlaqul Karimah pada Jenjang RA, MI, dan SMPI.</p>
                        <a href="ppdb.php" class="btn btn-accent btn-lg me-2 rounded-pill"><i class="fa-solid fa-paper-plane me-1"></i> Pendaftaran PPDB</a>
                        <a href="#pengumuman" class="btn btn-outline-light btn-lg rounded-pill">Agenda KBM</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= 3. PROFIL UNIT SEKOLAH ================= -->
    <section id="profil" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h6 class="text-islamic-green fw-bold text-uppercase">Pendidikan Terpadu</h6>
                <h2 class="fw-bold">Unit Pendidikan YPI Nurul Falah</h2>
                <div class="mx-auto bg-warning" style="height: 3px; width: 60px;"></div>
            </div>

            <div class="row g-4">
                <!-- Unit RA -->
                <div class="col-md-4">
                    <div class="card card-unit shadow-sm h-100 p-4 text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-child-reaching fa-3x text-islamic-green"></i>
                        </div>
                        <h4 class="fw-bold">RA (Raudhatul Athfal)</h4>
                        <p class="text-muted small">Pendidikan anak usia dini berbasis pembentukan karakter Islami, hafalan doa harian, dan bermain sambil belajar.</p>
                    </div>
                </div>

                <!-- Unit MI -->
                <div class="col-md-4">
                    <div class="card card-unit shadow-sm h-100 p-4 text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-book-quran fa-3x text-islamic-green"></i>
                        </div>
                        <h4 class="fw-bold">MI (Madrasah Ibtidaiyah)</h4>
                        <p class="text-muted small">Setara Sekolah Dasar dengan keunggulan kurikulum agama terpadu, program Tahfidz Qur'an, dan pembiasaan sholat berjamaah.</p>
                    </div>
                </div>

                <!-- Unit SMPI -->
                <div class="col-md-4">
                    <div class="card card-unit shadow-sm h-100 p-4 text-center">
                        <div class="mb-3">
                            <i class="fa-solid fa-graduation-cap fa-3x text-islamic-green"></i>
                        </div>
                        <h4 class="fw-bold">SMPI (SMP Islam)</h4>
                        <p class="text-muted small">Pendidikan tingkat pertama yang mengombinasikan akademik nasional, sains, teknologi, serta pendalaman ilmu syariah.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 4. PENGUMUMAN KBM TERBARU ================= -->
    <section id="pengumuman" class="py-5 bg-light">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h6 class="text-islamic-green fw-bold text-uppercase">Informasi Sekolah</h6>
                    <h2 class="fw-bold m-0">Pengumuman & Agenda KBM</h2>
                </div>
            </div>

            <div class="row g-4">
                <?php if (!empty($daftar_pengumuman)): ?>
                    <?php foreach ($daftar_pengumuman as $row): ?>
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-islamic-green badge-unit">
                                            <?= htmlspecialchars($row['target_unit']); ?>
                                        </span>
                                        <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> <?= date('d M Y', strtotime($row['tanggal_post'])); ?></small>
                                    </div>
                                    <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($row['judul']); ?></h5>
                                    <p class="card-text text-muted small">
                                        <?= htmlspecialchars(substr($row['isi_pengumuman'], 0, 120)) . '...'; ?>
                                    </p>
                                    <?php if (!empty($row['file_lampiran'])): ?>
                                        <a href="uploads/pengumuman/<?= $row['file_lampiran']; ?>" class="btn btn-sm btn-outline-success rounded-pill mt-2" target="_blank">
                                            <i class="fa-solid fa-paperclip me-1"></i> Unduh Edaran
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info text-center">Belum ada pengumuman KBM terbaru saat ini.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ================= 5. GALERI EKSKUL ================= -->
    <section id="galeri" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h6 class="text-islamic-green fw-bold text-uppercase">Dokumentasi Siswa</h6>
                <h2 class="fw-bold">Galeri Kegiatan & Ekskul</h2>
                <div class="mx-auto bg-warning" style="height: 3px; width: 60px;"></div>
            </div>

            <div class="row g-3">
                <?php if (!empty($daftar_galeri)): ?>
                    <?php foreach ($daftar_galeri as $galeri): ?>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 shadow-sm overflow-hidden h-100">
                                <img src="uploads/galeri/<?= htmlspecialchars($galeri['nama_file_foto']); ?>" class="card-img-top" alt="Galeri" style="height: 180px; object-fit: cover;">
                                <div class="card-body p-2 text-center">
                                    <small class="fw-bold d-block text-truncate"><?= htmlspecialchars($galeri['judul_kegiatan']); ?></small>
                                    <span class="badge bg-secondary style-font" style="font-size:0.7rem;"><?= htmlspecialchars($galeri['jenis_ekskul']); ?> (<?= htmlspecialchars($galeri['target_unit']); ?>)</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">Belum ada dokumentasi galeri kegiatan.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ================= 6. FOOTER ================= -->
    <footer class="bg-islamic-green text-white py-4 mt-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="fw-bold mb-3"><i class="fa-solid fa-mosque me-2 text-warning"></i>YPI Nurul Falah</h5>
                    <p class="small text-white-50">Menyediakan layanan informasi pendidikan berbasis digital untuk memudahkan komunikasi agenda KBM dan pendaftaran siswa baru secara transparan.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5 class="fw-bold mb-3">Kontak Sekolah</h5>
                    <p class="small text-white-50 m-0"><i class="fa-solid fa-location-dot me-2"></i> RA-MI-SMPI Nurul Falah</p>
                    <p class="small text-white-50"><i class="fa-solid fa-envelope me-2"></i> info@nurulfalah.sch.id</p>
                </div>
            </div>
            <hr class="border-secondary my-3">
            <div class="text-center small text-white-50">
                &copy; 2026 YPI Nurul Falah. All Rights Reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>