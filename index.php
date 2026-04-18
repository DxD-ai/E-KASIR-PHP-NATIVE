<?php
session_start(); // Memulai Sesi Login Untuk Menyimpan Username Dan Password Apabila ada
require 'koneksi.php'; // Sisihkan File Koneksi Untuk Menyambukan Ke Dayabase

if (isset($_POST['btn_login'])) { // Jika Button Login Di Klik

    $username = mysqli_real_escape_string($koneksi, $_POST['username']); //mengambil data yang username di input
    $password = mysqli_real_escape_string($koneksi, md5($_POST['password'])); // Pakai MD5 karena varchar(50)

    // Cek user ke database
    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username = '$username' AND password = '$password'"); // Query Untuk Menampilkan Tabel Admin
    $cek = mysqli_num_rows($query); //Hitung Jumlah Baris Pada Tabel Admin

    if ($cek > 0) { // jika data Lebih Dari 0
        $data = mysqli_fetch_assoc($query);
        // Set Sessi Login 
        $_SESSION['id_admin'] = $data['id_admin'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['status'] = "login";

        // Redirect ke dashboard 
        echo "<script>
        alert('Login Berhasil! Selamat Datang.'); 
        window.location.href = 'media.php?content=home'; 
        </script>";
    } else { // Jika Salah
        $error = "Username atau Password Salah!";
    }
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Petugas - UKK 2026</title>
    
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            /* Gradient Background Modern */
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card-login {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding-top: 30px;
            text-align: center;
        }
        .card-header i {
            font-size: 50px;
            color: #764ba2;
            margin-bottom: 10px;
        }
        .btn-primary-custom {
            background: linear-gradient(to right, #667eea, #764ba2);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            transition: 0.3s;
        }
        .btn-primary-custom:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .form-floating label {
            padding-left: 45px; /* Geser label agar tidak tabrakan dengan icon */
        }
        .input-icon {
            position: absolute;
            top: 18px;
            left: 15px;
            z-index: 10;
            color: #aaa;
        }
        .form-control {
            padding-left: 45px; /* Geser text input */
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <div class="card card-login p-4">
        <div class="card-header">
            <i class="fa-solid fa-store"></i>
            <h4 class="fw-bold mt-2">Aplikasi Penjualan Sparepart</h4>
            <p class="text-muted small">Masukan Username Dan Password Untuk Masuk Ke Aplikasi</p>
        </div>
        
        <div class="card-body">
            <?php if(isset($error)) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="position-relative mb-3">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                        <label for="username">Username</label>
                    </div>
                </div>

                <div class="position-relative mb-4">
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="showPass">
                        <label class="form-check-label small text-muted" for="showPass">
                            Tampilkan Password
                        </label>
                    </div>
                </div>

                <button type="submit" name="btn_login" class="btn btn-primary-custom">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> MASUK
                </button>
            </form>
        </div>
        <div class="card-footer text-center bg-white border-0 pb-3">
            <small class="text-muted">&copy; 2026 UKK PPLG</small>
        </div>
    </div>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $(document).ready(function(){
            $('#showPass').click(function(){
                if($(this).is(':checked')){
                    $('#password').attr('type','text');
                }else{
                    $('#password').attr('type','password');
                }
            });
        });
    </script>
</body>
</html>