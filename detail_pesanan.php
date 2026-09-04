<?php require "config/koneksi.php";require "config/helpers.php";wajib_login();$id=(int)($_GET["id"]??0);$pid=(int)$_SESSION["pelanggan"]["id"];$s=$conn->prepare("SELECT * FROM pesanan WHERE id_pesanan=? AND id_pelanggan=?");$s->bind_param("ii",$id,$pid);$s->execute();$o=$s->get_result()->fetch_assoc();if(!$o)redirect("riwayat.php");$s=$conn->prepare("SELECT d.*,p.nama_kue,p.jenis_kue,(SELECT COUNT(*) FROM review rv WHERE rv.id_produk=p.id_produk AND rv.id_pelanggan=? AND rv.id_pesanan=d.id_pesanan) AS sudah_review FROM detail_pesanan d JOIN produk_kue p ON p.id_produk=d.id_produk WHERE d.id_pesanan=?");$s->bind_param("ii",$pid,$id);$s->execute();$r=$s->get_result();$page_title="Detail Pesanan #$id | Bunéa Bakery";require "partials/header.php";?>
<div class="container page-head">
 <div class="section-kicker">
  Order detail
 </div>
 <h1>
  Detail Pesanan #<?=$id?>
 </h1>
</div>
<div class="container page-actions">
 <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">
  ← Kembali
 </a>
 <a class="btn btn-soft" href="#" onclick="history.back(); return false;">
  ← Kembali Sebelumnya
 </a>
</div>
<div class="container pb-5">
 <div class="table-card">
  <div class="d-flex justify-content-between mb-3">
   <span>
    <?=e($o["tanggal_pesanan"])?>
   </span>
   <span class="status status-<?=e($o["status_pesanan"])?>">
    <?=ucfirst(e($o["status_pesanan"]))?>
   </span>
  </div>
  <div class="alert alert-bunea mb-4">
   <h5 class="fw-bold">💳 Informasi Pembayaran</h5>
   <?php
    $pay=$conn->prepare("SELECT tanggal_bayar, metode_pembayaran, jumlah_bayar, status_pembayaran FROM pembayaran WHERE id_pesanan=? LIMIT 1");
    $pay->bind_param("i",$id);
    $pay->execute();
    $payment=$pay->get_result()->fetch_assoc();
   ?>
   <div>Status: <strong><?=ucfirst(e($payment["status_pembayaran"]??"belum lunas"))?></strong></div>
   <div>Metode: <strong><?=e($payment["metode_pembayaran"]??"-")?></strong></div>
   <div>Jumlah Bayar: <strong><?=rupiah($payment["jumlah_bayar"]??0)?></strong></div>
   <div>Tanggal Bayar: <strong><?=e($payment["tanggal_bayar"]??"-")?></strong></div>
  </div>
 <div class="table-responsive">
   <table class="table">
    <thead>
     <tr>
      <th>
       Produk
      </th>
      <th>
       Jumlah
      </th>
      <th>
       Harga Satuan
      </th>
      <th>
       Subtotal
      </th>
     </tr>
    </thead>
    <tbody>
     <?php while($d=$r->fetch_assoc()):?>
     <tr>
      <td>
       <?=e($d["nama_kue"])?>
      </td>
      <td>
       <?=$d["jumlah"]?>
      </td>
      <td>
       <?=rupiah($d["harga_satuan"])?>
      </td>
      <td class="price">
       <?=rupiah($d["subtotal"])?>
      </td>

     </tr>
     <?php endwhile;?>
    </tbody>
    <tfoot>
     <tr>
      <th class="text-end" colspan="3">
       Total
      </th>
      <th class="price">
       <?=rupiah($o["total_harga"])?>
      </th>
     </tr>
    </tfoot>
   </table>
  </div>
  <a class="btn btn-soft" href="riwayat.php">
   ← Kembali ke Pesanan
  </a>
  <a class="btn btn-bunea" href="pembayaran.php?id=<?=$id?>">
   Pembayaran
  </a>
  <a class="btn btn-dark-bunea" href="index.php">
   ⌂ Menu Utama
  </a>
 </div>
</div>
<?php require "partials/footer.php";?>

