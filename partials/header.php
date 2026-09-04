<?php require_once __DIR__."/../config/helpers.php"; ?>
<!DOCTYPE html>
<html lang="id">
 <head>
  <meta charset="utf-8"/>
  <meta content="width=device-width,initial-scale=1" name="viewport"/>
  <title>
   <?=e($page_title??"Bunéa Bakery")?>
  </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com" rel="preconnect"/>
  <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet"/>
  <link href="assets/css/style.css?v=2" rel="stylesheet"/>
 </head>
 <body>
  <nav class="navbar navbar-expand-lg navbar-bunea sticky-top">
   <div class="container">
    <a class="navbar-brand" href="index.php">
     <span class="brand-mark">
      B
     </span>
     <span>
      Bunéa
      <small>
       BAKERY
      </small>
     </span>
    </a>
    <button class="navbar-toggler" data-bs-target="#nav" data-bs-toggle="collapse" type="button">
     <span class="navbar-toggler-icon">
     </span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
     <ul class="navbar-nav mx-auto gap-lg-2">
      <li>
       <a class="nav-link" href="index.php">
        ⌂ Beranda
       </a>
      </li>
      <li>
       <a class="nav-link nav-shop" href="produk.php">
        🍰 Belanja Cake
       </a>
      </li>
      <li>
       <a class="nav-link" href="riwayat.php">
        📋 Pesanan Saya
       </a>
      </li>
     </ul>
     <div class="d-flex align-items-center gap-2">
      <a class="cart-pill" href="keranjang.php" title="Buka keranjang">
       🛒 Keranjang
       <span>
        <?=cart_count()?>
       </span>
      </a>
      <?php if (is_admin()): ?>
      <a class="btn btn-soft" href="admin/index.php">
       ⚙️ Dashboard Admin
      </a>
      <a class="btn btn-dark-bunea" href="logout.php">
       Keluar
      </a>
      <?php elseif (is_login()): ?>
      <a class="btn btn-soft" href="profil.php">
       Hai, <?=e($_SESSION["pelanggan"]["nama"])?>
      </a>
      <a class="btn btn-dark-bunea" href="logout.php">
       Keluar
      </a>
      <?php else: ?>
      <a class="btn btn-dark-bunea btn-login" href="login.php">
       Masuk
      </a>
      <a class="btn btn-register" href="register.php">
       Daftar
      </a>
      <?php endif;?>
     </div>
    </div>
   </div>
  </nav>
  <main>
  <?php if($m=flash()): ?>
  <div class="container mt-3">
   <div class="alert alert-bunea">
    <?=$m?>
   </div>
  </div>
  <?php endif;?>

