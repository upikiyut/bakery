<?php
require "config/koneksi.php";
require "config/helpers.php";
wajib_login();

$id = (int) ($_GET["id"] ?? 0);
$pelangganId = (int) $_SESSION["pelanggan"]["id"];

$stmt = $conn->prepare(
    "SELECT p.*, pl.nama_pelanggan, pl.email, pl.no_telepon,
            py.tanggal_bayar, py.metode_pembayaran, py.jumlah_bayar,
            py.status_pembayaran
     FROM pesanan p
     JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
     LEFT JOIN pembayaran py ON py.id_pesanan = p.id_pesanan
     WHERE p.id_pesanan = ? AND p.id_pelanggan = ?"
);
$stmt->bind_param("ii", $id, $pelangganId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION["flash"] = "Struk pesanan tidak ditemukan.";
    redirect("riwayat.php");
}

$stmt = $conn->prepare(
    "SELECT d.*, p.nama_kue
     FROM detail_pesanan d
     JOIN produk_kue p ON p.id_produk = d.id_produk
     WHERE d.id_pesanan = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$items = $stmt->get_result();

$page_title = "Struk Pesanan #$id | Bunéa Bakery";
require "partials/header.php";
?>

<div class="container page-head">
    <div class="section-kicker">PAYMENT RECEIPT</div>
    <h1>Struk Pesanan #<?= $id ?></h1>
    <p class="small-muted">Bukti pembayaran sederhana untuk pesanan online Bunéa Bakery.</p>
</div>

<div class="container page-actions no-print">
    <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">← Kembali</a>
    <button class="btn btn-bunea" type="button" onclick="window.print();">🖨 Cetak Struk</button>
    <a class="btn btn-soft" href="invoice.php?id=<?= $id ?>">📧 Lihat Invoice</a>
</div>

<div class="container pb-5">
    <div class="receipt-card">
        <div class="receipt-header">
            <div>
                <div class="receipt-brand">🍰 Bunéa Bakery</div>
                <h2>Struk Pembayaran</h2>
                <p class="small-muted mb-0">Bukti transaksi pembelian</p>
            </div>
            <div class="receipt-status">✓ <?= e(ucfirst($order["status_pembayaran"] ?: "belum lunas")) ?></div>
        </div>

        <hr>

        <div class="receipt-info">
            <div><span>Pesanan</span><strong>#<?= $id ?></strong></div>
            <div><span>Tanggal</span><strong><?= date("d/m/Y H:i", strtotime($order["tanggal_pesanan"])) ?></strong></div>
            <div><span>Pelanggan</span><strong><?= e($order["nama_pelanggan"]) ?></strong></div>
            <div><span>Pembayaran</span><strong><?= e($order["metode_pembayaran"] ?: "-") ?></strong></div>
        </div>

        <table class="table receipt-table mt-4">
            <thead>
                <tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr>
            </thead>
            <tbody>
            <?php while ($item = $items->fetch_assoc()): ?>
                <tr>
                    <td><?= e($item["nama_kue"]) ?></td>
                    <td class="text-center"><?= (int) $item["jumlah"] ?></td>
                    <td class="text-end"><?= rupiah($item["harga_satuan"]) ?></td>
                    <td class="text-end"><?= rupiah($item["subtotal"]) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <div class="receipt-total"><span>Total Dibayar</span><strong><?= rupiah($order["jumlah_bayar"] ?: $order["total_harga"]) ?></strong></div>

        <div class="receipt-note">
            Terima kasih sudah berbelanja di Bunéa Bakery! 💗<br>
            Invoice belanja online dapat dikirim ke email pelanggan.
        </div>
    </div>
</div>

<style>
.receipt-card{max-width:850px;margin:auto;background:#fff;border:1px solid #f0dbe7;border-radius:24px;padding:32px;box-shadow:0 16px 45px rgba(102,50,78,.08)}
.receipt-header{display:flex;justify-content:space-between;gap:20px;align-items:center}.receipt-brand{font-size:1.05rem;font-weight:900;color:#b84f7d}.receipt-header h2{margin:5px 0}.receipt-status{background:#eaf8ef;color:#23804a;border-radius:999px;padding:8px 14px;font-weight:800}.receipt-info{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.receipt-info div{background:#fffafc;border:1px solid #f3e2ea;border-radius:14px;padding:12px}.receipt-info span{display:block;font-size:.7rem;color:#9a7181;margin-bottom:3px}.receipt-table thead th{background:#fff7fb;border:0}.receipt-total{display:flex;justify-content:space-between;border-top:2px solid #f0dbe7;padding-top:18px;font-size:18px}.receipt-total strong{font-size:24px;color:#b84f7d}.receipt-note{text-align:center;margin-top:22px;background:#fff0f6;border-radius:15px;padding:16px;color:#8c4666}
@media(max-width:768px){.receipt-header{flex-direction:column;align-items:flex-start}.receipt-info{grid-template-columns:1fr 1fr}}
@media(max-width:576px){.receipt-card{padding:20px}.receipt-info{grid-template-columns:1fr}}
@media print{.no-print,nav,footer{display:none!important}body{background:#fff!important}.receipt-card{box-shadow:none!important;border:1px solid #ddd!important}}
</style>

<?php require "partials/footer.php"; ?>
