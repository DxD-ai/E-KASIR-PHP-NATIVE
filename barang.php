<?php
include $_SERVER['DOCUMENT_ROOT'] . "/syadan/koneksi.php";

$pesan = "";
$limit_stok = 5;

// --- A. TAMBAH BARANG ---
if (isset($_POST['simpan_barang'])) {
    $kategori = $_POST['id_kategori'];
    $kode     = htmlspecialchars($_POST['kode_barang']);
    $nama     = htmlspecialchars($_POST['nama_barang']);
    $beli     = $_POST['harga_beli'];
    $jual     = $_POST['harga_jual'];
    $satuan   = htmlspecialchars($_POST['satuan']);
    $stok     = $_POST['stok'];

    $cek_kode = mysqli_query($koneksi, "SELECT kode_barang FROM barang WHERE kode_barang = '$kode'");
    if (mysqli_num_rows($cek_kode) > 0) {
        $pesan = "<div class='alert alert-danger'>Gagal! Kode <b>$kode</b> sudah ada.</div>";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO barang (id_kategori, kode_barang, nama_barang, harga_beli, harga_jual, satuan, stok) 
                 VALUES ('$kategori', '$kode', '$nama', '$beli', '$jual', '$satuan', '$stok')");
        if ($query) { $pesan = "<div class='alert alert-success'>Barang berhasil ditambah!</div>"; }
    }
}

// --- B. EDIT BARANG ---
if (isset($_POST['update_barang'])) {
    $id = $_POST['id_barang'];
    $kategori = $_POST['id_kategori'];
    $nama = htmlspecialchars($_POST['nama_barang']);
    $beli = $_POST['harga_beli'];
    $jual = $_POST['harga_jual'];
    $satuan = htmlspecialchars($_POST['satuan']);
    $stok = $_POST['stok'];

    $query = mysqli_query($koneksi, "UPDATE barang SET id_kategori='$kategori', nama_barang='$nama', harga_beli='$beli', harga_jual='$jual', satuan='$satuan', stok='$stok' WHERE id_barang='$id'");
    if ($query) { $pesan = "<div class='alert alert-primary'>Data diperbarui!</div>"; }
}

// --- C. HAPUS BARANG ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang='$id'");
    echo "<script>window.location.href='media.php?content=barang';</script>";
}

// --- D. IMPORT CSV ---
if (isset($_POST['import_csv'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    $handle = fopen($file, "r");
    $berhasil = 0; $gagal = 0;
    fgetcsv($handle, 1000, ","); // Lewati header

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $kode_csv   = htmlspecialchars($data[0]);
        $nama_csv   = htmlspecialchars($data[1]);
        $kat_csv    = $data[2]; 
        $beli_csv   = $data[3];
        $jual_csv   = $data[4];
        $satuan_csv = htmlspecialchars($data[5]);
        $stok_csv   = $data[6];

        if(!empty($kode_csv)){
            $cek = mysqli_query($koneksi, "SELECT kode_barang FROM barang WHERE kode_barang = '$kode_csv'");
            if (mysqli_num_rows($cek) == 0) {
                $ins = mysqli_query($koneksi, "INSERT INTO barang (id_kategori, kode_barang, nama_barang, harga_beli, harga_jual, satuan, stok) 
                       VALUES ('$kat_csv', '$kode_csv', '$nama_csv', '$beli_csv', '$jual_csv', '$satuan_csv', '$stok_csv')");
                if ($ins) $berhasil++;
            } else { $gagal++; }
        }
    }
    fclose($handle);
    $pesan = "<div class='alert alert-info'>Import Selesai! <b>$berhasil</b> sukses, <b>$gagal</b> duplikat.</div>";
}

// --- E. LOGIKA PENCARIAN ---
$keyword = "";
$condition = "";
if (isset($_POST['cari'])) {
    $keyword = htmlspecialchars($_POST['keyword']);
    $condition = " WHERE (barang.nama_barang LIKE '%$keyword%' OR barang.kode_barang LIKE '%$keyword%') ";
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-md-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fa-solid fa-box text-primary me-2"></i> Inventaris Sparepart</h3>
            <div class="mt-2 mt-md-0">
                <button class="btn btn-success rounded-pill px-3 me-2" data-bs-toggle="modal" data-bs-target="#modalImport">
                    <i class="fa-solid fa-file-import me-1"></i> Import CSV
                </button>
                <button class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa-solid fa-plus me-1"></i> Tambah
                </button>
            </div>
        </div>

        <?= $pesan; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <form action="" method="POST" class="row g-2">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" name="keyword" class="form-control border-start-0" placeholder="Cari nama atau kode sparepart..." value="<?= $keyword; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="cari" class="btn btn-dark w-100">Cari Barang</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Sparepart</th>
                                <th>Stok</th>
                                <th>Harga Jual</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q = "SELECT barang.*, kategori.nama_kategori FROM barang 
                                  JOIN kategori ON barang.id_kategori = kategori.id_kategori 
                                  $condition 
                                  ORDER BY id_barang DESC";
                            $tampil = mysqli_query($koneksi, $q);
                            if(mysqli_num_rows($tampil) == 0) echo "<tr><td colspan='5' class='text-center p-4'>Data tidak ditemukan.</td></tr>";
                            
                            while ($data = mysqli_fetch_assoc($tampil)) :
                                $is_low = ($data['stok'] <= $limit_stok);
                            ?>
                            <tr>
                                <td><span class="badge bg-dark"><?= $data['kode_barang']; ?></span></td>
                                <td>
                                    <div class="fw-bold"><?= $data['nama_barang']; ?></div>
                                    <small class="text-muted"><?= $data['nama_kategori']; ?></small>
                                </td>
                                <td>
                                    <span class="badge <?= $is_low ? 'bg-danger' : 'bg-success'; ?>">
                                        <?= $data['stok']; ?> <?= $data['satuan']; ?>
                                    </span>
                                    <?= $is_low ? '<br><small class="text-danger fw-bold">Menipis!</small>' : ''; ?>
                                </td>
                                <td class="fw-bold">Rp <?= number_format($data['harga_jual'], 0, ',', '.'); ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning text-white btn-edit" 
                                        data-bs-toggle="modal" data-bs-target="#modalEdit" 
                                        data-id="<?= $data['id_barang']; ?>" data-nama="<?= $data['nama_barang']; ?>" 
                                        data-kode="<?= $data['kode_barang']; ?>" data-beli="<?= $data['harga_beli']; ?>" 
                                        data-jual="<?= $data['harga_jual']; ?>" data-satuan="<?= $data['satuan']; ?>" 
                                        data-stok="<?= $data['stok']; ?>" data-kategori="<?= $data['id_kategori']; ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="media.php?content=barang&hapus=<?= $data['id_barang']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImport" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import dari CSV</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info small">
                        Format Kolom: <b>Kode, Nama, ID_Kat, Harga_Beli, Harga_Jual, Satuan, Stok</b>
                    </div>
                    <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="import_csv" class="btn btn-success w-100">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Sparepart</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label fw-bold">Kode</label><input type="text" name="kode_barang" class="form-control" required></div>
                        <div class="col-md-8"><label class="form-label fw-bold">Nama</label><input type="text" name="nama_barang" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Kategori</label>
                            <select name="id_kategori" class="form-select">
                                <?php $kat = mysqli_query($koneksi, "SELECT * FROM kategori"); while($k = mysqli_fetch_assoc($kat)) echo "<option value='$k[id_kategori]'>$k[nama_kategori]</option>"; ?>
                            </select>
                        </div>
                        <div class="col-md-3"><label class="form-label fw-bold">Satuan</label><input type="text" name="satuan" class="form-control" required></div>
                        <div class="col-md-3"><label class="form-label fw-bold">Stok Awal</label><input type="number" name="stok" class="form-control" value="0" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Harga Beli</label><input type="number" name="harga_beli" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label fw-bold">Harga Jual</label><input type="number" name="harga_jual" class="form-control" required></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" name="simpan_barang" class="btn btn-primary w-100">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.btn-edit').on('click', function(){
        $('#edit-id').val($(this).data('id'));
        $('#edit-nama').val($(this).data('nama'));
        $('#edit-beli').val($(this).data('beli'));
        $('#edit-jual').val($(this).data('jual'));
        $('#edit-satuan').val($(this).data('satuan'));
        $('#edit-stok').val($(this).data('stok'));
        $('#edit-kategori').val($(this).data('kategori'));
    });
});
</script>