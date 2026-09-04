<?php require "config/koneksi.php";require "config/helpers.php";wajib_login();$pid=(int)$_SESSION["pelanggan"]["id"];$s=$conn->prepare("SELECT p.*, py.status_pembayaran, py.metode_pembayaran, py.jumlah_bayar, py.tanggal_bayar FROM pesanan p LEFT JOIN pembayaran py ON py.id_pesanan=p.id_pesanan WHERE p.id_pelanggan=? ORDER BY p.id_pesanan DESC");$s->bind_param("i",$pid);$s->execute();$r=$s->get_result();$page_title="Pesanan Saya | Bunéa Bakery";require "partials/header.php";?>
<div class="container page-head">
 <div class="section-kicker">
  Your orders
 </div>
 <h1>
  Pesanan Saya
 </h1>
 <p class="small-muted">
  Pantau status pesanan dan pembayaran.
 </p>
</div>
<div class="container page-actions">
 <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">
  ← Kembali
 </a>
 <a class="btn btn-bunea" href="produk.php">
  🍰 Belanja Cake Lagi
 </a>
</div>
<div class="container pb-5">
 <?php if(!$r->num_rows):?>
 <div class="empty">
  <div class="display-5">
   🍰
  </div>
  <h3>
   Belum ada pesanan
  </h3>
  <a class="btn btn-bunea" href="produk.php">
   Mulai Belanja
  </a>
 </div>
 <?php else:?>
 <div class="row g-3">
  <?php while($o=$r->fetch_assoc()):?>
  <div class="col-lg-6">
   <div class="order-card">
    <div class="d-flex justify-content-between">
     <div>
      <div class="small-muted">
       Pesanan
      </div>
      <h4>
       #<?=$o["id_pesanan"]?>
      </h4>
      <div class="small-muted">
       <?=e($o["tanggal_pesanan"])?>
      </div>
     </div>
     <span class="status status-<?=e($o["status_pesanan"])?>">
      <?=ucfirst(e($o["status_pesanan"]))?>
     </span>
    </div>
    <hr/>
    <div class="d-flex justify-content-between">
     Total
     <strong class="price">
      <?=rupiah($o["total_harga"])?>
     </strong>
    </div>
    <div class="small-muted mt-2">
     Pembayaran: <strong><?=ucfirst(e($o["status_pembayaran"]??"belum lunas"))?></strong>
    </div>
    <?php if(($o["status_pembayaran"]??"")==="lunas"): ?>
    <div class="small-muted mt-1">
     Metode: <strong><?=e($o["metode_pembayaran"]??"-")?></strong><br>
     Jumlah Bayar: <strong><?=rupiah($o["jumlah_bayar"]??0)?></strong><br>
     Tanggal Bayar: <strong><?=e($o["tanggal_bayar"]??"-")?></strong>
    </div>
    <?php endif; ?>
    <?php
      $detail_review_stmt = $conn->prepare("SELECT d.id_produk, p.nama_kue, (SELECT COUNT(*) FROM review rv WHERE rv.id_produk=d.id_produk AND rv.id_pelanggan=? AND rv.id_pesanan=?) AS sudah_review FROM detail_pesanan d JOIN produk_kue p ON p.id_produk=d.id_produk WHERE d.id_pesanan=?");
      $detail_review_stmt->bind_param("iii", $pid, $o["id_pesanan"], $o["id_pesanan"]);
      $detail_review_stmt->execute();
      $detail_reviews = $detail_review_stmt->get_result();
    ?>
    <?php if($o["status_pesanan"] === "selesai" && $detail_reviews->num_rows): ?>
      <div class="review-order-box mt-3">
       <div class="fw-bold mb-2">⭐ Review pesanan ini</div>
       <?php while($dr=$detail_reviews->fetch_assoc()): ?>
        <div class="d-flex justify-content-between align-items-center gap-2 py-2 border-bottom">
         <span><?=e($dr["nama_kue"])?></span>
         <a class="btn btn-sm btn-outline-bunea" href="review.php?produk=<?=$dr["id_produk"]?>&pesanan=<?=$o["id_pesanan"]?>">
          <?= $dr["sudah_review"] ? "✎ Edit Review" : "⭐ Beri Review" ?>
         </a>
        </div>
       <?php endwhile; ?>
      </div>
    <?php endif; ?>
    <div class="d-flex flex-wrap gap-2 mt-3">
     <a class="btn btn-soft" href="detail_pesanan.php?id=<?=$o["id_pesanan"]?>">Detail</a>
     <?php if(($o["status_pembayaran"]??"")!=="lunas"):?>
     <a class="btn btn-bunea" href="pembayaran.php?id=<?=$o["id_pesanan"]?>">Bayar</a>
     <?php else: ?>
     <a class="btn btn-bunea" href="invoice.php?id=<?=$o["id_pesanan"]?>">📄 Invoice</a>
     <a class="btn btn-soft" href="invoice.php?id=<?=$o["id_pesanan"]?>#cetak" onclick="window.print(); return false;">🖨 Cetak Invoice</a>
     <?php endif;?>
    </div>
   </div>
  </div>
  <?php endwhile;?>
 </div>
 <?php endif;?>
</div>
<?php require "partials/footer.php";?>

