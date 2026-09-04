<?php
require "config/koneksi.php";
require "config/helpers.php";
require "config/invoice.php";

wajib_login();

$id = (int) ($_GET["id"] ?? 0);
$pelangganId = (int) $_SESSION["pelanggan"]["id"];

$stmt = $conn->prepare(
    "SELECT p.*, py.id_pembayaran, py.tanggal_bayar,
            py.metode_pembayaran, py.jumlah_bayar,
            py.status_pembayaran, py.bukti_pembayaran
     FROM pesanan p
     JOIN pembayaran py ON py.id_pesanan = p.id_pesanan
     WHERE p.id_pesanan = ? AND p.id_pelanggan = ?"
);
$stmt->bind_param("ii", $id, $pelangganId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION["flash"] = "Pesanan tidak ditemukan.";
    redirect("riwayat.php");
}

$error = "";
$metodeTersimpan = $order["metode_pembayaran"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $metode = trim($_POST["metode_pembayaran"] ?? "");
    $subMetode = $metode === "QRIS" ? trim($_POST["qris_provider"] ?? "") : ($metode === "Transfer Bank" ? trim($_POST["bank_provider"] ?? "") : "");
    $jumlah = (float) ($_POST["jumlah_bayar"] ?? 0);

    $bankAllowed = [
        "Bank Mandiri" => "1234567890",
        "Bank BRI" => "0987654321",
        "Bank BNI" => "1122334455",
    ];
    $qrisAllowed = ["DANA", "OVO", "GoPay"];

    if (!in_array($metode, ["Transfer Bank", "QRIS", "Cash"], true) || $jumlah < $order["total_harga"]) {
        $error = "Pilih metode pembayaran dan pastikan jumlah bayar sesuai total.";
    } elseif ($metode === "Transfer Bank" && !array_key_exists($subMetode, $bankAllowed)) {
        $error = "Silakan pilih bank tujuan.";
    } elseif ($metode === "QRIS" && !in_array($subMetode, $qrisAllowed, true)) {
        $error = "Silakan pilih tujuan QRIS.";
    } else {
        // Invoice online tidak membutuhkan upload bukti pembayaran.
        // Untuk simulasi tugas, pembayaran langsung dicatat sebagai lunas.
        $metodeSimpan = $metode === "Cash" ? "Cash" : ($metode . " - " . $subMetode);
        $stmt = $conn->prepare(
            "UPDATE pembayaran SET tanggal_bayar=NOW(), metode_pembayaran=?, jumlah_bayar=?, status_pembayaran='lunas', bukti_pembayaran=NULL WHERE id_pembayaran=?"
        );
        $stmt->bind_param("sdi", $metodeSimpan, $jumlah, $order["id_pembayaran"]);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE pesanan SET status_pesanan='diproses' WHERE id_pesanan=? AND status_pesanan='menunggu'");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $nama = $_SESSION["pelanggan"]["nama"];
        $_SESSION["flash"] = "Terima kasih, $nama! Pembayaran pesanan #$id berhasil dicatat. Invoice sudah tersedia dan dapat dikirim ke email. 💗";
        kirimInvoiceEmail($conn, $id);
        redirect("invoice.php?id=" . $id);
    }
}

$page_title = "Pembayaran #$id | Bunéa Bakery";
require "partials/header.php";
?>

<div class="container page-head">
    <div class="section-kicker">PAYMENT</div>
    <h1>Pembayaran #<?= $id ?></h1>
    <p class="small-muted">Total: <strong class="price"><?= rupiah($order["total_harga"]) ?></strong></p>
</div>

<div class="container page-actions">
    <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">← Kembali</a>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="form-card">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <?php if ($order["status_pembayaran"] === "lunas"): ?>
                    <div class="alert alert-success">
                        <strong>Terima kasih, <?= e($_SESSION["pelanggan"]["nama"]) ?>! 💗</strong><br>
                        Sudah mampir ke Bunéa Bakery. Pembayaran pesanan #<?= $id ?> sudah diterima dan pesanan sedang diproses.
                    </div>
                    <div class="payment-success-actions mt-3">
                        <a class="btn btn-bunea" href="invoice.php?id=<?= $id ?>">📄 Lihat Invoice</a>
                        <a class="btn btn-soft" href="invoice.php?id=<?= $id ?>#cetak" onclick="window.print(); return false;">🖨 Cetak Invoice</a>
                        <a class="btn btn-soft" href="invoice.php?id=<?= $id ?>&kirim=1">📧 Kirim Invoice ke Email</a>
                        <a class="btn btn-soft" href="detail_pesanan.php?id=<?= $id ?>">Lihat Detail Pesanan</a>
                    </div>
                    <div class="invoice-order-note mt-3">
                        📄 <strong>Invoice pembayaran sudah tersedia.</strong><br>
                        <span>Invoice dapat dilihat dan dicetak sebagai bukti transaksi Bunéa Bakery.</span>
                    </div>
                <?php else: ?>
                    <h4>Metode Pembayaran</h4>
                    <p class="small-muted">Pilih metode pembayaran. Setelah pembayaran dicatat, invoice belanja online langsung tersedia dan dapat dikirim ke email pelanggan.</p>

                    <form method="post" id="paymentForm">
                        <label class="form-label">Metode Pembayaran</label>
                        <select class="form-select mb-3" name="metode_pembayaran" id="metodePembayaran" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="QRIS">QRIS</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Cash">Cash</option>
                        </select>

                        <div id="qrisInfo" class="payment-method-info d-none mb-3">
                            <label class="form-label">Pilih QRIS</label>
                            <select class="form-select mb-3" name="qris_provider" id="qrisProvider">
                                <option value="">-- Pilih QRIS --</option>
                                <option value="DANA">DANA</option>
                                <option value="OVO">OVO</option>
                                <option value="GoPay">GoPay</option>
                            </select>
                            <div class="qris-box">
                                <div class="qris-title">Scan Barcode QRIS Bunéa Bakery</div>
                                <img id="qrisImage" class="qris-image" src="assets/img/qris-dana.png" alt="QRIS DANA">
                                <div id="qrisLabel" class="fw-bold mb-2">DANA</div>
                                <p class="small-muted mb-0">Pilih DANA, OVO, atau GoPay lalu scan barcode demo yang sesuai.</p>
                            </div>
                        </div>

                        <div id="transferInfo" class="payment-method-info d-none mb-3">
                            <label class="form-label">Pilih Bank</label>
                            <select class="form-select mb-3" name="bank_provider" id="bankProvider">
                                <option value="">-- Pilih Bank --</option>
                                <option value="Bank Mandiri">Bank Mandiri</option>
                                <option value="Bank BRI">Bank BRI</option>
                                <option value="Bank BNI">Bank BNI</option>
                            </select>
                            <div id="bankInfo" class="alert alert-bunea mb-0">
                                <strong>Transfer Bank</strong><br>
                                Pilih bank untuk melihat nama dan nomor rekening Bunéa Bakery.
                            </div>
                        </div>

                        <div id="cashInfo" class="payment-method-info d-none mb-3">
                            <div class="alert alert-success mb-0"><strong>Cash</strong><br>Pembayaran dilakukan secara tunai saat pesanan diterima/diambil.</div>
                        </div>

                        <label class="form-label">Jumlah Bayar</label>
                        <input class="form-control mb-3" min="<?= $order["total_harga"] ?>" name="jumlah_bayar" required step="1" type="number" value="<?= $order["total_harga"] ?>">

                        <button class="btn btn-bunea w-100" type="submit">Bayar Sekarang</button>
                    </form>

                    <script>
                    const metode = document.getElementById('metodePembayaran');
                    const qrisInfo = document.getElementById('qrisInfo');
                    const transferInfo = document.getElementById('transferInfo');
                    const cashInfo = document.getElementById('cashInfo');
                    const qrisProvider = document.getElementById('qrisProvider');
                    const bankProvider = document.getElementById('bankProvider');
                    const qrisImage = document.getElementById('qrisImage');
                    const qrisLabel = document.getElementById('qrisLabel');
                    const bankInfo = document.getElementById('bankInfo');
                    const qrisData = {
                        'DANA': ['assets/img/qris-dana.png', 'DANA'],
                        'OVO': ['assets/img/qris-ovo.png', 'OVO'],
                        'GoPay': ['assets/img/qris-gopay.png', 'GoPay']
                    };
                    const bankData = {
                        'Bank Mandiri': '1234567890',
                        'Bank BRI': '0987654321',
                        'Bank BNI': '1122334455'
                    };
                    function updatePaymentForm() {
                        const value = metode.value;
                        qrisInfo.classList.toggle('d-none', value !== 'QRIS');
                        transferInfo.classList.toggle('d-none', value !== 'Transfer Bank');
                        cashInfo.classList.toggle('d-none', value !== 'Cash');
                        qrisProvider.required = value === 'QRIS';
                        bankProvider.required = value === 'Transfer Bank';
                        qrisProvider.disabled = value !== 'QRIS';
                        bankProvider.disabled = value !== 'Transfer Bank';
                    }
                    qrisProvider.addEventListener('change', function() {
                        const data = qrisData[this.value];
                        if (data) { qrisImage.src=data[0]; qrisLabel.textContent=data[1]; qrisImage.alt='QRIS '+data[1]; }
                    });
                    bankProvider.addEventListener('change', function() {
                        const no = bankData[this.value];
                        bankInfo.innerHTML = no ? '<strong>'+this.value+'</strong><br>Nama Rekening: <strong>Bunéa Bakery</strong><br>No. Rekening: <strong>'+no+'</strong><br><small>Silakan transfer sesuai total pembayaran.</small>' : '<strong>Transfer Bank</strong><br>Pilih bank untuk melihat nama dan nomor rekening Bunéa Bakery.';
                    });
                    metode.addEventListener('change', updatePaymentForm);
                    updatePaymentForm();
                    </script>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="summary-card">
                <h5>Alur Pembayaran</h5>
                <ol class="small-muted">
                    <li>Pilih QRIS, Transfer Bank, atau Cash.</li>
                    <li>QRIS: pilih DANA, OVO, atau GoPay lalu scan barcode.</li>
                    <li>Transfer: pilih Mandiri, BRI, atau BNI lalu transfer ke rekening Bunéa Bakery.</li>
                    <li>QRIS, Transfer Bank, dan Cash tidak perlu upload bukti pembayaran.</li>
                    <li>Setelah berhasil, invoice belanja online langsung tersedia.</li>
                    <li>Invoice dapat dikirim ke email pelanggan dan dilihat kembali dari halaman pesanan.</li>
                </ol>
                <a class="btn btn-soft w-100" href="#" onclick="history.back(); return false;">← Kembali Sebelumnya</a>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
