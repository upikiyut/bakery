<?php
require "config/koneksi.php";
require "config/helpers.php";

wajib_login();

$cart = $_SESSION["cart"] ?? [];

if (!$cart) {
    $_SESSION["flash"] = "Keranjang masih kosong.";
    redirect("produk.php");
}

$ids = implode(",", array_map("intval", array_keys($cart)));
$result = $conn->query("SELECT * FROM produk_kue WHERE id_produk IN ($ids)");

$items = [];
$total = 0;

while ($p = $result->fetch_assoc()) {
    $qty = (int) ($cart[$p["id_produk"]] ?? 0);

    if ($qty > $p["stok"]) {
        $_SESSION["flash"] = "Stok {$p["nama_kue"]} tidak mencukupi.";
        redirect("keranjang.php");
    }

    $p["qty"] = $qty;
    $p["subtotal"] = $qty * $p["harga"];
    $items[] = $p;
    $total += $p["subtotal"];
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn->begin_transaction();

    try {
        foreach ($items as $item) {
            $check = $conn->query(
                "SELECT stok FROM produk_kue WHERE id_produk=" .
                (int) $item["id_produk"] .
                " FOR UPDATE"
            )->fetch_assoc();

            if (!$check || $check["stok"] < $item["qty"]) {
                throw new Exception("Stok berubah. Periksa keranjang.");
            }
        }

        $pelangganId = (int) $_SESSION["pelanggan"]["id"];

        $stmt = $conn->prepare(
            "INSERT INTO pesanan (id_pelanggan, total_harga, status_pesanan)
             VALUES (?, ?, 'menunggu')"
        );
        $stmt->bind_param("id", $pelangganId, $total);
        $stmt->execute();

        $pesananId = $conn->insert_id;

        $detail = $conn->prepare(
            "INSERT INTO detail_pesanan
             (id_pesanan, id_produk, jumlah, harga_satuan, subtotal)
             VALUES (?, ?, ?, ?, ?)"
        );

        $updateStok = $conn->prepare(
            "UPDATE produk_kue SET stok = stok - ? WHERE id_produk = ?"
        );

        foreach ($items as $item) {
            $produkId = (int) $item["id_produk"];
            $qty = (int) $item["qty"];
            $harga = (float) $item["harga"];
            $subtotal = (float) $item["subtotal"];

            $detail->bind_param(
                "iiidd",
                $pesananId,
                $produkId,
                $qty,
                $harga,
                $subtotal
            );
            $detail->execute();

            $updateStok->bind_param("ii", $qty, $produkId);
            $updateStok->execute();
        }

        $pembayaran = $conn->prepare(
            "INSERT INTO pembayaran
             (id_pesanan, jumlah_bayar, status_pembayaran)
             VALUES (?, ?, 'belum lunas')"
        );
        $pembayaran->bind_param("id", $pesananId, $total);
        $pembayaran->execute();

        $conn->commit();
        unset($_SESSION["cart"]);

        $nama = $_SESSION["pelanggan"]["nama"];
        $_SESSION["flash"] =
            "Selamat datang kembali, $nama! Pesanan #$pesananId berhasil dibuat. " .
            "Silakan lanjutkan pembayaran. 💗";

        redirect("pembayaran.php?id=" . $pesananId);
    } catch (Throwable $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

$page_title = "Checkout | Bunéa Bakery";
require "partials/header.php";
?>

<div class="container page-head">
    <div class="section-kicker">CHECKOUT</div>
    <h1>Konfirmasi Pesanan</h1>
    <p class="small-muted">
        Pastikan cake, jumlah, dan total pesananmu sudah sesuai.
    </p>
</div>

<div class="container page-actions">
    <a
        class="btn btn-outline-bunea"
        href="#"
        onclick="history.back(); return false;"
    >
        ← Kembali
    </a>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-card">
                <h4 class="mb-3">Pesanan Kamu</h4>

                <?php foreach ($items as $item): ?>
                    <div class="d-flex justify-content-between align-items-center border-bottom py-3">
                        <div>
                            <strong><?= e($item["nama_kue"]) ?></strong>
                            <div class="small-muted">
                                <?= $item["qty"] ?> × <?= rupiah($item["harga"]) ?>
                            </div>
                        </div>
                        <strong><?= rupiah($item["subtotal"]) ?></strong>
                    </div>
                <?php endforeach; ?>

                <div class="d-flex justify-content-between mt-4">
                    <strong>Total</strong>
                    <strong class="price fs-5"><?= rupiah($total) ?></strong>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="summary-card">
                <h5>Siap membuat harimu lebih manis? 🍰</h5>
                <p class="small-muted">
                    Klik tombol di bawah untuk membuat pesanan dan lanjut ke pembayaran.
                </p>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <button class="btn btn-bunea w-100" type="submit">
                        Buat Pesanan & Lanjut Bayar
                    </button>
                </form>

                <a
                    class="btn btn-soft w-100 mt-2"
                    href="#"
                    onclick="history.back(); return false;"
                >
                    ← Kembali Sebelumnya
                </a>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
