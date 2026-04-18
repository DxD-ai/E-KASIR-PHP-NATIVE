<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '././koneksi.php';

/** @var mysqli $koneksi */

if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$pesan = '';

function hitungGrandTotalKeranjang(array $keranjang): float
{
    $total = 0;
    foreach ($keranjang as $item) {
        $total += ((float) $item['harga'] * (int) $item['qty']);
    }
    return $total;
}

function redirectTransaksi(string $query = ''): void
{
    echo "<script>window.location.href='media.php?content=transaksi{$query}';</script>";
}

// =============== TAMBAH ITEM ===============
if (isset($_POST['tambah_item'])) {
    $id_barang = (int) ($_POST['id_barang'] ?? 0);
    $jumlah    = max(1, (int) ($_POST['jumlah'] ?? 1));

    $q_barang = mysqli_query(
        $koneksi,
        "SELECT id_barang, kode_barang, nama_barang, harga_jual, stok 
         FROM barang 
         WHERE id_barang='$id_barang' 
         LIMIT 1"
    );
    $d_barang = $q_barang ? mysqli_fetch_assoc($q_barang) : null;

    if (!$d_barang) {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show mb-4'>
            <i class='fa-solid fa-circle-exclamation me-2'></i> Barang tidak ditemukan.
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    } else {
        $qty_di_keranjang = isset($_SESSION['keranjang'][$id_barang])
            ? (int) $_SESSION['keranjang'][$id_barang]['qty']
            : 0;
        $stok_tersedia = (int) $d_barang['stok'];

        if ($stok_tersedia <= 0) {
            $pesan = "<div class='alert alert-warning alert-dismissible fade show mb-4'>
                <i class='fa-solid fa-box-open me-2'></i> Stok untuk <strong>" . htmlspecialchars($d_barang['nama_barang']) . "</strong> habis.
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        } elseif (($qty_di_keranjang + $jumlah) > $stok_tersedia) {
            $pesan = "<div class='alert alert-warning alert-dismissible fade show mb-4'>
                <i class='fa-solid fa-triangle-exclamation me-2'></i> Qty melebihi stok tersedia. Stok saat ini: <strong>{$stok_tersedia}</strong>.
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        } else {
            if (isset($_SESSION['keranjang'][$id_barang])) {
                $_SESSION['keranjang'][$id_barang]['qty'] += $jumlah;
            } else {
                $_SESSION['keranjang'][$id_barang] = [
                    'id'    => (int) $d_barang['id_barang'],
                    'kode'  => $d_barang['kode_barang'],
                    'nama'  => $d_barang['nama_barang'],
                    'harga' => (float) $d_barang['harga_jual'],
                    'qty'   => $jumlah
                ];
            }

            redirectTransaksi();
            return;
        }
    }
}

// =============== HAPUS ITEM ===============
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $id = (int) ($_GET['id'] ?? 0);
    unset($_SESSION['keranjang'][$id]);
    redirectTransaksi();
    return;
}

// =============== RESET KERANJANG ===============
if (isset($_POST['reset_keranjang'])) {
    $_SESSION['keranjang'] = [];
    redirectTransaksi();
    return;
}

$grand_total = hitungGrandTotalKeranjang($_SESSION['keranjang']);

// =============== SIMPAN TRANSAKSI ===============
if (isset($_POST['simpan_transaksi'])) {
    $nama_konsumen_input = trim($_POST['nama_konsumen'] ?? 'Umum');
    $nama_konsumen = $nama_konsumen_input !== '' ? $nama_konsumen_input : 'Umum';
    $nama_konsumen_db = mysqli_real_escape_string($koneksi, $nama_konsumen);
    $tanggal = date('Y-m-d');
    $total_bayar = (float) ($_POST['total_bayar_input'] ?? 0);
    $grand_total = hitungGrandTotalKeranjang($_SESSION['keranjang']);
    $kembalian = $total_bayar - $grand_total;

    if (empty($_SESSION['keranjang'])) {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show mb-4'>
            <i class='fa-solid fa-cart-shopping me-2'></i> Keranjang masih kosong.
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    } elseif ($total_bayar < $grand_total) {
        $pesan = "<div class='alert alert-danger alert-dismissible fade show mb-4'>
            <i class='fa-solid fa-money-bill-wave me-2'></i> Uang bayar kurang dari total tagihan.
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>";
    } else {
        mysqli_begin_transaction($koneksi);

        try {
            // No faktur unik
            do {
                $no_faktur = random_int(100000, 999999);
                $cek_faktur = mysqli_query(
                    $koneksi,
                    "SELECT id_penjualan FROM penjualan WHERE no_faktur='$no_faktur' LIMIT 1"
                );
                if (!$cek_faktur) {
                    throw new Exception(mysqli_error($koneksi));
                }
            } while (mysqli_num_rows($cek_faktur) > 0);

            // INSERT ke tabel penjualan (struktur: bayar)
            $insert_jual = mysqli_query(
                $koneksi,
                "INSERT INTO penjualan (nama_konsumen, tanggal, no_faktur, bayar)
                 VALUES ('$nama_konsumen_db', '$tanggal', '$no_faktur', '$total_bayar')"
            );

            if (!$insert_jual) {
                throw new Exception(mysqli_error($koneksi));
            }

            $id_penjualan = mysqli_insert_id($koneksi);

            // Detail + update stok
            foreach ($_SESSION['keranjang'] as $item) {
                $id_barang = (int) $item['id'];
                $qty       = (int) $item['qty'];

                $cek_stok = mysqli_query(
                    $koneksi,
                    "SELECT stok FROM barang WHERE id_barang='$id_barang' LIMIT 1 FOR UPDATE"
                );
                if (!$cek_stok) {
                    throw new Exception(mysqli_error($koneksi));
                }

                $stok_barang = mysqli_fetch_assoc($cek_stok);
                if (!$stok_barang) {
                    throw new Exception('Barang pada keranjang tidak ditemukan.');
                }

                if ((int) $stok_barang['stok'] < $qty) {
                    throw new Exception('Stok barang tidak mencukupi untuk menyelesaikan transaksi.');
                }

                $insert_detail = mysqli_query(
                    $koneksi,
                    "INSERT INTO detail_penjualan (id_penjualan, id_barang, jumlah_trans)
                     VALUES ('$id_penjualan', '$id_barang', '$qty')"
                );
                if (!$insert_detail) {
                    throw new Exception(mysqli_error($koneksi));
                }

                $update_stok = mysqli_query(
                    $koneksi,
                    "UPDATE barang SET stok = stok - $qty WHERE id_barang='$id_barang'"
                );
                if (!$update_stok) {
                    throw new Exception(mysqli_error($koneksi));
                }
            }

            mysqli_commit($koneksi);
            $_SESSION['keranjang'] = [];

            // LANGSUNG KE HALAMAN STRUK (tidak pakai popup)
            echo "<script>
                alert('Transaksi Berhasil Disimpan!');
                window.location.href = 'cetak.php?id=$id_penjualan';
            </script>";
            return;
        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            $pesan = "<div class='alert alert-danger alert-dismissible fade show mb-4'>
                <i class='fa-solid fa-bug me-2'></i> Transaksi gagal disimpan: " . htmlspecialchars($e->getMessage()) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        }
    }
}
?>

<?= $pesan; ?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card card-rich shadow-sm h-100 border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-cart-plus me-2"></i> Kasir
                </h5>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih Barang</label>
                        <select name="id_barang" class="form-select" required autofocus>
                            <option value="">-- Cari Barang --</option>
                            <?php
                            $barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nama_barang ASC");
                            while ($b = mysqli_fetch_assoc($barang)) {
                                $disabled  = ((int) $b['stok'] <= 0) ? 'disabled' : '';
                                $labelStok = ((int) $b['stok'] <= 0)
                                    ? 'Stok habis'
                                    : 'Stok: ' . number_format($b['stok']);
                                echo "<option value='$b[id_barang]' data-stok='$b[stok]' $disabled>
                                        $b[nama_barang] - Rp " . number_format($b['harga_jual']) . " ($labelStok)
                                      </option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Qty</label>
                        <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
                        <small class="text-muted" id="qty_hint">
                            Pilih barang untuk melihat stok tersedia.
                        </small>
                    </div>
                    <button type="submit" name="tambah_item" class="btn btn-primary w-100 fw-bold">
                        <i class="fa-solid fa-plus me-1"></i> TAMBAH
                    </button>
                </form>
                <form action="" method="POST" class="mt-3">
                    <button type="submit" name="reset_keranjang"
                            class="btn btn-light text-danger w-100 btn-sm border"
                            onclick="return confirm('Reset Keranjang?')">
                        Reset Keranjang
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card card-rich shadow-sm h-100 border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between">
                <h5 class="mb-0 fw-bold text-dark">Keranjang</h5>
                <span class="badge bg-primary rounded-pill">
                    <?= count($_SESSION['keranjang']); ?> Item
                </span>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th class="text-center">Qty</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['keranjang'])) : ?>
                            <?php foreach ($_SESSION['keranjang'] as $id => $val) :
                                $subtotal = $val['harga'] * $val['qty']; ?>
                                <tr>
                                    <td><?= $val['nama']; ?></td>
                                    <td><?= number_format($val['harga']); ?></td>
                                    <td class="text-center"><?= $val['qty']; ?></td>
                                    <td class="fw-bold">Rp <?= number_format($subtotal); ?></td>
                                    <td>
                                        <a href="media.php?content=transaksi&aksi=hapus&id=<?= $id; ?>"
                                           class="text-danger">
                                            <i class="fa-solid fa-xmark"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Keranjang masih kosong.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL BAYAR</td>
                            <td colspan="2" class="fw-bold fs-4 text-primary">
                                Rp <?= number_format($grand_total); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="card-footer bg-white p-4 text-end">
                <button type="button"
                        class="btn btn-success btn-lg px-5 rounded-pill shadow"
                        <?= empty($_SESSION['keranjang']) ? 'disabled' : ''; ?>
                        data-bs-toggle="modal"
                        data-bs-target="#modalBayar">
                    <i class="fa-solid fa-print me-2"></i> BAYAR & CETAK
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBayar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Pembayaran</h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Pelanggan</label>
                        <input type="text" name="nama_konsumen"
                               class="form-control" value="Umum" required>
                    </div>
                    <div class="d-flex justify-content-between mb-2 bg-light p-2 rounded">
                        <span>Total Tagihan:</span>
                        <span class="fw-bold">
                            Rp <?= number_format($grand_total); ?>
                        </span>
                        <input type="hidden" id="grand_total_js"
                               value="<?= $grand_total; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Bayar (Rp)</label>
                        <input type="number" name="total_bayar_input"
                               id="input_bayar"
                               class="form-control fs-4 fw-bold text-success"
                               required min="<?= $grand_total; ?>">
                    </div>
                    <div class="text-center">
                        <small>Kembalian</small>
                        <h3 class="fw-bold text-dark" id="text_kembalian">
                            Rp 0
                        </h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit"
                            name="simpan_transaksi"
                            class="btn btn-success w-100 fw-bold">
                        PROSES TRANSAKSI
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const selectBarang = document.querySelector('select[name="id_barang"]');
    const inputQtyBarang = document.querySelector('input[name="jumlah"]');
    const qtyHint = document.getElementById('qty_hint');

    function updateQtyMax() {
        if (!selectBarang || !inputQtyBarang || !qtyHint) {
            return;
        }

        const selectedOption = selectBarang.options[selectBarang.selectedIndex];
        const stok = Number(selectedOption?.dataset?.stok || 0);

        if (stok > 0) {
            inputQtyBarang.max = stok;
            if (Number(inputQtyBarang.value) > stok) {
                inputQtyBarang.value = stok;
            }
            qtyHint.textContent = "Stok tersedia: " + stok;
        } else {
            inputQtyBarang.removeAttribute('max');
            qtyHint.textContent = "Pilih barang untuk melihat stok tersedia.";
        }
    }

    if (selectBarang) {
        selectBarang.addEventListener('change', updateQtyMax);
        updateQtyMax();
    }

    const inputBayar  = document.getElementById('input_bayar');
    const totalTagihan = Number(document.getElementById('grand_total_js').value || 0);
    const textKembalian = document.getElementById('text_kembalian');

    if (inputBayar) {
        inputBayar.addEventListener('input', function () {
            let bayar = Number(this.value || 0);
            let kembalian = bayar - totalTagihan;
            if (kembalian < 0) {
                textKembalian.innerText = "Kurang Rp " + Math.abs(kembalian).toLocaleString('id-ID');
                textKembalian.classList.add('text-danger');
            } else {
                textKembalian.innerText = "Rp " + kembalian.toLocaleString('id-ID');
                textKembalian.classList.remove('text-danger');
            }
        });
    }
</script>