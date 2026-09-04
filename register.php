<?php require "config/koneksi.php";require "config/helpers.php";if(is_login())redirect("index.php");$error="";
if($_SERVER["REQUEST_METHOD"]==="POST"){$nama=trim($_POST["nama_pelanggan"]??"");$tel=trim($_POST["no_telepon"]??"");$email=trim($_POST["email"]??"");$alamat=trim($_POST["alamat"]??"");$pw=$_POST["password"]??"";
if(!$nama||!$tel||!$email||!$alamat||strlen($pw)<6)$error="Semua data wajib diisi dan password minimal 6 karakter.";else{$s=$conn->prepare("SELECT id_pelanggan FROM pelanggan WHERE email=?");$s->bind_param("s",$email);$s->execute();if($s->get_result()->num_rows)$error="Email sudah terdaftar.";else{$h=password_hash($pw,PASSWORD_DEFAULT);$s=$conn->prepare("INSERT INTO pelanggan(nama_pelanggan,no_telepon,email,alamat,tanggal_daftar,password) VALUES(?,?,?,?,CURDATE(),?)");$s->bind_param("sssss",$nama,$tel,$email,$alamat,$h);if($s->execute()){$_SESSION["flash"]="Pendaftaran berhasil. Silakan login.";redirect("login.php");}else $error="Pendaftaran gagal.";}}}
$page_title="Daftar | Bunéa Bakery";require "partials/header.php";?>
<div class="container py-5">
 <div class="row justify-content-center">
  <div class="col-lg-6">
   <div class="form-card">
    <div class="section-kicker">
     Welcome to Bunéa
    </div>
    <h1>
     Buat Akun
    </h1>
    <p class="small-muted">
     Daftar untuk menyimpan pesanan dan pembayaranmu.
    </p>
    <?php if($error):?>
    <div class="alert alert-danger">
     <?=e($error)?>
    </div>
    <?php endif;?>
    <form method="post">
     <label class="form-label">
      Nama Pelanggan
     </label>
     <input class="form-control mb-3" name="nama_pelanggan" required=""/>
     <div class="row g-3">
      <div class="col-md-6">
       <label class="form-label">
        No. Telepon
       </label>
       <input class="form-control" name="no_telepon" required=""/>
      </div>
      <div class="col-md-6">
       <label class="form-label">
        Email
       </label>
       <input class="form-control" name="email" required="" type="email"/>
      </div>
     </div>
     <label class="form-label mt-3">
      Alamat
     </label>
     <textarea class="form-control mb-3" name="alamat" required=""></textarea>
     <label class="form-label">
      Password
     </label>
     <input class="form-control mb-4" minlength="6" name="password" required="" type="password"/>
     <button class="btn btn-bunea w-100">
      Daftar Sekarang
     </button>
    </form>
    <p class="text-center small-muted mt-3">
     Sudah punya akun?
     <a href="login.php">
      Masuk
     </a>
    </p>
    <div class="d-flex gap-2 justify-content-center mt-3">
     <a class="btn btn-soft btn-sm" href="index.php">
      ← Menu Utama
     </a>
     <a class="btn btn-outline-bunea btn-sm" href="produk.php">
      🍰 Lihat Cake
     </a>
    </div>
   </div>
  </div>
 </div>
</div>
<?php require "partials/footer.php"; ?>

