<?php
// 1. Perbaikan Path Koneksi
$path_koneksi = $_SERVER['DOCUMENT_ROOT'] . '/syadan/koneksi.php';
if (file_exists($path_koneksi)) {
    include $path_koneksi;
} else {
    die("<div class='alert alert-danger'>Error: File koneksi.php tidak ditemukan!</div>");
}

// 2. QUERY: Hitung Keuangan Langsung dari tabel LAPORAN
// Ini kunci supaya Dashboard sinkron dengan halaman Laporan
$query_laporan = mysqli_query($koneksi, "
    SELECT 
        SUM(total_modal) as modal_akhir, 
        SUM(total_jual) as jual_akhir, 
        SUM(laba) as laba_akhir,
        COUNT(id_laporan) as total_nota
    FROM laporan
");

$data_db = mysqli_fetch_assoc($query_laporan);

// Set variabel dengan pengaman (jika null jadi 0)
$total_modal  = $data_db['modal_akhir'] ?? 0;
$total_jual   = $data_db['jual_akhir'] ?? 0;
$laba_bersih  = $data_db['laba_akhir'] ?? 0;
$jml_transaksi = $data_db['total_nota'] ?? 0;

// Hitung jumlah jenis barang dari tabel barang
$q_jml_brg = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM barang");
$jml_barang = mysqli_fetch_assoc($q_jml_brg)['total'] ?? 0;
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card p-4 text-white shadow-sm border-0" 
             style="background: linear-gradient(135deg, #0d6efd 0%, #0043a8 100%); border-radius: 15px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">Dashboard Keuangan 📈</h2>
                    <p class="mb-0 opacity-75">Ringkasan performa bisnis Anda secara real-time.</p>
                </div>
                <div class="d-none d-md-block">
                    <i class="fa-solid fa-chart-pie fa-4x opacity-25"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger me-3">
                        <i class="fa-solid fa-hand-holding-dollar fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Modal</h6>
                        <small class="text-muted">Aset Barang Terjual</small>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">Rp <?= number_format($total_modal, 0, ',', '.'); ?></h3>
            </div>
            <div class="card-footer bg-white border-0 py-2">
                <small class="text-danger"><i class="fa-solid fa-arrow-down"></i> Pengeluaran Pokok</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary me-3">
                        <i class="fa-solid fa-cash-register fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Total Omset</h6>
                        <small class="text-muted">Pendapatan Kotor</small>
                    </div>
                </div>
                <h3 class="fw-bold text-dark mb-0">Rp <?= number_format($total_jual, 0, ',', '.'); ?></h3>
            </div>
            <div class="card-footer bg-white border-0 py-2">
                <small class="text-primary"><i class="fa-solid fa-arrow-up"></i> Uang Masuk</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden" style="border-radius: 12px;">
            <div class="position-absolute end-0 top-0 p-3 opacity-10">
                <i class="fa-solid fa-coins fa-5x text-success"></i>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success me-3">
                        <i class="fa-solid fa-chart-line fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold small mb-1">Laba Bersih</h6>
                        <small class="text-muted">Keuntungan Real</small>
                    </div>
                </div>
                <h3 class="fw-bold text-success mb-0">Rp <?= number_format($laba_bersih, 0, ',', '.'); ?></h3>
            </div>
            <div class="card-footer bg-white border-0 py-2">
                <small class="text-success fw-bold"><i class="fa-solid fa-check-circle"></i> Profit Didapat</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold">Total Transaksi</h6>
                    <h2 class="fw-bold mb-0"><?= $jml_transaksi; ?> <small class="fs-6 text-muted">Nota</small></h2>
                </div>
                <i class="fa-solid fa-receipt fa-3x text-secondary opacity-25"></i>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold">Stok Jenis Barang</h6>
                    <h2 class="fw-bold mb-0"><?= $jml_barang; ?> <small class="fs-6 text-muted">Item</small></h2>
                </div>
                <i class="fa-solid fa-boxes-stacked fa-3x text-warning opacity-50"></i>
            </div>
        </div>
    </div>
</div>