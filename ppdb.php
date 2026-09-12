<?php
/**
 * Halaman Portal Informasi PPDB
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

// Tampilkan error jika ada kendala sistem
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Panggil file koneksi database
require_once 'koneksi.php';

// 1. Ambil Data Berkas PPDB (Formulir & Brosur) dari Database
try {
    $stmt_berkas = $pdo->query("SELECT * FROM berkas_ppdb ORDER BY target_unit ASC, jenis_berkas ASC");
    $daftar_berkas = $stmt_berkas->fetchAll();
} catch (PDOException $e) {
    $daftar_berkas = [];
}

// 2. Ambil Data Kontak WA Panitia per Unit
try {
    $stmt_wa = $pdo->query("SELECT * FROM kontak_wa ORDER BY target_unit ASC");
    $daftar_wa = $stmt_wa->fetchAll();
} catch (PDOException $e) {
    $daftar_wa = [];
}

// 3. Ambil Data FAQ PPDB (Urut Berdasarkan Kolom Urutan)
try {
    $stmt_faq = $pdo->query("SELECT * FROM faq_ppdb ORDER BY urutan ASC");
    $daftar_faq = $stmt_faq->fetchAll();
} catch (PDOException $e) {
    $daftar_faq = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi PPDB - YPI Nurul Falah</title>
    
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

        .btn-whatsapp {
            background-color: #25D366;
            color: white;
            font-weight: 600;
            border: none;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
            color: white;
        }

        /* Header Banner Styling */
        .ppdb-header {
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.95), rgba(46, 125, 50, 0.85)), url('https://images.unsplash.com/photo-1577896851231-70ef18881754?q=80&w=1200');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 60px 0;
        }

        /* Nav Pills Styling */
        .nav-pills-custom .nav-link {
            color: var(--primary-green);
            font-weight: 600;
            border: 2px solid var(--primary-green);
            margin: 0 5px;
            border-radius: 30px;
        }

        .nav-pills-custom .nav-link.active {
            background-color: var(--primary-green);
            color: white;
        }

        /* Card Custom */
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Accordion Custom */
        .accordion-button:not(.collapsed) {
            color: var(--primary-green);
            background-color: var(--light-green);
            font-weight: 600;
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
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link active" href="ppdb.php">Info PPDB</a></li>
                    <li class="nav-item"><a class="nav-link" href="#alur">Alur & Syarat</a></li>
                    <li class="nav-item"><a class="nav-link" href="#download">Unduh Berkas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light btn-sm px-3 rounded-pill mt-2 mt-lg-0" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ================= 2. HEADER PPDB ================= -->
    <header class="ppdb-header text-center">
        <div class="container">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">PPDB ONLINE INFORMATIONAL</span>
            <h1 class="fw-bold display-5">Pendaftaran Peserta Didik Baru</h1>
            <p class="lead mb-0">Tahun Ajaran Baru YPI Nurul Falah (Unit RA, MI, & SMPI)</p>
        </div>
    </header>

    <!-- ================= 3. ALUR & SYARAT PPDB PER UNIT ================= -->
    <section id="alur" class="py-5">
        <div class="container py-3">
            <div class="text-center mb-5">
                <h6 class="text-islamic-green fw-bold text-uppercase">Persyaratan Masuk</h6>
                <h2 class="fw-bold">Alur & Syarat Berkas Per Unit</h2>
                <div class="mx-auto bg-warning" style="height: 3px; width: 60px;"></div>
            </div>

            <!-- Tab Navigasi Unit -->
            <ul class="nav nav-pills nav-pills-custom justify-content-center mb-4" id="ppdbTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-4" id="ra-tab" data-bs-toggle="tab" data-bs-target="#ra-content" type="button">Unit RA</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4" id="mi-tab" data-bs-toggle="tab" data-bs-target="#mi-content" type="button">Unit MI</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4" id="smpi-tab" data-bs-toggle="tab" data-bs-target="#smpi-content" type="button">Unit SMPI</button>
                </li>
            </ul>

            <!-- Isi Konten Tab -->
            <div class="tab-content" id="ppdbTabContent">
                
                <!-- Tab Unit RA -->
                <div class="tab-pane fade show active" id="ra-content" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100">
                                <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-file-lines me-2"></i>Syarat Dokumen RA</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Kartu Keluarga (KK) - 2 Lembar</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Akta Kelahiran - 2 Lembar</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi KTP Orang Tua (Ayah & Ibu)</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Pas Foto Calon Siswa 3x4 (4 Lembar)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100 bg-light">
                                <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-circle-info me-2"></i>Informasi Pendaftaran RA</h4>
                                <p class="small text-muted mb-2"><strong>Fasilitas Seragam:</strong> Seragam Batik, Seragam Olahraga, dan Atribut Muslim.</p>
                                <p class="small text-muted mb-2"><strong>Jam KBM:</strong> Senin - Jumat (07.30 - 10.30 WIB).</p>
                                <p class="small text-muted"><strong>Catatan:</strong> Pengisian formulir fisik dapat diunduh di bawah atau diambil langsung di kantor RA.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Unit MI -->
                <div class="tab-pane fade" id="mi-content" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100">
                                <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-file-lines me-2"></i>Syarat Dokumen MI</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Kartu Keluarga (KK) - 2 Lembar</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Akta Kelahiran - 2 Lembar</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Ijazah / Surat Kelulusan RA/TK (Jika ada)</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Pas Foto Calon Siswa 3x4 (4 Lembar)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100 bg-light">
                                <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-circle-info me-2"></i>Informasi Pendaftaran MI</h4>
                                <p class="small text-muted mb-2"><strong>Fasilitas Seragam:</strong> Seragam Merah-Putih, Seragam Batik Sekolah, Seragam Pramuka, dan Olahraga.</p>
                                <p class="small text-muted mb-2"><strong>Jam KBM:</strong> Senin - Sabtu (07.00 - 12.00 WIB).</p>
                                <p class="small text-muted"><strong>Keunggulan:</strong> Program Tahfidz Juz 30 dan Pembiasaan Sholat Dhuha Berjamaah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Unit SMPI -->
                <div class="tab-pane fade" id="smpi-content" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100">
                                <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-file-lines me-2"></i>Syarat Dokumen SMPI</h4>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Ijazah / Surat Kelulusan SD/MI (Legalisir)</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Kartu Keluarga (KK) & Akta Kelahiran</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi NISN (Nomor Induk Siswa Nasional)</li>
                                    <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Pas Foto Calon Siswa 3x4 (4 Lembar)</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-custom p-4 h-100 bg-light">
                                <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-circle-info me-2"></i>Informasi Pendaftaran SMPI</h4>
                                <p class="small text-muted mb-2"><strong>Fasilitas Seragam:</strong> Seragam Biru-Putih, Seragam Batik, Seragam Pramuka, dan Olahraga.</p>
                                <p class="small text-muted mb-2"><strong>Jam KBM:</strong> Senin - Sabtu (07.00 - 13.30 WIB).</p>
                                <p class="small text-muted"><strong>Keunggulan:</strong> Pembinaan Ekskul Komputer, Seni Qur'an, dan Pramuka.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= 4. PUSAT UNDUH BERKAS (DOWNLOAD CENTER) ================= -->
    <section id="download" class="py-5 bg-light">
        <div class="container py-3">
            <div class="text-center mb-5">
                <h6 class="text-islamic-green fw-bold text-uppercase">Download Center</h6>
                <h2 class="fw-bold">Unduh Formulir & Brosur PPDB</h2>
                <div class="mx-auto bg-warning" style="height: 3px; width: 60px;"></div>
                <p class="text-muted small mt-2">Unduh formulir pendaftaran fisik dan brosur rincian biaya resmi berikut untuk dicetak.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <?php if (!empty($daftar_berkas)): ?>
                    <?php foreach ($daftar_berkas as $berkas): ?>
                        <div class="col-md-4">
                            <div class="card card-custom p-4 text-center h-100">
                                <div class="mb-3">
                                    <i class="fa-solid fa-file-pdf fa-3x text-danger"></i>
                                </div>
                                <span class="badge bg-islamic-green mb-2 mx-auto" style="width: fit-content;">
                                    Unit <?= htmlspecialchars($berkas['target_unit']); ?>
                                </span>
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($berkas['nama_berkas']); ?></h5>
                                <p class="text-muted small mb-3">Tipe: <?= ucfirst(htmlspecialchars($berkas['jenis_berkas'])); ?> (PDF)</p>
                                <a href="download.php?file=<?= urlencode($berkas['nama_file']); ?>" class="btn btn-islamic rounded-pill mt-auto shadow-sm">
                                    <i class="fa-solid fa-download me-1"></i> Unduh Berkas
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-8">
                        <div class="alert alert-warning text-center shadow-sm">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Berkas formulir digital belum diunggah oleh pihak admin sekolah. Silakan hubungi panitia via WhatsApp di bawah.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ================= 5. DIRECT WHATSAPP PANITIA ================= -->
    <section class="py-5 bg-islamic-green text-white">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Butuh Bantuan Pendaftaran?</h2>
            <p class="lead mb-4">Hubungi Panitia PPDB atau Kepala Sekolah langsung via WhatsApp sesuai unit yang dituju:</p>
            
            <div class="row g-3 justify-content-center">
                <?php if (!empty($daftar_wa)): ?>
                    <?php foreach ($daftar_wa as $wa): ?>
                        <div class="col-md-4">
                            <a href="https://wa.me/<?= htmlspecialchars($wa['nomor_wa']); ?>?text=Assalamu'alaikum,%20saya%20ingin%20bertanya%20mengenai%20PPDB%20Unit%20<?= htmlspecialchars($wa['target_unit']); ?>" target="_blank" class="btn btn-whatsapp btn-lg w-100 rounded-pill shadow">
                                <i class="fa-brands fa-whatsapp fa-lg me-2"></i> <?= htmlspecialchars($wa['nama_kontak']); ?> (<?= htmlspecialchars($wa['target_unit']); ?>)
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Kontak Fallback Default jika DB kosong -->
                    <div class="col-md-4">
                        <a href="https://wa.me/6281234567890?text=Assalamu'alaikum,%20saya%20ingin%20bertanya%20mengenai%20PPDB" target="_blank" class="btn btn-whatsapp btn-lg w-100 rounded-pill shadow">
                            <i class="fa-brands fa-whatsapp fa-lg me-2"></i> Hubungi Panitia PPDB
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- ================= 6. ACCORDION FAQ PPDB ================= -->
    <section id="faq" class="py-5">
        <div class="container py-3">
            <div class="text-center mb-5">
                <h6 class="text-islamic-green fw-bold text-uppercase">Pertanyaan Umum</h6>
                <h2 class="fw-bold">FAQ (Frequently Asked Questions)</h2>
                <div class="mx-auto bg-warning" style="height: 3px; width: 60px;"></div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-9">
                    <div class="accordion shadow-sm rounded-3 overflow-hidden" id="faqAccordion">
                        
                        <?php if (!empty($daftar_faq)): ?>
                            <?php foreach ($daftar_faq as $index => $faq): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $index; ?>">
                                        <button class="accordion-button <?= $index !== 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index; ?>">
                                            <i class="fa-solid fa-circle-question me-2 text-success"></i> <?= htmlspecialchars($faq['pertanyaan']); ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $index; ?>" class="accordion-collapse collapse <?= $index === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body text-muted">
                                            <?= nl2br(htmlspecialchars($faq['jawaban'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Data FAQ Default (Berdasarkan Hasil Wawancara Sekolah) -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="h1">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#c1">
                                        <i class="fa-solid fa-circle-question me-2 text-success"></i> Unit pendidikan apa saja yang tersedia di YPI Nurul Falah?
                                    </button>
                                </h2>
                                <div id="c1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        YPI Nurul Falah menyelenggarakan 3 jenjang pendidikan terpadu yaitu: Raudhatul Athfal (RA/setara TK), Madrasah Ibtidaiyah (MI/setara SD), dan Sekolah Menengah Pertama Islam (SMPI/setara SMP).
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="h2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c2">
                                        <i class="fa-solid fa-circle-question me-2 text-success"></i> Berapa estimasi biaya pendaftaran dan apakah ada potongan/diskon khusus?
                                    </button>
                                </h2>
                                <div id="c2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Rincian biaya pendaftaran, uang pangkal, serta informasi potongan/diskon (seperti diskon pendaftaran awal/gelombang 1 atau pendaftar bersaudara) dapat dilihat pada Brosur PPDB resmi yang dapat diunduh di atas atau dikonfirmasikan ke Panitia via WhatsApp.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="h3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c3">
                                        <i class="fa-solid fa-circle-question me-2 text-success"></i> Seragam apa saja yang akan didapatkan oleh calon siswa baru?
                                    </button>
                                </h2>
                                <div id="c3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Paket seragam meliputi Seragam Utama (Batik/Identitas Yayasan, Seragam Khusus Unit), Seragam Olahraga, dan Seragam Pramuka sesuai aturan jenjang masing-masing.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="h4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c4">
                                        <i class="fa-solid fa-circle-question me-2 text-success"></i> Apa saja mata pelajaran unggulan dan kegiatan ekstrakurikuler di sekolah?
                                    </button>
                                </h2>
                                <div id="c4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-muted">
                                        Program unggulan meliputi Tahfidz Al-Qur'an, Pembiasaan Sholat Berjamaah/Dhuha, serta ekstrakurikuler seperti Pramuka, Seni Seni Qur'an/Hadroh, dan Olahraga.
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ================= 7. FOOTER ================= -->
    <footer class="bg-islamic-green text-white py-4 mt-5">
        <div class="container text-center">
            <p class="small text-white-50 m-0">
                &copy; 2026 YPI Nurul Falah. All Rights Reserved.
            </p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>