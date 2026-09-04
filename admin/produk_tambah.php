<?php
require_once __DIR__ . '/../config/koneksi.php';
$page_title = 'Tambah Cake | Bunéa Bakery';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kue']);
    $jenis = trim($_POST['jenis_kue']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $deskripsi = trim($_POST['deskripsi']);
    $foto = trim($_POST['foto_kue']);
    $stmt = $conn->prepare('INSERT INTO produk_kue (id_toko,nama_kue,jenis_kue,harga,stok,deskripsi,foto_kue) VALUES (1,?,?,?,?,?,?)');
    $stmt->bind_param('issdiss', $nama, $jenis, $harga, $stok, $deskripsi, $foto);
    $stmt->execute();
    redirect('produk.php');
}
require __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="admin-card p-4 p-md-5">
<h1 class="h3 fw-bold">Tambah Cake</h1><p class="text-muted">Masukkan informasi produk baru.</p>
<form method="post">
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Nama Cake</label><input name="nama_kue" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Jenis Cake</label><input name="jenis_kue" class="form-control" required></div>
<div class="col-md-6"><label class="form-label">Harga</label><input type="number" name="harga" class="form-control" min="0" required></div>
<div class="col-md-6"><label class="form-label">Stok</label><input type="number" name="stok" class="form-control" min="0" required></div>
<div class="col-12"><label class="form-label">Nama File Foto</label><input name="foto_kue" class="form-control" placeholder="contoh: strawberry-cloud.svg"></div>
<div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"></textarea></div>
</div>
<div class="d-flex gap-2 mt-4"><a href="#" onclick="history.back(); return false;" class="btn btn-soft">← Kembali</a><button class="btn btn-register">Simpan Cake</button></div>
</form></div></div></div>
<?php require __DIR__ . '/partials/footer.php'; ?>
