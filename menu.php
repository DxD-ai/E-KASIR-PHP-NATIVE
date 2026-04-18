<?php
$page = $_GET['content'] ?? 'home';
?>

<div class="mb-4 text-center">
    <h4 class="fw-bold mt-2"><i class="fa-solid fa-store"></i> E-KASIR</h4>
    <small class="text-white-50">Aplikasi Ujikom 2026</small>
</div>
<hr class="text-white-50">

<ul class="nav nav-pills flex-column mb-auto">
    <li class="nav-item">
        <a href="media.php?content=home" class="nav-link <?= ($page=='home') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
    </li>

    <li class="nav-item mt-3 mb-1">
        <span class="text-white-50 small text-uppercase fw-bold ms-3">Master Data</span>
    </li>

    <li class="nav-item">
        <a href="media.php?content=kategori" class="nav-link <?= ($page=='kategori') ? 'active' : ''; ?>">
            <i class="fa-solid fa-layer-group"></i> Kategori
        </a>
    </li>

    <li class="nav-item">
        <a href="media.php?content=barang" class="nav-link <?= ($page=='barang') ? 'active' : ''; ?>">
            <i class="fa-solid fa-box"></i> Data Barang
        </a>
    </li>

    <li class="nav-item mt-3 mb-1">
        <span class="text-white-50 small text-uppercase fw-bold ms-3">Kasir</span>
    </li>

    <li class="nav-item">
        <a href="media.php?content=transaksi" class="nav-link <?= ($page=='transaksi') ? 'active' : ''; ?>">
            <i class="fa-solid fa-cash-register"></i> Transaksi
        </a>
    </li>

    <li class="nav-item">
        <a href="media.php?content=laporan" class="nav-link <?= ($page=='laporan') ? 'active' : ''; ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i> Laporan
        </a>
    </li>

    <li class="nav-item mt-3 mb-1">
        <span class="text-white-50 small text-uppercase fw-bold ms-3">Lainnya</span>
    </li>

    <li class="nav-item">
        <a href="media.php?content=about" class="nav-link <?= ($page=='about') ? 'active' : ''; ?>">
            <i class="fa-solid fa-circle-info"></i> About
        </a>
    </li>

    <li class="nav-item mt-4">
        <a href="logout.php" class="nav-link text-danger bg-light" onclick="return confirm('Yakin ingin keluar?');">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </li>
</ul>