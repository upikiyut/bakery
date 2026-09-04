<?php
require "config/koneksi.php";
require "config/helpers.php";
require "config/invoice.php";

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
    $_SESSION["flash"] = "Invoice tidak ditemukan.";
    redirect("riwayat.php");
}

if (isset($_GET['kirim']) && $order['status_pembayaran'] === 'lunas') {
    $sent = kirimInvoiceEmail($conn, $id);
    $_SESSION['flash'] = $sent
        ? "Invoice pesanan #$id berhasil dikirim ke " . ($order['email'] ?: "email pelanggan") . "."
        : "Invoice siap ditampilkan, tetapi email belum dapat diproses.";
    redirect("invoice.php?id=" . $id);
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

$page_title = "Invoice Pesanan #$id | Bunéa Bakery";
require "partials/header.php";
?>

<div class="container page-head">
    <div class="section-kicker">ORDER INVOICE</div>
    <h1>Invoice Pesanan #<?= $id ?></h1>
    <p class="small-muted">Invoice pembelian online Bunéa Bakery — seperti bukti belanja marketplace. 💗</p>
</div>

<div class="container page-actions no-print">
    <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">← Kembali</a>
    <a class="btn btn-bunea" href="invoice.php?id=<?= $id ?>&kirim=1">📧 Kirim Invoice ke Email</a>
    <button type="button" class="btn btn-invoice-print" onclick="window.print()">🖨 Cetak Invoice</button>
    <a class="btn btn-soft" href="struk.php?id=<?= $id ?>">🧾 Lihat Struk</a>
</div>

<div class="container pb-5">
    <div class="invoice-card">
        <div class="invoice-top">
            <div>
                <div class="invoice-brand">🍰 Bunéa Bakery</div>
                <h2 class="mb-1">Invoice Pembelian</h2>
                <p class="small-muted mb-0">Bukti transaksi belanja online</p>
            </div>
            <div class="invoice-number-box">
                <span>NO. INVOICE</span>
                <strong>#<?= str_pad((string)$id, 6, "0", STR_PAD_LEFT) ?></strong>
                <em class="invoice-paid">✓ LUNAS</em>
            </div>
        </div>

        <hr>

        <div class="invoice-info-grid mb-4">
            <div class="invoice-info-box">
                <div class="invoice-info-title">DATA PELANGGAN</div>
                <strong><?= e($order["nama_pelanggan"]) ?></strong>
                <div><?= e($order["email"]) ?></div>
                <div><?= e($order["no_telepon"]) ?></div>
            </div>
            <div class="invoice-info-box text-md-end">
                <div class="invoice-info-title">DETAIL PESANAN</div>
                <strong><?= date("d/m/Y H:i", strtotime($order["tanggal_pesanan"])) ?></strong>
                <div>Pembayaran: <?= $order["tanggal_bayar"] ? date("d/m/Y H:i", strtotime($order["tanggal_bayar"])) : "-" ?></div>
                <div>Status pesanan: <strong><?= e(ucfirst($order["status_pesanan"])) ?></strong></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table invoice-table">
                <thead><tr><th>Produk</th><th class="text-center">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= e($item["nama_kue"]) ?></strong></td>
                        <td class="text-center"><?= (int) $item["jumlah"] ?></td>
                        <td class="text-end"><?= rupiah($item["harga_satuan"]) ?></td>
                        <td class="text-end"><?= rupiah($item["subtotal"]) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="invoice-total"><span>TOTAL TAGIHAN</span><strong><?= rupiah($order["total_harga"]) ?></strong></div>

        <div class="invoice-payment">
            <div><span class="small-muted">Metode Pembayaran</span><br><strong><?= e($order["metode_pembayaran"] ?: "Belum dipilih") ?></strong></div>
            <div class="text-md-end"><span class="small-muted">Status Pembayaran</span><br><span class="invoice-status-paid">✓ <?= e(ucfirst($order["status_pembayaran"] ?: "belum lunas")) ?></span></div>
        </div>

        <div class="invoice-email-note">
            <strong>📧 Invoice belanja online</strong><br>
            Invoice ini adalah dokumen pembelian yang dikirim ke email pelanggan: <strong><?= e($order["email"]) ?></strong>.
            <div class="small-muted mt-1">Gunakan tombol “Kirim Invoice ke Email” di atas untuk mengirim ulang invoice.</div>
        </div>

        <div class="invoice-thanks">
            <strong>Terima kasih sudah belanja di Bunéa Bakery! 💗</strong><br>
            Pesananmu sedang diproses. Semoga harimu semakin manis.
        </div>
    </div>
</div>

<style>
.btn-invoice-print{background:#f3eaff;color:#7b58a8;border:1px solid #dfd0f5;font-weight:700}
.btn-invoice-print:hover{background:#eadcff;color:#67428f}
.invoice-card{max-width:900px;margin:auto;background:#fff;border:1px solid #f0dbe7;border-radius:24px;padding:32px;box-shadow:0 16px 45px rgba(102,50,78,.08)}
.invoice-top,.invoice-payment,.invoice-total{display:flex;justify-content:space-between;gap:20px;align-items:center}
.invoice-brand{font-size:1.05rem;font-weight:900;color:#b84f7d;margin-bottom:5px}
.invoice-number-box{min-width:150px;text-align:right;background:#fff0f6;border-radius:16px;padding:11px 15px}
.invoice-number-box span{display:block;font-size:.68rem;letter-spacing:.12em;color:#9a7181}
.invoice-number-box strong{display:block;color:#b84f7d;font-size:1.15rem}
.invoice-paid{display:inline-block;margin-top:4px;color:#23804a;font-size:.72rem;font-style:normal;font-weight:800}
.invoice-info-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.invoice-info-box{background:#fffafc;border:1px solid #f3e2ea;border-radius:16px;padding:16px;line-height:1.75}
.invoice-info-title{font-size:.7rem;font-weight:800;letter-spacing:.1em;color:#9a7181;margin-bottom:5px}
.invoice-table thead th{background:#fff7fb;border:0}.invoice-table td{vertical-align:middle}
.invoice-total{margin-top:20px;padding:18px 0;border-top:2px solid #f0dbe7;font-size:18px}.invoice-total strong{font-size:24px;color:#b84f7d}
.invoice-payment{margin-top:12px;background:#fff7fb;border-radius:16px;padding:16px}.invoice-status-paid{display:inline-block;color:#23804a;background:#eaf8ef;border-radius:999px;padding:5px 10px;font-weight:800}
.invoice-email-note{margin-top:20px;background:#f8f5ff;border:1px solid #e9def8;border-radius:16px;padding:16px}.invoice-thanks{text-align:center;margin-top:24px;padding:20px;background:#fff0f6;border-radius:16px;color:#8c4666}
@media (max-width: 576px){.invoice-card{padding:22px}.invoice-top,.invoice-payment{flex-direction:column;align-items:stretch}.invoice-number-box{text-align:left}.invoice-info-grid{grid-template-columns:1fr}.invoice-payment .text-md-end{text-align:left!important}}
@media print{.no-print,nav,footer{display:none!important}}
</style>

<?php require "partials/footer.php"; ?>
