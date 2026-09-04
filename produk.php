<?php require "config/koneksi.php"; require "config/helpers.php"; if(isset($_GET["tambah"])){$id=(int)$_GET["tambah"];$q=$conn->query("SELECT id_produk,stok FROM produk_kue WHERE id_produk=$id");$p=$q?$q->fetch_assoc():null;if($p){$n=$_SESSION["cart"][$id]??0;if($n<$p["stok"]){$_SESSION["cart"][$id]=$n+1;$_SESSION["flash"]="Cake berhasil masuk ke keranjang.";}else $_SESSION["flash"]="Jumlah melebihi stok.";}redirect((isset($_GET["lanjut"]) && $_GET["lanjut"] === "keranjang") ? "keranjang.php" : "produk.php");}$produk=$conn->query("SELECT p.* FROM produk_kue p ORDER BY p.id_produk DESC");$page_title="Koleksi Cake | Bunéa Bakery";require "partials/header.php"; ?>
<div class="container page-head">
 <div class="section-kicker">
  Bunéa collection
 </div>
 <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
  <div>
   <h1>
    Choose your cake.
   </h1>
   <p class="small-muted mb-0">
    Setiap cake punya ciri khas masing-masing Pilih yang paling cocok untuk momenmu.
   </p>
  </div>
  <div class="photo-note">
   📸 
   <strong>
    uploads
   </strong>
   .
  </div>
 </div>
</div>
<div class="container page-actions">
 <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">
  ← Kembali
 </a>
 <a class="btn btn-bunea" href="keranjang.php">
  🛒 Lihat Keranjang
  <span class="mini-count">
   <?=cart_count()?>
  </span>
 </a>
</div>
<div class="container pb-5">
 <div class="row g-4">
  <?php while($p=$produk->fetch_assoc()): ?>
  <div class="col-sm-6 col-lg-4">
   <div class="product-card">
    <div class="product-img">
     <?php if($p["foto_kue"]): ?>
     <img alt="<?=e($p["nama_kue"])?>" src="uploads/<?=e($p["foto_kue"])?>"/>
     <?php else: ?>
     <div class="no-photo">
      🍰
     </div>
     <?php endif;?>
    </div>
    <div class="product-body">
     <div class="product-type">
      <?=e($p["jenis_kue"])?>
     </div>
     <h4>
      <?=e($p["nama_kue"])?>
     </h4>
     <p class="small-muted">
      <?=e($p["deskripsi"])?>
     </p>
     <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="price">
       <?=rupiah($p["harga"])?>
      </span>
      <span class="small-muted">
       Stok <?=$p["stok"]?>
      </span>
     </div>
     <?php if($p["stok"]>0): ?>
     <a class="btn btn-bunea w-100" href="produk.php?tambah=<?=$p["id_produk"]?>">
      + Tambah ke Keranjang
     </a>
     <?php else: ?>
     <button class="btn btn-secondary w-100 rounded-pill" disabled="">
      Stok Habis
     </button>
     <?php endif;?>
    </div>
   </div>
  </div>
  <?php endwhile;?>
 </div>
</div>
<?php require "partials/footer.php"; ?>

