<?php require "config/koneksi.php"; require "config/helpers.php"; $page_title="Bunéa Bakery | Sweet Moments"; $result=$conn->query("SELECT * FROM produk_kue ORDER BY id_produk DESC LIMIT 4");
$home_reviews=$conn->query("SELECT r.*, p.nama_kue, pel.nama_pelanggan FROM review r JOIN produk_kue p ON p.id_produk=r.id_produk JOIN pelanggan pel ON pel.id_pelanggan=r.id_pelanggan ORDER BY r.tanggal_review DESC LIMIT 6"); require "partials/header.php"; ?>
<section class="hero">
 <div class="container">
  <?php if (is_login()): ?>
  <div class="welcome-banner mb-4">
   💗 <strong>Selamat datang <?=e($_SESSION["pelanggan"]["nama"])?> di Bunéa Bakery!</strong>
   <span class="small-muted"> Senang melihatmu kembali. Yuk pilih cake favoritmu.</span>
  </div>
  <?php endif; ?>
  <div class="row align-items-center g-5">
   <div class="col-lg-7">
    <div class="ribbon mb-3">
     ♡ Handmade with love
    </div>
    <div class="hero-badge">
     Bunéa Bakery 
    </div>
    <h1>
     Small bites,
     <br/>
     <em>
      big happiness.
     </em>
    </h1>
    <p class="mt-4">
     Cake cantik, lembut, dan dibuat untuk membuat hari biasa terasa sangat nikmat.
    </p>
    <div class="d-flex gap-2 mt-4 flex-wrap">
     <a class="btn btn-bunea" href="produk.php">
      Jelajahi Koleksi →
     </a>
     <?php if(!is_login()): ?>
     <a class="btn btn-soft" href="register.php">
      Daftar sebagai pelanggan
     </a>
     <?php else: ?>
     <a class="btn btn-soft" href="riwayat.php">
      Lihat Pesananku
     </a>
     <?php endif; ?>
    </div>
   </div>
   <div class="col-lg-5">
    <div class="hero-art">
     <div class="cake-orb">
      <div class="emoji">
       🎂
      </div>
     </div>
     <div class="floating float-one">
      🍓 Fresh every day
     </div>
     <div class="floating float-two">
      🎀 Made for your moment
     </div>
    </div>
   </div>
  </div>
 </div>
</section>
<section class="feature-strip">
 <div class="container">
  <div class="row g-3">
   <div class="col-md-4">
    <div class="feature-box">
     <div class="feature-icon">
      🍰
     </div>
     <h5>
      Freshly Baked
     </h5>
     <p class="small-muted mb-0">
      Cake lembut dengan bahan pilihan dan rasa yang dibuat sepenuh hati.
     </p>
    </div>
   </div>
   <div class="col-md-4">
    <div class="feature-box">
     <div class="feature-icon">
      🌷
     </div>
     <h5>
      Pretty & Sweet
     </h5>
     <p class="small-muted mb-0">
      Dekorasi manis untuk ulang tahun, hadiah, atau self-reward.
     </p>
    </div>
   </div>
   <div class="col-md-4">
    <div class="feature-box">
     <div class="feature-icon">
      💌
     </div>
     <h5>
      Easy Ordering
     </h5>
     <p class="small-muted mb-0">
      Pilih cake → keranjang → checkout → pembayaran. Sesimpel itu.
     </p>
    </div>
   </div>
  </div>
 </div>
</section>
<section class="section">
 <div class="container">
  <div class="row align-items-center mb-4 favorite-header">
   <div class="col">
    <div class="section-kicker">
     OUR FAVORITES
    </div>
    <h2 class="section-title mb-0">
     Cake yang paling manis
     <br/>
     untuk harimu.
    </h2>
   </div>
   <div class="col-auto">
    <a class="mini-link btn-see-all" href="produk.php">
     Lihat semua cake →
    </a>
   </div>
  </div>
  <div class="row g-4">
   <?php while($p=$result->fetch_assoc()): ?>
   <div class="col-12 col-sm-6 col-lg-3">
    <div class="product-card">
     <div class="product-img">
      <?php if($p['foto_kue']): ?>
      <img alt="<?=e($p['nama_kue'])?>" src="uploads/<?=e($p['foto_kue'])?>"/>
      <?php else: ?>
      <div class="no-photo">
       🍰
      </div>
      <?php endif;?>
     </div>
     <div class="product-body">
      <div class="product-type">
       <?=e($p['jenis_kue'])?>
      </div>
      <h4>
       <?=e($p['nama_kue'])?>
      </h4>
      <p class="small-muted mb-3">
       <?=e($p['deskripsi'])?>
      </p>
      <div class="d-flex justify-content-between align-items-center">
       <span class="price">
        <?=rupiah($p['harga'])?>
       </span>
       <a class="btn btn-sm btn-bunea" href="produk.php?tambah=<?= $p["id_produk"] ?>&lanjut=keranjang">
        Pesan
       </a>
      </div>
     </div>
    </div>
   </div>
   <?php endwhile;?>
  </div>
 </div>
</section>
<section class="section pt-0">
 <div class="container">
  <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
   <div>
    <div class="section-kicker">CUSTOMER LOVE</div>
    <h2 class="section-title mb-0">Cerita manis dari pelanggan 💗</h2>
    <p class="small-muted mb-0 mt-2">Review dari pelanggan yang sudah menikmati cake Bunéa Bakery.</p>
   </div>
  </div>
  <div class="row g-4">
   <?php if($home_reviews && $home_reviews->num_rows): ?>
    <?php while($rv=$home_reviews->fetch_assoc()): ?>
     <div class="col-md-6 col-lg-4">
      <div class="review-home-card h-100">
       <div class="d-flex justify-content-between gap-3 mb-2">
        <strong><?=e($rv["nama_pelanggan"])?></strong>
        <span class="stars"><?=str_repeat("★", (int)$rv["rating"])?></span>
       </div>
       <div class="small-muted mb-2"><?=e($rv["nama_kue"])?></div>
       <p class="mb-2 review-home-text">“<?=e($rv["ulasan"])?>”</p>
       <small class="small-muted"><?=e(date("d M Y", strtotime($rv["tanggal_review"])))?></small>
      </div>
     </div>
    <?php endwhile; ?>
   <?php else: ?>
    <div class="col-12"><div class="review-home-empty">Belum ada review pelanggan. Setelah pelanggan menyelesaikan pesanan dan memberikan review, ceritanya akan tampil di sini. 💗</div></div>
   <?php endif; ?>
  </div>
 </div>
</section>
<section class="section pt-0">
 <div class="container">
  <div class="row align-items-center g-4">
   <div class="col-lg-6">
    <div class="section-kicker">
     How it works
    </div>
    <h2 class="section-title">
     Pesan cake tanpa ribet.
    </h2>
   </div>
   <div class="col-lg-6">
    <div class="row g-3">
     <div class="col-6">
      <div class="feature-box">
       <strong>
        01
       </strong>
       <h5 class="mt-2">
        Pilih Cake
       </h5>
       <p class="small-muted mb-0">
        Lihat foto, harga, stok, dan detail produk.
       </p>
      </div>
     </div>
     <div class="col-6">
      <div class="feature-box">
       <strong>
        02
       </strong>
       <h5 class="mt-2">
        Checkout
       </h5>
       <p class="small-muted mb-0">
        Masukkan cake ke keranjang lalu buat pesanan.
       </p>
      </div>
     </div>
     <div class="col-6">
      <div class="feature-box">
       <strong>
        03
       </strong>
       <h5 class="mt-2">
        Bayar
       </h5>
       <p class="small-muted mb-0">
        Pilih metode pembayaran lalu dapatkan invoice belanja online.
       </p>
      </div>
     </div>
     <div class="col-6">
      <div class="feature-box">
       <strong>
        04
       </strong>
       <h5 class="mt-2">
        Selesai
       </h5>
       <p class="small-muted mb-0">
        Pantau status pesanan dari menu Pesanan Saya.
       </p>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</section>
<?php require "partials/footer.php"; ?>

