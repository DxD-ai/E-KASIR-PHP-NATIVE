<?php

//INI PC
$host = "localhost";
$user = "root";
$pass = "";
$db   = "syadanDb"; // Samakan Dengan Database Yang Sudah Di buat

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

?>