<?php
/**
 * Skrip Pemrosesan CRUD Akun Admin (Tambah, Edit, Hapus)
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();
require_once '../koneksi.php';

// Proteksi Halaman Admin: Harus login dan memilki role super_admin
if (!isset($_SESSION['login_admin']) || $_SESSION['login_admin'] !== true || !isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    $_SESSION['error'] = "Akses ditolak! Anda tidak memiliki wewenang untuk aksi ini.";
    header("Location: kelola_admin.php");
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'tambah') {
        $username = trim($_POST['username'] ?? '');
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'admin_unit';
        $unit_akses = $_POST['unit_akses'] ?? 'all';

        if (empty($username) || empty($nama_lengkap) || empty($password) || empty($role) || empty($unit_akses)) {
            $_SESSION['error'] = "Semua kolom form wajib diisi!";
            header("Location: kelola_admin.php");
            exit;
        }

        try {
            // Cek keunikan username
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Username '$username' sudah digunakan oleh akun lain!";
                header("Location: kelola_admin.php");
                exit;
            }

            // Enkripsi password menggunakan password_hash()
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // Simpan akun baru
            $stmtInsert = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role, unit_akses) VALUES (:username, :password, :nama_lengkap, :role, :unit_akses)");
            $stmtInsert->execute([
                ':username'     => $username,
                ':password'     => $password_hashed,
                ':nama_lengkap' => $nama_lengkap,
                ':role'         => $role,
                ':unit_akses'   => $unit_akses
            ]);

            $_SESSION['success'] = "Akun admin baru <strong>" . htmlspecialchars($username) . "</strong> berhasil ditambahkan dengan password terenkripsi.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menambah akun admin: " . $e->getMessage();
        }
        header("Location: kelola_admin.php");
        exit;

    } elseif ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $username = trim($_POST['username'] ?? '');
        $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'admin_unit';
        $unit_akses = $_POST['unit_akses'] ?? 'all';

        if ($id <= 0 || empty($username) || empty($nama_lengkap) || empty($role) || empty($unit_akses)) {
            $_SESSION['error'] = "Data yang dikirim tidak valid!";
            header("Location: kelola_admin.php");
            exit;
        }

        try {
            // Cek keunikan username (selain ID sendiri)
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id LIMIT 1");
            $stmt->execute([':username' => $username, ':id' => $id]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = "Username '$username' sudah digunakan oleh akun lain!";
                header("Location: kelola_admin.php");
                exit;
            }

            // Jika password diisi, perbarui password dengan enkripsi hash baru
            if (!empty($password)) {
                $password_hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmtUpdate = $pdo->prepare("UPDATE users SET username = :username, password = :password, nama_lengkap = :nama_lengkap, role = :role, unit_akses = :unit_akses WHERE id = :id");
                $stmtUpdate->execute([
                    ':username'     => $username,
                    ':password'     => $password_hashed,
                    ':nama_lengkap' => $nama_lengkap,
                    ':role'         => $role,
                    ':unit_akses'   => $unit_akses,
                    ':id'           => $id
                ]);
            } else {
                // Jika password kosong, pertahankan password lama
                $stmtUpdate = $pdo->prepare("UPDATE users SET username = :username, nama_lengkap = :nama_lengkap, role = :role, unit_akses = :unit_akses WHERE id = :id");
                $stmtUpdate->execute([
                    ':username'     => $username,
                    ':nama_lengkap' => $nama_lengkap,
                    ':role'         => $role,
                    ':unit_akses'   => $unit_akses,
                    ':id'           => $id
                ]);
            }

            // Jika mengedit akun sendiri yang sedang aktif log in, update session
            if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id) {
                $_SESSION['username']   = $username;
                $_SESSION['admin_name'] = $nama_lengkap;
                $_SESSION['role']       = $role;
                $_SESSION['unit_akses'] = $unit_akses;
            }

            $_SESSION['success'] = "Data akun admin <strong>" . htmlspecialchars($username) . "</strong> berhasil diperbarui.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui akun admin: " . $e->getMessage();
        }
        header("Location: kelola_admin.php");
        exit;

    } elseif ($action === 'hapus') {
        $id = intval($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = "ID akun tidak valid!";
            header("Location: kelola_admin.php");
            exit;
        }

        // Mencegah menghapus akun sendiri yang sedang login
        if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id) {
            $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan untuk log in!";
            header("Location: kelola_admin.php");
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $_SESSION['success'] = "Akun admin berhasil dihapus dari database.";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal menghapus akun admin: " . $e->getMessage();
        }
        header("Location: kelola_admin.php");
        exit;
    }
}

// Redirect default jika tanpa aksi yang valid
header("Location: kelola_admin.php");
exit;
