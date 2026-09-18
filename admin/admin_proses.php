<?php
/**
 * Skrip Pemrosesan CRUD Akun Admin (Tambah, Edit, Hapus)
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 */

session_start();
require_once '../koneksi.php';
require_once '../helpers/auth_helper.php';
require_once '../helpers/csrf.php';

check_admin_auth();
enforce_unit_access('all', 'kelola_admin.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: kelola_admin.php");
    exit;
}

verify_csrf_token();

$action = $_POST['action'] ?? '';

if ($action === 'tambah') {
    $username     = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? 'admin_unit';
    $unit_akses   = $_POST['unit_akses'] ?? 'all';

    if (empty($username) || empty($nama_lengkap) || empty($password) || empty($role) || empty($unit_akses)) {
        $_SESSION['error'] = "Semua kolom form wajib diisi!";
        header("Location: kelola_admin.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Username '$username' sudah digunakan oleh akun lain!";
            header("Location: kelola_admin.php");
            exit;
        }

        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

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
        error_log("Insert Admin Error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan saat menambahkan akun admin baru.";
    }
    header("Location: kelola_admin.php");
    exit;

} elseif ($action === 'edit') {
    $id           = intval($_POST['id'] ?? 0);
    $username     = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role         = $_POST['role'] ?? 'admin_unit';
    $unit_akses   = $_POST['unit_akses'] ?? 'all';

    if ($id <= 0 || empty($username) || empty($nama_lengkap) || empty($role) || empty($unit_akses)) {
        $_SESSION['error'] = "Data yang dikirim tidak valid!";
        header("Location: kelola_admin.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id LIMIT 1");
        $stmt->execute([':username' => $username, ':id' => $id]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Username '$username' sudah digunakan oleh akun lain!";
            header("Location: kelola_admin.php");
            exit;
        }

        if (!empty($password)) {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmtUpdate      = $pdo->prepare("UPDATE users SET username = :username, password = :password, nama_lengkap = :nama_lengkap, role = :role, unit_akses = :unit_akses WHERE id = :id");
            $stmtUpdate->execute([
                ':username'     => $username,
                ':password'     => $password_hashed,
                ':nama_lengkap' => $nama_lengkap,
                ':role'         => $role,
                ':unit_akses'   => $unit_akses,
                ':id'           => $id
            ]);
        } else {
            $stmtUpdate = $pdo->prepare("UPDATE users SET username = :username, nama_lengkap = :nama_lengkap, role = :role, unit_akses = :unit_akses WHERE id = :id");
            $stmtUpdate->execute([
                ':username'     => $username,
                ':nama_lengkap' => $nama_lengkap,
                ':role'         => $role,
                ':unit_akses'   => $unit_akses,
                ':id'           => $id
            ]);
        }

        if (isset($_SESSION['admin_id']) && $_SESSION['admin_id'] == $id) {
            $_SESSION['username']   = $username;
            $_SESSION['admin_name'] = $nama_lengkap;
            $_SESSION['role']       = $role;
            $_SESSION['unit_akses'] = $unit_akses;
        }

        $_SESSION['success'] = "Data akun admin <strong>" . htmlspecialchars($username) . "</strong> berhasil diperbarui.";
    } catch (PDOException $e) {
        error_log("Update Admin Error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui akun admin.";
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
        error_log("Delete Admin Error: " . $e->getMessage());
        $_SESSION['error'] = "Terjadi kesalahan saat menghapus akun admin.";
    }
    header("Location: kelola_admin.php");
    exit;
}

header("Location: kelola_admin.php");
exit;
