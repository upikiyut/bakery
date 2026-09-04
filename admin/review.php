<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/helpers.php';
wajib_admin();

if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM review WHERE id_review = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $_SESSION['flash'] = 'Review berhasil dihapus.';
    redirect('review.php');
}

$review = $conn->query("SELECT r.*, p.nama_kue, pel.nama_pelanggan, ps.id_pesanan FROM review r JOIN produk_kue p ON p.id_produk=r.id_produk JOIN pelanggan pel ON pel.id_pelanggan=r.id_pelanggan JOIN pesanan ps ON ps.id_pesanan=r.id_pesanan ORDER BY r.tanggal_review DESC");
$page_title = 'Kelola Review | Bunéa Bakery';
require __DIR__ . '/partials/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <div class="section-kicker">CUSTOMER VOICE</div>
        <h1 class="h2 fw-bold mb-1">Review Pelanggan</h1>
        <p class="text-muted mb-0">Lihat masukan pelanggan tentang cake Bunéa Bakery.</p>
    </div>
    <a href="#" onclick="history.back(); return false;" class="btn btn-soft">← Kembali Sebelumnya</a>
</div>

<div class="admin-card p-3 p-md-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Cake</th>
                    <th>Rating</th>
                    <th>Ulasan</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$review->num_rows): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">Belum ada review pelanggan.</td></tr>
            <?php else: ?>
                <?php while ($r = $review->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="fw-semibold"><?= e($r['nama_pelanggan']) ?></div>
                            <small class="text-muted">Pesanan #<?= (int) $r['id_pesanan'] ?></small>
                        </td>
                        <td class="fw-semibold"><?= e($r['nama_kue']) ?></td>
                        <td><span class="stars"><?= str_repeat('★', (int) $r['rating']) ?></span> <strong><?= (int) $r['rating'] ?>/5</strong></td>
                        <td style="min-width:260px"><?= e($r['ulasan']) ?></td>
                        <td><?= e($r['tanggal_review']) ?></td>
                        <td>
                            <a href="review.php?hapus=<?= (int) $r['id_review'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus review ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
