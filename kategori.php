<?php
// 1. KONEKSI AMAN (Path Absolut)
$path_koneksi = $_SERVER['DOCUMENT_ROOT'] . "/syadan/koneksi.php";
if (file_exists($path_koneksi)) {
    include $path_koneksi;
} else {
    include "../koneksi.php"; 
}

// Cek Koneksi
if (!$koneksi) {
    die("<div class='alert alert-danger'>Koneksi database gagal!</div>");
}

$pesan = "";

// A. TAMBAH DATA KATEGORI
if (isset($_POST['simpan_kategori'])) {
    $nama = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['nama_kategori']));
    $merk = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['merk']));
    $rak  = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['kode_rak']));

    $query = mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori, merk, kode_rak) VALUES ('$nama', '$merk', '$rak')");
    if ($query) {
        $pesan = "<div class='alert alert-success alert-dismissible fade show'>
            <i class='fa-solid fa-check-circle me-2'></i> Data berhasil ditambahkan!
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
}

// B. EDIT DATA KATEGORI
if (isset($_POST['update_kategori'])) {
    $id   = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['nama_kategori']));
    $merk = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['merk']));
    $rak  = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['kode_rak']));

    $query = mysqli_query($koneksi, "UPDATE kategori SET nama_kategori='$nama', merk='$merk', kode_rak='$rak' WHERE id_kategori='$id'");
    if ($query) {
        $pesan = "<div class='alert alert-primary alert-dismissible fade show'>
            <i class='fa-solid fa-check-double me-2'></i> Data berhasil diperbarui!
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    }
}

// C. HAPUS DATA KATEGORI
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $query = mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori='$id'");
    if ($query) {
        mysqli_query($koneksi, "ALTER TABLE kategori AUTO_INCREMENT = 1");
        echo "<script>window.location.href='media.php?content=kategori&msg=deleted';</script>";
        exit;
    }
}

// D. IMPORT KATEGORI DARI CSV (GOOGLE SHEETS)
if (isset($_POST['import_kategori'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    $handle = fopen($file, "r");
    $berhasil = 0;

    fgetcsv($handle, 1000, ","); // Lewati baris judul/header

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $nama_kat = mysqli_real_escape_string($koneksi, htmlspecialchars($data[0]));
        $merk_kat = mysqli_real_escape_string($koneksi, htmlspecialchars($data[1]));
        $rak_kat  = mysqli_real_escape_string($koneksi, htmlspecialchars($data[2]));

        if (!empty($nama_kat)) {
            $ins = mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori, merk, kode_rak) VALUES ('$nama_kat', '$merk_kat', '$rak_kat')");
            if ($ins) $berhasil++;
        }
    }
    fclose($handle);
    $pesan = "<div class='alert alert-info'>Selesai! <b>$berhasil</b> kategori berhasil di-import.</div>";
}

// Notifikasi Hapus
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $pesan = "<div class='alert alert-warning alert-dismissible fade show'>
        <i class='fa-solid fa-trash-can me-2'></i> Data berhasil dihapus!
        <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
    </div>";
}
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">
                <i class="fa-solid fa-layer-group text-primary me-2"></i> Data Kategori
            </h3>
            <div>
                <button type="button" class="btn btn-success rounded-pill shadow-sm px-4 me-2" data-bs-toggle="modal" data-bs-target="#modalImportKat">
                    <i class="fa-solid fa-file-import me-1"></i> Import CSV
                </button>
                <button type="button" class="btn btn-primary rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Baru
                </button>
            </div>
        </div>

        <?= $pesan; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Kategori</th>
                                <th>Merk / Brand</th>
                                <th>Kode Rak</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $tampil = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY id_kategori ASC");
                            while ($data = mysqli_fetch_assoc($tampil)) :
                            ?>
                            <tr>
                                <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                                <td class="fw-semibold text-primary"><?= $data['nama_kategori']; ?></td>
                                <td><?= $data['merk']; ?></td>
                                <td><span class="badge bg-secondary"><?= $data['kode_rak']; ?></span></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning text-white me-1 btn-edit"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        data-id="<?= $data['id_kategori']; ?>"
                                        data-nama="<?= $data['nama_kategori']; ?>"
                                        data-merk="<?= $data['merk']; ?>"
                                        data-rak="<?= $data['kode_rak']; ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-hapus"
                                        data-bs-toggle="modal" data-bs-target="#modalHapus"
                                        data-id="<?= $data['id_kategori']; ?>"
                                        data-nama="<?= $data['nama_kategori']; ?>">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
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

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: Ban" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Merk</label>
                        <input type="text" name="merk" class="form-control" placeholder="Contoh: FDR" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Rak</label>
                        <input type="text" name="kode_rak" class="form-control" placeholder="A01" required maxlength="3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan_kategori" class="btn btn-primary w-100">SIMPAN DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div class="modal fade" id="modalImportKat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Import Kategori (CSV)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-warning small">Urutan Kolom Sheets: <b>Nama_Kategori, Merk, Kode_Rak</b></div>
                    <input type="file" name="file_csv" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="import_kategori" class="btn btn-success w-100">MULAI IMPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_kategori" id="edit-id">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Kategori</label>
                        <input type="text" name="nama_kategori" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Merk</label>
                        <input type="text" name="merk" id="edit-merk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Rak</label>
                        <input type="text" name="kode_rak" id="edit-rak" class="form-control" required maxlength="3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_kategori" class="btn btn-warning text-white w-100">UPDATE DATA</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fa-solid fa-circle-exclamation fa-4x text-danger mb-3"></i>
                <h4 class="fw-bold">Hapus Data?</h4>
                <p class="text-muted">Kategori <span id="nama-hapus" class="fw-bold text-dark"></span> akan dihapus.</p>
                <div class="mt-4">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="link-hapus" class="btn btn-danger px-4">Ya, Hapus!</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-edit').on('click', function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-nama').val($(this).data('nama'));
        $('#edit-merk').val($(this).data('merk'));
        $('#edit-rak').val($(this).data('rak'));
    });

    $('.btn-hapus').on('click', function() {
        $('#nama-hapus').text($(this).data('nama'));
        $('#link-hapus').attr('href', 'media.php?content=kategori&hapus=' + $(this).data('id'));
    });
});
</script>