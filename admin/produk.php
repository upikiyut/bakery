<?php
require_once __DIR__ . '/../config/koneksi.php';
$page_title = 'Kelola Produk | Bunéa Bakery';
require __DIR__ . '/partials/header.php';
$produk = $conn->query('SELECT * FROM produk_kue ORDER BY id_produk DESC');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><div class="section-kicker">CATALOG</div><h1 class="h2 fw-bold mb-1">Kelola Cake</h1><p class="text-muted mb-0">Tambah, edit, dan hapus produk.</p></div>
    <a href="produk_tambah.php" class="btn btn-register">+ Tambah Cake</a>
</div>
<div class="admin-card p-3 p-md-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Foto</th><th>Nama Cake</th><th>Jenis</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
            <tbody>
            <?php while ($row = $produk->fetch_assoc()): ?>
                <tr>
                    <td><img src="../uploads/<?= e($row['foto_kue']) ?>" class="admin-product-img" alt="<?= e($row['nama_kue']) ?>"></td>
                    <td class="fw-semibold"><?= e($row['nama_kue']) ?></td>
                    <td><?= e($row['jenis_kue']) ?></td>
                    <td><?= rupiah($row['harga']) ?></td>
                    <td><?= (int)$row['stok'] ?> pcs</td>
                    <td>
                        <a href="produk_edit.php?id=<?= (int)$row['id_produk'] ?>" class="btn btn-soft btn-sm">Edit</a>
                        <a href="produk_hapus.php?id=<?= (int)$row['id_produk'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
