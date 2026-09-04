<?php require "config/koneksi.php";require "config/helpers.php";
if(isset($_GET["hapus"])){unset($_SESSION["cart"][(int)$_GET["hapus"]]);$_SESSION["flash"]="Produk dihapus dari keranjang.";redirect("keranjang.php");}
if($_SERVER["REQUEST_METHOD"]==="POST"&&isset($_POST["jumlah"])){foreach($_POST["jumlah"] as $id=>$n){$n=max(0,(int)$n);if($n===0)unset($_SESSION["cart"][(int)$id]);else $_SESSION["cart"][(int)$id]=$n;}$_SESSION["flash"]="Keranjang diperbarui.";redirect("keranjang.php");}
$cart=$_SESSION["cart"]??[];$items=[];$total=0;if($cart){$ids=implode(",",array_map("intval",array_keys($cart)));$r=$conn->query("SELECT * FROM produk_kue WHERE id_produk IN($ids)");while($p=$r->fetch_assoc()){$q=(int)($cart[$p["id_produk"]]??0);$q=min($q,(int)$p["stok"]);if($q>0){$p["qty"]=$q;$p["subtotal"]=$q*$p["harga"];$items[]=$p;$total+=$p["subtotal"];$_SESSION["cart"][$p["id_produk"]]=$q;}}}
$page_title="Keranjang | Bunéa Bakery";require "partials/header.php";?>
<div class="container page-head">
 <div class="section-kicker">
  Your little basket
 </div>
 <h1>
  Keranjang Belanja
 </h1>
</div>
<div class="container page-actions">
 <a class="btn btn-outline-bunea" href="#" onclick="history.back(); return false;">
  ← Kembali
 </a>
 <a class="btn btn-soft" href="index.php">
  ⌂ Menu Utama
 </a>
</div>
<div class="container pb-5">
 <?php if(!$items): ?>
 <div class="empty">
  <div class="display-5">
   🧺
  </div>
  <h3>
   Keranjangmu masih kosong
  </h3>
  <p class="small-muted">
   Yuk pilih cake favoritmu.
  </p>
  <a class="btn btn-bunea" href="produk.php">
   Lihat Koleksi
  </a>
 </div>
 <?php else: ?>
 <form method="post">
  <div class="table-card">
   <div class="table-responsive">
    <table class="table align-middle">
     <thead>
      <tr>
       <th>
        Cake
       </th>
       <th>
        Harga
       </th>
       <th>
        Jumlah
       </th>
       <th>
        Subtotal
       </th>
       <th>
       </th>
      </tr>
     </thead>
     <tbody>
      <?php foreach($items as $i): ?>
      <tr>
       <td>
        <strong>
         <?=e($i["nama_kue"])?>
        </strong>
        <br/>
        <span class="small-muted">
         <?=e($i["jenis_kue"])?>
        </span>
       </td>
       <td>
        <?=rupiah($i["harga"])?>
       </td>
       <td>
        <input class="form-control" max="<?=$i["stok"]?>" min="0" name="jumlah[<?=$i["id_produk"]?>]" style="max-width:130px" type="number" value="<?=$i["qty"]?>"/>
       </td>
       <td class="price">
        <?=rupiah($i["subtotal"])?>
       </td>
       <td>
        <a class="btn btn-sm btn-outline-danger" data-confirm="Hapus cake ini?" href="keranjang.php?hapus=<?=$i["id_produk"]?>">
         Hapus
        </a>
       </td>
      </tr>
      <?php endforeach;?>
     </tbody>
    </table>
   </div>
   <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
    <a class="btn btn-soft" href="#" onclick="history.back(); return false;">
     ← Kembali Sebelumnya
    </a>
    <div>
     <span class="small-muted">
      Total
     </span>
     <span class="fs-4 price">
      <?=rupiah($total)?>
     </span>
    </div>
    <button class="btn btn-soft">
     Update
    </button>
    <a class="btn btn-bunea" href="checkout.php">
     Lanjut Checkout →
    </a>
   </div>
  </div>
 </form>
 <?php endif;?>
</div>
<?php require "partials/footer.php"; ?>

