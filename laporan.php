<?php
// 1. Perbaikan Path Koneksi
include $_SERVER['DOCUMENT_ROOT'] . "/syadan/koneksi.php";

// 2. Filter Tanggal (Default: Awal bulan ini sampai hari ini)
$tgl_awal  = isset($_GET['tgl_awal']) ? $_GET['tgl_awal'] : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-chart-line text-primary me-2"></i> Laporan Laba Rugi
            </h3>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET">
                    <input type="hidden" name="content" value="laporan"> 
                    
                    <div class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label class="fw-bold small text-muted mb-1">Dari Tanggal</label>
                            <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold small text-muted mb-1">Sampai Tanggal</label>
                            <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir; ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-filter me-1"></i> Tampilkan
                            </button>
                        </div>
                        <div class="col-md-4 text-end">
                            
<a href="cetak_laporan.php?tgl_awal=<?= $tgl_awal; ?>&tgl_akhir=<?= $tgl_akhir; ?>" target="_blank" class="btn btn-success px-4">
    <i class="fa-solid fa-print me-2"></i> CETAK LAPORAN
</a>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Tanggal</th>
                                <th>Faktur</th>
                                <th>Total Modal</th>
                                <th>Total Jual (Omset)</th>
                                <th>Keuntungan (Laba)</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $grand_modal = 0;
                            $grand_jual  = 0;
                            $grand_laba  = 0;

                            // Gunakan tabel 'laporan' sesuai database syadandb
                            $query = mysqli_query($koneksi, "SELECT * FROM laporan 
                                     WHERE tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                     ORDER BY id_laporan DESC");

                            if (mysqli_num_rows($query) == 0) {
                                echo "<tr><td colspan='7' class='text-center py-5 text-muted'>Tidak ada data transaksi pada periode ini.</td></tr>";
                            }

                            while ($data = mysqli_fetch_assoc($query)) :
                                $grand_modal += $data['total_modal'];
                                $grand_jual  += $data['total_jual'];
                                $grand_laba  += $data['laba'];
                            ?>
                            <tr>
                                <td class="text-muted"><?= $no++; ?></td>
                                <td><?= date('d/m/Y', strtotime($data['tanggal'])); ?></td>
                                <td><span class="badge bg-dark">#<?= $data['faktur']; ?></span></td>
                                <td class="text-danger">Rp <?= number_format($data['total_modal'], 0, ',', '.'); ?></td>
                                <td class="fw-bold text-primary">Rp <?= number_format($data['total_jual'], 0, ',', '.'); ?></td>
                                <td class="fw-bold text-success">Rp <?= number_format($data['laba'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <a href="print_struk.php?id=<?= $data['id_laporan']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-print"></i> Struk
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot class="table-dark">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-center text-uppercase">Ringkasan Laba Rugi</td>
                                <td class="text-warning">Rp <?= number_format($grand_modal, 0, ',', '.'); ?></td>
                                <td class="text-info">Rp <?= number_format($grand_jual, 0, ',', '.'); ?></td>
                                <td class="bg-success text-white">Rp <?= number_format($grand_laba, 0, ',', '.'); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>