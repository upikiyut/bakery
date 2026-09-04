<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/helpers.php';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $id=(int)$_POST['id_pesanan'];
    $status=$_POST['status_pesanan'];
    $allowed=['menunggu','diproses','selesai','dibatalkan'];

    if(in_array($status,$allowed,true)){
        $s=$conn->prepare('UPDATE pesanan SET status_pesanan=? WHERE id_pesanan=?');
        $s->bind_param('si',$status,$id);
        $s->execute();
        $_SESSION['flash']='Status pesanan berhasil disimpan.';
    } else {
        $_SESSION['flash']='Status pesanan tidak valid.';
    }

    redirect('pesanan.php');
}

$page_title='Kelola Pesanan | Bunéa Bakery';
require __DIR__.'/partials/header.php';

/*
 * Ambil data pesanan sekaligus daftar produk yang dipesan.
 * Format daftar_pesanan contoh:
 * "Lemon Honey Cake × 2, Tiramisu Velvet Cake × 1"
 */
$rows=$conn->query("
    SELECT
        p.*,
        pl.nama_pelanggan,
        pl.no_telepon,
        py.status_pembayaran,
        py.metode_pembayaran,
        py.jumlah_bayar,
        GROUP_CONCAT(
            CONCAT(dp.nama_kue, ' × ', dp.jumlah)
            ORDER BY dp.id_detail ASC
            SEPARATOR ', '
        ) AS daftar_pesanan
    FROM pesanan p
    JOIN pelanggan pl ON pl.id_pelanggan=p.id_pelanggan
    LEFT JOIN pembayaran py ON py.id_pesanan=p.id_pesanan
    LEFT JOIN (
        SELECT
            d.id_detail,
            d.id_pesanan,
            d.jumlah,
            k.nama_kue
        FROM detail_pesanan d
        JOIN produk_kue k ON k.id_produk=d.id_produk
    ) dp ON dp.id_pesanan=p.id_pesanan
    GROUP BY
        p.id_pesanan,
        p.id_pelanggan,
        p.tanggal_pesanan,
        p.total_harga,
        p.status_pesanan,
        pl.nama_pelanggan,
        pl.no_telepon,
        py.status_pembayaran,
        py.metode_pembayaran,
        py.jumlah_bayar
    ORDER BY p.id_pesanan DESC
");
?>

<div class="mb-4">
    <div class="section-kicker">ORDER MANAGEMENT</div>
    <h1 class="h2 fw-bold mb-1">Kelola Pesanan</h1>
    <p class="text-muted">Pantau produk yang dipesan pelanggan dan ubah status pesanan.</p>
</div>

<div class="admin-card p-3 p-md-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Nama Pesanan</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if($rows && $rows->num_rows): ?>
                    <?php while($r=$rows->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <strong>#<?= (int)$r['id_pesanan'] ?></strong>
                            </td>

                            <td>
                                <strong><?= e($r['nama_pelanggan']) ?></strong><br>
                                <small class="text-muted"><?= e($r['no_telepon']) ?></small>
                            </td>

                            <td>
                                <?= date('d/m/Y H:i',strtotime($r['tanggal_pesanan'])) ?>
                            </td>

                            <td style="min-width:240px;">
                                <?php if(!empty($r['daftar_pesanan'])): ?>
                                    <div class="fw-semibold">
                                        <?= e($r['daftar_pesanan']) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Detail pesanan tidak tersedia.</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?= rupiah($r['total_harga']) ?>
                            </td>

                            <td>
                                <div><strong><?= e(ucfirst($r['status_pembayaran'] ?? 'belum lunas')) ?></strong></div>
                                <?php if(($r['status_pembayaran'] ?? '') === 'lunas'): ?>
                                    <small class="text-muted"><?= e($r['metode_pembayaran'] ?? '-') ?></small><br>
                                    <small><?= rupiah($r['jumlah_bayar'] ?? 0) ?></small>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="badge rounded-pill text-bg-light">
                                    <?= e(ucfirst($r['status_pesanan'])) ?>
                                </span>
                            </td>

                            <td>
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="id_pesanan" value="<?= (int)$r['id_pesanan'] ?>">

                                    <select name="status_pesanan" class="form-select form-select-sm">
                                        <option value="menunggu" <?= $r['status_pesanan']==='menunggu'?'selected':'' ?>>Menunggu</option>
                                        <option value="diproses" <?= $r['status_pesanan']==='diproses'?'selected':'' ?>>Diproses</option>
                                        <option value="selesai" <?= $r['status_pesanan']==='selesai'?'selected':'' ?>>Selesai</option>
                                        <option value="dibatalkan" <?= $r['status_pesanan']==='dibatalkan'?'selected':'' ?>>Dibatalkan</option>
                                    </select>

                                    <button class="btn btn-dark-bunea btn-sm">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            Belum ada pesanan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__.'/partials/footer.php'; ?>
