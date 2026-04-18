<?php 
if (isset($_GET['content'])) {
    $page = $_GET['content'];

    switch ($page) {
        case 'home':
            include 'content/home.php';
            break;
        case 'kategori':
            include 'content/kategori.php';
            break;
        case 'barang':
            include 'content/barang.php';
            break;
        case 'transaksi':
            include 'content/transaksi.php';
            break;
        case 'laporan':
            include 'content/laporan.php';
            break;
        case 'about':
            include 'content/about.php';
            break;
        default:
            include 'content/blank.php';
            break;
    }
} else {
    include 'content/home.php';
}
?>