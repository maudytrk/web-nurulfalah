<?php
/**
 * Halaman Portal Informasi PPDB
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

// Matikan display_errors pada mode production
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Panggil file koneksi database
require_once 'koneksi.php';

// 1. Ambil Data Berkas PPDB (Formulir & Brosur) dari Database
try {
    $stmt_berkas = $pdo->query("SELECT * FROM berkas_ppdb ORDER BY target_unit ASC, jenis_berkas ASC");
    $daftar_berkas = $stmt_berkas->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Fetch Berkas PPDB Error: " . $e->getMessage());
    $daftar_berkas = [];
}

// 2. Ambil Data Kontak WA Panitia per Unit
try {
    $stmt_wa = $pdo->query("SELECT * FROM kontak_wa ORDER BY target_unit ASC");
    $daftar_wa = $stmt_wa->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Fetch Kontak WA Error: " . $e->getMessage());
    $daftar_wa = [];
}

// 3. Ambil Data FAQ PPDB (Urut Berdasarkan Kolom Urutan)
try {
    $stmt_faq = $pdo->query("SELECT * FROM faq_ppdb ORDER BY urutan ASC");
    $daftar_faq = $stmt_faq->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Fetch FAQ Error: " . $e->getMessage());
    $daftar_faq = [];
}

// 4. Ambil Data Informasi PPDB Dinamis per Unit
try {
    $stmt_info = $pdo->query("SELECT * FROM ppdb_info");
    $info_raw = $stmt_info->fetchAll(PDO::FETCH_ASSOC);
    $ppdb_info = [];
    foreach ($info_raw as $info) {
        $ppdb_info[$info['unit']] = $info;
    }
} catch (PDOException $e) {
    error_log("Fetch PPDB Info Error: " . $e->getMessage());
    $ppdb_info = [];
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

            <!-- Isi Konten Tab Dinamis -->
            <div class="tab-content" id="ppdbTabContent">
                
                <?php 
                $units = ['RA', 'MI', 'SMPI'];
                foreach ($units as $idx => $u_code):
                    $u_data = $ppdb_info[$u_code] ?? null;
                    $is_active = ($idx === 0) ? 'show active' : '';
                ?>
                    <div class="tab-pane fade <?= $is_active; ?>" id="<?= strtolower($u_code); ?>-content" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card card-custom p-4 h-100">
                                    <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-file-lines me-2"></i>Syarat Dokumen Unit <?= $u_code; ?></h4>
                                    <ul class="list-group list-group-flush mb-3">
                                        <?php 
                                        if ($u_data && !empty($u_data['persyaratan'])):
                                            $syarat_items = array_filter(explode("\n", $u_data['persyaratan']));
                                            foreach ($syarat_items as $syarat):
                                        ?>
                                                <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i><?= htmlspecialchars(trim($syarat)); ?></li>
                                        <?php 
                                            endforeach;
                                        else:
                                        ?>
                                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Kartu Keluarga (KK) - 2 Lembar</li>
                                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Fotokopi Akta Kelahiran - 2 Lembar</li>
                                            <li class="list-group-item"><i class="fa-solid fa-check text-success me-2"></i>Pas Foto Calon Siswa 3x4 (4 Lembar)</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card card-custom p-4 h-100 bg-light">
                                    <h4 class="fw-bold text-islamic-green mb-3"><i class="fa-solid fa-circle-info me-2"></i>Informasi Pendaftaran Unit <?= $u_code; ?></h4>
                                    
                                    <p class="small text-muted mb-2">
                                        <strong>Gelombang:</strong> <?= htmlspecialchars($u_data['gelombang'] ?? 'Gelombang 1 (Januari - Mei)'); ?>
                                    </p>
                                    <p class="small text-muted mb-2">
                                        <strong>Biaya Form:</strong> <span class="badge bg-success"><?= htmlspecialchars($u_data['biaya_pendaftaran'] ?? 'Hubungi Panitia'); ?></span>
                                    </p>
                                    <p class="small text-muted mb-2">
                                        <strong>Jam KBM:</strong> <?= htmlspecialchars($u_data['jam_kbm'] ?? 'Senin - Jumat'); ?>
                                    </p>

                                    <?php if ($u_data && !empty($u_data['seragam'])): ?>
                                        <p class="small text-muted mb-2">
                                            <strong>Seragam Sekolah:</strong> <?= htmlspecialchars(str_replace("\n", ", ", $u_data['seragam'])); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($u_data && !empty($u_data['keunggulan'])): ?>
                                        <p class="small text-muted mb-2">
                                            <strong>Keunggulan:</strong> <?= htmlspecialchars(str_replace("\n", ", ", $u_data['keunggulan'])); ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ($u_data && !empty($u_data['rincian_biaya'])): ?>
                                        <div class="p-2 bg-white rounded border border-success mt-2">
                                            <small class="fw-bold text-islamic-green d-block mb-1">Rincian / Potongan Biaya:</small>
                                            <small class="text-muted"><?= nl2br(htmlspecialchars($u_data['rincian_biaya'])); ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

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
                    <!-- Kontak Fallback Default / Placeholder -->
                    <div class="col-md-4">
                        <a href="https://wa.me/6281234567890?text=Assalamu'alaikum,%20saya%20ingin%20bertanya%20mengenai%20PPDB" target="_blank" class="btn btn-whatsapp btn-lg w-100 rounded-pill shadow">
                            <i class="fa-brands fa-whatsapp fa-lg me-2"></i> Panitia PPDB RA (0812-3456-7890)
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="https://wa.me/6281234567891?text=Assalamu'alaikum,%20saya%20ingin%20bertanya%20mengenai%20PPDB" target="_blank" class="btn btn-whatsapp btn-lg w-100 rounded-pill shadow">
                            <i class="fa-brands fa-whatsapp fa-lg me-2"></i> Panitia PPDB MI (0812-3456-7891)
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="https://wa.me/6281234567892?text=Assalamu'alaikum,%20saya%20ingin%20bertanya%20mengenai%20PPDB" target="_blank" class="btn btn-whatsapp btn-lg w-100 rounded-pill shadow">
                            <i class="fa-brands fa-whatsapp fa-lg me-2"></i> Panitia PPDB SMPI (0812-3456-7892)
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