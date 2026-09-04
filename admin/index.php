<?php
require_once __DIR__ . '/../config/koneksi.php';
$page_title = 'Dashboard Admin | Bunéa Bakery';
require __DIR__ . '/partials/header.php';

$total_produk = (int) $conn->query('SELECT COUNT(*) total FROM produk_kue')->fetch_assoc()['total'];
$total_pelanggan = (int) $conn->query('SELECT COUNT(*) total FROM pelanggan')->fetch_assoc()['total'];
$total_pesanan = (int) $conn->query('SELECT COUNT(*) total FROM pesanan')->fetch_assoc()['total'];
$total_pendapatan = (float) $conn->query("SELECT COALESCE(SUM(total_harga),0) total FROM pesanan WHERE status_pesanan IN ('diproses','selesai')")->fetch_assoc()['total'];
$pesanan = $conn->query("SELECT p.*, pl.nama_pelanggan FROM pesanan p JOIN pelanggan pl ON pl.id_pelanggan=p.id_pelanggan ORDER BY p.id_pesanan DESC LIMIT 8");
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="section-kicker">ADMIN PANEL</div>
        <h1 class="display-6 fw-bold mb-1">Dashboard Bunéa Bakery</h1>
        <p class="text-muted mb-0">Kelola produk dan pesanan bakery dengan mudah.</p>
    </div>
    <a href="produk_tambah.php" class="btn btn-register">+ Tambah Cake</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3"><div class="stat-card"><small>Total Produk</small><strong><?= $total_produk ?></strong><span>cake tersedia</span></div></div>
    <div class="col-md-6 col-xl-3"><div class="stat-card"><small>Total Pelanggan</small><strong><?= $total_pelanggan ?></strong><span>akun terdaftar</span></div></div>
    <div class="col-md-6 col-xl-3"><div class="stat-card"><small>Total Pesanan</small><strong><?= $total_pesanan ?></strong><span>pesanan masuk</span></div></div>
    <div class="col-md-6 col-xl-3"><div class="stat-card"><small>Pendapatan</small><strong><?= rupiah($total_pendapatan) ?></strong><span>diproses + selesai</span></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Pesanan Terbaru</h2>
                <a href="pesanan.php" class="btn btn-soft btn-sm">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php if ($pesanan->num_rows): while ($row = $pesanan->fetch_assoc()): ?>
                        <tr>
                            <td><?= e($row['nama_pelanggan']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pesanan'])) ?></td>
                            <td><?= rupiah($row['total_harga']) ?></td>
                            <td><span class="badge rounded-pill text-bg-light"><?= e(ucfirst($row['status_pesanan'])) ?></span></td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card p-4 h-100">
            <h2 class="h5">Menu Admin</h2>
            <div class="d-grid gap-2 mt-3">
                <a href="produk.php" class="btn btn-soft text-start">🍰 Kelola Produk</a>
                <a href="produk_tambah.php" class="btn btn-soft text-start">➕ Tambah Produk</a>
                <a href="pesanan.php" class="btn btn-soft text-start">📦 Kelola Pesanan</a>
                <a href="../index.php" class="btn btn-register text-start">♡ Buka Website Pelanggan</a>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
