<?php
/**
 * Form Login Admin (Tampilan SB Admin 2 Style)
 * Portal Informasi PPDB dan KBM YPI Nurul Falah
 * Author: Maudy Tri Kusuma
 */

session_start();

// Jika admin sudah login, langsung arahkan ke dashboard admin
if (isset($_SESSION['login_admin']) && $_SESSION['login_admin'] === true) {
    header("Location: admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login Admin - YPI Nurul Falah</title>

    <!-- Custom fonts for SB Admin 2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for SB Admin 2 (via CDN Bootstrap 4 & Custom Layout) -->
    <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .bg-login-image-custom {
            background: linear-gradient(135deg, rgba(27, 94, 32, 0.9), rgba(46, 125, 50, 0.8)), 
                        url('https://images.unsplash.com/photo-1541829070764-84a7d30dd3f3?q=80&w=800');
            background-position: center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
        }
        .bg-islamic-green {
            background-color: #1B5E20 !important;
        }
        .btn-islamic {
            background-color: #1B5E20;
            color: white;
            border: none;
        }
        .btn-islamic:hover {
            background-color: #2E7D32;
            color: white;
        }
    </style>
</head>

<body class="bg-islamic-green">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center align-items-center min-vh-100">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <!-- Left Branding Column (SB Admin 2 Style) -->
                            <div class="col-lg-6 d-none d-lg-block bg-login-image-custom text-center">
                                <i class="fa-solid fa-mosque fa-4x mb-3 text-warning"></i>
                                <h3 class="font-weight-bold">YPI NURUL FALAH</h3>
                                <p class="small mb-0">Sistem Informasi Manajemen Portal PPDB & KBM Terpadu (RA - MI - SMPI)</p>
                            </div>

                            <!-- Right Form Column -->
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2 font-weight-bold">Selamat Datang!</h1>
                                        <p class="text-muted small mb-4">Masukkan username dan password admin Anda</p>
                                    </div>

                                    <!-- Alert Notifikasi Pesan Error / Logout -->
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <?= htmlspecialchars($_SESSION['error']); ?>
                                            <?php unset($_SESSION['error']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($_SESSION['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show small" role="alert">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <?= htmlspecialchars($_SESSION['success']); ?>
                                            <?php unset($_SESSION['success']); ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Form Login -->
                                    <form class="user" action="proses_login.php" method="POST">
                                        <div class="form-group">
                                            <input type="text" class="form-style form-control form-control-user"
                                                id="username" name="username" placeholder="Masukkan Username..." required autofocus>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="password" name="password" placeholder="Masukkan Password..." required>
                                        </div>
                                        <button type="submit" name="btn_login" class="btn btn-islamic btn-user btn-block font-weight-bold py-2">
                                            <i class="fas fa-sign-in-alt mr-1"></i> Login Administrator
                                        </button>
                                    </form>

                                    <hr>

                                    <div class="text-center">
                                        <a class="small text-success" href="index.php">
                                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Halaman Utama (Publik)
                                        </a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

</body>
</html>