<?php
/**
 * Engine Backend CRUD Galeri Foto & Pengelolaan Berkas Gambar Server
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// 1. Proteksi Akses Backend Admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 2. Hanya menerima request via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: galeri.php");
    exit;
}

// 3. Panggil Koneksi Database
require_once '../koneksi.php';

$upload_dir = '../uploads/galeri/';

// Helper Function Upload Berkas Gambar
function uploadGambarGaleri($file, $target_dir) {
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $file_name = $file['name'];
        $file_tmp  = $file['tmp_name'];
        $file_size = $file['size'];
        $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

        // Validasi Ekstensi Gambar
        if (!in_array($file_ext, $allowed_ext)) {
            return ['status' => false, 'message' => 'Format berkas tidak diizinkan! Format harus berupa JPG, JPEG, PNG, atau WEBP.'];
        }

        // Validasi Ukuran Berkas (Maksimal 5MB)
        if ($file_size > 5 * 1024 * 1024) {
            return ['status' => false, 'message' => 'Ukuran file gambar terlalu besar! Maksimal 5 MB.'];
        }

        // Buat direktori jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate nama file unik
        $new_filename = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $file_name);

        if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
            return ['status' => true, 'filename' => $new_filename];
        } else {
            return ['status' => false, 'message' => 'Gagal mengunggah berkas gambar ke server.'];
        }
    }
    return ['status' => true, 'filename' => null];
}

$action = $_POST['action'] ?? '';

switch ($action) {

    // ==========================================
    // ACTION 1: TAMBAH FOTO (INSERT)
    // ==========================================
    case 'tambah':
        $judul      = trim($_POST['judul'] ?? '');
        $kategori   = trim($_POST['kategori'] ?? 'kegiatan');
        $unit_akses = trim($_POST['unit_akses'] ?? 'all');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if (empty($judul) || empty($unit_akses) || !isset($_FILES['file_gambar']) || $_FILES['file_gambar']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = "Judul, Target Unit, dan Berkas Gambar wajib diisi!";
            header("Location: galeri.php");
            exit;
        }

        $upload_result = uploadGambarGaleri($_FILES['file_gambar'], $upload_dir);

        if (!$upload_result['status']) {
            $_SESSION['error'] = $upload_result['message'];
            header("Location: galeri.php");
            exit;
        }

        $nama_file = $upload_result['filename'];

        try {
            $stmt = $pdo->prepare("INSERT INTO galeri (judul, kategori, unit_akses, keterangan, file_gambar, created_at) VALUES (:judul, :kategori, :unit_akses, :keterangan, :file_gambar, NOW())");
            $stmt->execute([
                ':judul'       => $judul,
                ':kategori'    => $kategori,
                ':unit_akses'  => $unit_akses,
                ':keterangan'  => $keterangan,
                ':file_gambar' => $nama_file
            ]);

            $_SESSION['success'] = "Foto galeri baru berhasil diunggah!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menyimpan ke database: " . $e->getMessage();
        }

        header("Location: galeri.php");
        exit;

    // ==========================================
    // ACTION 2: EDIT FOTO (UPDATE)
    // ==========================================
    case 'edit':
        $id         = (int)($_POST['id'] ?? 0);
        $judul      = trim($_POST['judul'] ?? '');
        $kategori   = trim($_POST['kategori'] ?? 'kegiatan');
        $unit_akses = trim($_POST['unit_akses'] ?? 'all');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($id <= 0 || empty($judul) || empty($unit_akses)) {
            $_SESSION['error'] = "Data tidak valid atau kolom wajib masih kosong!";
            header("Location: galeri.php");
            exit;
        }

        try {
            // Ambil data lama
            $stmt_old = $pdo->prepare("SELECT file_gambar FROM galeri WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old_data = $stmt_old->fetch();

            if (!$old_data) {
                $_SESSION['error'] = "Data galeri tidak ditemukan!";
                header("Location: galeri.php");
                exit;
            }

            $nama_file = $old_data['file_gambar'];

            // Jika ada gambar baru yang diunggah
            if (isset($_FILES['file_gambar']) && $_FILES['file_gambar']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadGambarGaleri($_FILES['file_gambar'], $upload_dir);

                if (!$upload_result['status']) {
                    $_SESSION['error'] = $upload_result['message'];
                    header("Location: galeri.php");
                    exit;
                }

                // Hapus berkas gambar lama dari folder server
                if (!empty($nama_file) && file_exists($upload_dir . $nama_file)) {
                    unlink($upload_dir . $nama_file);
                }

                $nama_file = $upload_result['filename'];
            }

            $stmt_update = $pdo->prepare("UPDATE galeri SET judul = :judul, kategori = :kategori, unit_akses = :unit_akses, keterangan = :keterangan, file_gambar = :file_gambar WHERE id = :id");
            $stmt_update->execute([
                ':judul'       => $judul,
                ':kategori'    => $kategori,
                ':unit_akses'  => $unit_akses,
                ':keterangan'  => $keterangan,
                ':file_gambar' => $nama_file,
                ':id'          => $id
            ]);

            $_SESSION['success'] = "Data galeri foto berhasil diperbarui!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui database: " . $e->getMessage();
        }

        header("Location: galeri.php");
        exit;

    // ==========================================
    // ACTION 3: HAPUS FOTO (DELETE)
    // ==========================================
    case 'hapus':
        $id = (int)($_POST['id'] ?? 0);

        if ($id > 0) {
            try {
                $stmt = $pdo->prepare("SELECT file_gambar FROM galeri WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();

                if ($data) {
                    // Hapus gambar fisik dari direktori server
                    if (!empty($data['file_gambar']) && file_exists($upload_dir . $data['file_gambar'])) {
                        unlink($upload_dir . $data['file_gambar']);
                    }

                    // Hapus baris dari database
                    $stmt_delete = $pdo->prepare("DELETE FROM galeri WHERE id = :id");
                    $stmt_delete->execute([':id' => $id]);

                    $_SESSION['success'] = "Foto galeri berhasil dihapus dari sistem!";
                } else {
                    $_SESSION['error'] = "Data galeri tidak ditemukan!";
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = "Gagal menghapus data: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "ID data tidak valid!";
        }

        header("Location: galeri.php");
        exit;

    default:
        $_SESSION['error'] = "Aksi tidak dikenal!";
        header("Location: galeri.php");
        exit;
}