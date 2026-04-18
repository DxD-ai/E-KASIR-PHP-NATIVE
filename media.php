<?php
session_start();

// PANGGIL KONEKSI DATABASE
include 'koneksi.php';

// Cek apakah user sudah login
if (empty($_SESSION['status'])) {
    header("Location: index.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aplikasi Kasir</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/all.min.css" rel="stylesheet">
    <link href="assets/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet">

    <script src="assets/js/jquery.min.js"></script>

    <style>
        body {
            background-color:rgb(248, 248, 248);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            color: #fff;
            transition: all 0.3s;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin-bottom: 5px;
            border-radius: 0 25px 25px 0;
            transition: 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #fff;
            color: #2c3e50;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link i {
            width: 25px;
        }

        .content-wrapper {
            width: 100%;
            padding: 20px;
        }

        .card-rich {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            background: #fff;
        }
    </style>
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="sidebar p-3" style="width: 280px; flex-shrink: 0;">
        <?php include 'menu.php'; ?>
    </div>

    <!-- CONTENT -->
    <div class="content-wrapper d-flex flex-column min-vh-100">

        <!-- NAVBAR -->
        <nav class="navbar navbar-light bg-white rounded shadow-sm mb-4 px-4 py-3">
            <span class="navbar-brand mb-0 h1 fw-bold text-primary">
                <i class="fa-solid fa-bars me-2"></i> Sparepart
            </span>

            <div class="d-flex align-items-center">
                <div class="me-3 text-end">
                    <small class="text-muted d-block">Halo, MR</small>
                    <span class="fw-bold text-dark">
                        <?= $_SESSION['username'] ?? 'User'; ?>
                    </span>
                </div>
                <img src="admin.jpeg" 
     class="rounded-circle" 
     width="35" 
     height="35" 
     style="object-fit: cover; object-position: center;" 
     alt="Admin">
            </div>
        </nav>

        <!-- MAIN CONTENT -->
        <div class="container-fluid flex-grow-1">
            <?php include 'content.php'; ?>
        </div>

        <!-- FOOTER -->
        <footer class="mt-auto py-3 text-center text-muted small">
            <?php include 'footer.php'; ?>
        </footer>

    </div>
</div>

<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>