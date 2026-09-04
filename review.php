<?php
require "config/koneksi.php";
require "config/helpers.php";
wajib_login();

if (is_admin()) {
    redirect("admin/review.php");
}

$produk_id = (int) ($_GET["produk"] ?? $_POST["id_produk"] ?? 0);
$pesanan_id = (int) ($_GET["pesanan"] ?? $_POST["id_pesanan"] ?? 0);
$pelanggan_id = (int) $_SESSION["pelanggan"]["id"];

$stmt = $conn->prepare(
    "SELECT p.id_produk, p.nama_kue, p.foto_kue, ps.id_pesanan
     FROM produk_kue p
     JOIN detail_pesanan d ON d.id_produk = p.id_produk
     JOIN pesanan ps ON ps.id_pesanan = d.id_pesanan
     WHERE p.id_produk = ? AND ps.id_pesanan = ?
       AND ps.id_pelanggan = ? AND ps.status_pesanan = 'selesai'
     LIMIT 1"
);
$stmt->bind_param("iii", $produk_id, $pesanan_id, $pelanggan_id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    $_SESSION["flash"] = "Review hanya bisa diberikan untuk cake yang sudah selesai dipesan.";
    redirect("riwayat.php");
}

$cek = $conn->prepare(
    "SELECT * FROM review WHERE id_produk = ? AND id_pelanggan = ? AND id_pesanan = ? LIMIT 1"
);
$cek->bind_param("iii", $produk_id, $pelanggan_id, $pesanan_id);
$cek->execute();
$review_lama = $cek->get_result()->fetch_assoc();

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $rating = (int) ($_POST["rating"] ?? 0);
    $ulasan = trim($_POST["ulasan"] ?? "");

    if ($rating < 1 || $rating > 5) {
        $error = "Silakan pilih rating 1 sampai 5 bintang.";
    } elseif ($ulasan === "") {
        $error = "Ulasan tidak boleh kosong.";
    } elseif (mb_strlen($ulasan) > 1000) {
        $error = "Ulasan maksimal 1000 karakter.";
    } else {
        if ($review_lama) {
            $stmt = $conn->prepare(
                "UPDATE review SET rating = ?, ulasan = ?, tanggal_review = NOW() WHERE id_review = ? AND id_pelanggan = ?"
            );
            $stmt->bind_param("isii", $rating, $ulasan, $review_lama["id_review"], $pelanggan_id);
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO review (id_produk, id_pelanggan, id_pesanan, rating, ulasan) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("iiiis", $produk_id, $pelanggan_id, $pesanan_id, $rating, $ulasan);
        }

        if ($stmt->execute()) {
            $_SESSION["flash"] = "Terima kasih! Review kamu berhasil disimpan. 💗";
            redirect("review.php?produk=$produk_id&pesanan=$pesanan_id");
        }

        $error = "Review gagal disimpan. Silakan coba lagi.";
    }
}

$page_title = "Review {$produk['nama_kue']} | Bunéa Bakery";
require "partials/header.php";
?>

<div class="container page-head">
    <div class="section-kicker">YOUR EXPERIENCE</div>
    <h1>Bagikan pengalamanmu 💗</h1>
    <p class="small-muted">Bantu pelanggan lain memilih cake favorit mereka.</p>
</div>

<div class="container pb-5">
    <div class="row justify-content-center g-4">
        <div class="col-lg-4">
            <div class="product-card h-100">
                <div class="product-img">
                    <?php if ($produk["foto_kue"]): ?>
                        <img src="uploads/<?= e($produk["foto_kue"]) ?>" alt="<?= e($produk["nama_kue"]) ?>">
                    <?php else: ?>
                        <div class="no-photo">🍰</div>
                    <?php endif; ?>
                </div>
                <div class="product-body">
                    <div class="product-type">Pesanan #<?= $pesanan_id ?></div>
                    <h4><?= e($produk["nama_kue"]) ?></h4>
                    <p class="small-muted mb-0">Pesanan kamu sudah selesai. Sekarang kamu bisa memberikan review.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="form-card">
                <div class="section-kicker">REVIEW CAKE</div>
                <h2 class="h3 mb-3"><?= $review_lama ? "Edit review kamu" : "Bagaimana rasanya?" ?></h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="id_produk" value="<?= $produk_id ?>">
                    <input type="hidden" name="id_pesanan" value="<?= $pesanan_id ?>">

                    <label class="form-label fw-semibold">Rating</label>
                    <div class="rating-input mb-4">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= ((int) ($review_lama["rating"] ?? 0) === $i) ? "checked" : "" ?> required>
                            <label for="star<?= $i ?>" title="<?= $i ?> bintang">★</label>
                        <?php endfor; ?>
                    </div>

                    <label class="form-label fw-semibold">Ulasan</label>
                    <textarea class="form-control mb-4" name="ulasan" rows="6" maxlength="1000" placeholder="Ceritakan rasa, tekstur, pelayanan, atau hal yang kamu sukai..." required><?= e($review_lama["ulasan"] ?? "") ?></textarea>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" onclick="history.back(); return false;" class="btn btn-soft">← Kembali</a>
                        <button class="btn btn-bunea" type="submit">💗 Simpan Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
