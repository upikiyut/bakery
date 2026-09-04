<?php require "config/koneksi.php";require "config/helpers.php";wajib_login();$id=(int)$_SESSION["pelanggan"]["id"];$s=$conn->prepare("SELECT * FROM pelanggan WHERE id_pelanggan=?");$s->bind_param("i",$id);$s->execute();$u=$s->get_result()->fetch_assoc();$page_title="Profil | Bunéa Bakery";require "partials/header.php";?>
<div class="container page-head">
 <div class="section-kicker">
  Bunéa account
 </div>
 <h1>
  Profil Pelanggan
 </h1>
</div>
<div class="container page-actions">
 <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">
  ← Kembali
 </a>
 <a class="btn btn-bunea" href="riwayat.php">
  📋 Lihat Pesanan
 </a>
</div>
<div class="container pb-5">
 <div class="form-card">
  <div class="row g-4">
   <div class="col-md-6">
    <div class="small-muted">
     Nama
    </div>
    <h5>
     <?=e($u["nama_pelanggan"])?>
    </h5>
   </div>
   <div class="col-md-6">
    <div class="small-muted">
     Email
    </div>
    <h5>
     <?=e($u["email"])?>
    </h5>
   </div>
   <div class="col-md-6">
    <div class="small-muted">
     No. Telepon
    </div>
    <h5>
     <?=e($u["no_telepon"])?>
    </h5>
   </div>
   <div class="col-md-6">
    <div class="small-muted">
     Tanggal Daftar
    </div>
    <h5>
     <?=e($u["tanggal_daftar"])?>
    </h5>
   </div>
   <div class="col-12">
    <div class="small-muted">
     Alamat
    </div>
    <h5>
     <?=e($u["alamat"])?>
    </h5>
   </div>
  </div>
  <a class="btn btn-bunea" href="riwayat.php">
   Pesanan Saya
  </a>
  <a class="btn btn-soft" href="#" onclick="history.back(); return false;">
   ← Kembali Sebelumnya
  </a>
 </div>
</div>
<?php require "partials/footer.php";?>

