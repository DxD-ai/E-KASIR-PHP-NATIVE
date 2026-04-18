<?php
include "koneksi.php";

$tgl_awal  = isset($_GET['tgl_awal'])  ? $_GET['tgl_awal']  : date('Y-m-01');
$tgl_akhir = isset($_GET['tgl_akhir']) ? $_GET['tgl_akhir'] : date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan - Bengkel Syadan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 16px; }
        .header p { margin: 4px 0 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background: #f2f2f2; }
        .text-right { text-align: right; }
        tfoot tr { background: #eee; font-weight: bold; }
        @media print {
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN PENJUALAN & KEUNTUNGAN</h2>
        <p>Bengkel Syadan</p>
        <p>Periode: <?= date('d/m/Y', strtotime($tgl_awal)); ?> s/d <?= date('d/m/Y', strtotime($tgl_akhir)); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>No Faktur</th>
                <th>Total Modal</th>
                <th>Total Jual</th>
                <th>Keuntungan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1; $tm = 0; $tj = 0; $tl = 0;

            $sql = "SELECT * FROM laporan 
                    WHERE DATE(tanggal) BETWEEN '$tgl_awal' AND '$tgl_akhir'
                    ORDER BY tanggal ASC";

            $query = mysqli_query($koneksi, $sql);

            if ($query && mysqli_num_rows($query) > 0) {
                while ($r = mysqli_fetch_assoc($query)) {
                    $laba = $r['laba'];
                    $tm  += $r['total_modal'];
                    $tj  += $r['total_jual'];
                    $tl  += $laba;
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tanggal'])); ?></td>
                    <td><?= $r['faktur']; ?></td>
                    <td class="text-right">Rp <?= number_format($r['total_modal'], 0, ',', '.'); ?></td>
                    <td class="text-right">Rp <?= number_format($r['total_jual'],  0, ',', '.'); ?></td>
                    <td class="text-right">Rp <?= number_format($laba,             0, ',', '.'); ?></td>
                </tr>
            <?php
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center'>Data tidak ditemukan untuk periode " . date('d/m/Y', strtotime($tgl_awal)) . " s/d " . date('d/m/Y', strtotime($tgl_akhir)) . "</td></tr>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3">TOTAL</td>
                <td class="text-right">Rp <?= number_format($tm, 0, ',', '.'); ?></td>
                <td class="text-right">Rp <?= number_format($tj, 0, ',', '.'); ?></td>
                <td class="text-right">Rp <?= number_format($tl, 0, ',', '.'); ?></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>