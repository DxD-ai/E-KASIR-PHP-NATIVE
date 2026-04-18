<?php
include "koneksi.php";
$id = $_GET['id'] ?? "";
if(!$id) die("ID Tidak Ditemukan");

$q_p = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE id_penjualan = '$id'");
$p = mysqli_fetch_assoc($q_p);
if(!$p) die("Data Penjualan Kosong!");

$faktur = $p['no_faktur'];
$q_l = mysqli_query($koneksi, "SELECT total_jual FROM laporan WHERE faktur = '$faktur'");
$l = mysqli_fetch_assoc($q_l);
$total = $l['total_jual'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk <?=$faktur?></title>
    <style>
        body { font-family: monospace; width: 250px; margin: auto; padding: 10px; font-size: 12px; }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 5px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print();">
    <div class="text-center"><strong>BENGKEL SYADAN</strong><br>Cigombong, Sukabumi</div>
    <div class="line"></div>
    No: <?=$faktur?><br>Tgl: <?=$p['tanggal']?><br>Ksm: <?=$p['nama_konsumen']?>
    <div class="line"></div>
    <table width="100%">
        <?php
        $det = mysqli_query($koneksi, "SELECT dp.*, b.nama_barang, b.harga_jual FROM detail_penjualan dp JOIN barang b ON dp.id_barang = b.id_barang WHERE dp.id_penjualan = '$id'");
        while($i = mysqli_fetch_assoc($det)):
        ?>
        <tr><td><?=$i['nama_barang']?></td><td align="right"><?=$i['jumlah_trans']?> x <?=number_format($i['harga_jual'])?></td></tr>
        <?php endwhile; ?>
    </table>
    <div class="line"></div>
    <table width="100%" style="font-weight: bold;">
        <tr><td>TOTAL</td><td align="right"><?=number_format($total)?></td></tr>
        <tr><td>BAYAR</td><td align="right"><?=number_format($p['bayar'])?></td></tr>
        <tr><td>KEMBALI</td><td align="right"><?=number_format($p['bayar'] - $total)?></td></tr>
    </table>
    <div class="line"></div>
    <div class="text-center">-- TERIMA KASIH --</div>
    <div class="no-print text-center"><br><button onclick="window.close()">TUTUP</button></div>
</body>
</html>