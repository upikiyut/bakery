<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../config/helpers.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare('SELECT * FROM produk_kue WHERE id_produk=?');
$stmt->bind_param('i', $id);
$stmt->execute();
$produk = $stmt->get_result()->fetch_assoc();

if (!$produk) {
    redirect('produk.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kue'] ?? '');
    $jenis = trim($_POST['jenis_kue'] ?? '');
    $harga = (float)($_POST['harga'] ?? 0);
    $stok = (int)($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $foto = $produk['foto_kue'];

    // Jika admin memilih foto baru, simpan ke folder uploads dan gunakan foto tersebut.
    if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['foto_file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['flash'] = 'Foto gagal diupload. Silakan pilih foto lain.';
            redirect('produk_edit.php?id=' . $id);
        }

        $tmp = $_FILES['foto_file']['tmp_name'];
        $size = (int)$_FILES['foto_file']['size'];
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);

        if (!isset($allowed[$mime])) {
            $_SESSION['flash'] = 'Format foto harus JPG, PNG, WEBP, atau GIF.';
            redirect('produk_edit.php?id=' . $id);
        }

        if ($size > 5 * 1024 * 1024) {
            $_SESSION['flash'] = 'Ukuran foto maksimal 5 MB.';
            redirect('produk_edit.php?id=' . $id);
        }

        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $namaAman = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($nama));
        $namaAman = trim($namaAman, '-_');
        if ($namaAman === '') {
            $namaAman = 'cake';
        }
        $fotoBaru = $namaAman . '-' . $id . '-' . date('YmdHis') . '.' . $allowed[$mime];
        $tujuan = $uploadDir . $fotoBaru;

        if (!move_uploaded_file($tmp, $tujuan)) {
            $_SESSION['flash'] = 'Foto gagal disimpan ke folder uploads.';
            redirect('produk_edit.php?id=' . $id);
        }

        $foto = $fotoBaru;
    }

    $up = $conn->prepare('UPDATE produk_kue SET nama_kue=?,jenis_kue=?,harga=?,stok=?,deskripsi=?,foto_kue=? WHERE id_produk=?');
    $up->bind_param('ssdissi', $nama, $jenis, $harga, $stok, $deskripsi, $foto, $id);
    $up->execute();

    $_SESSION['flash'] = 'Perubahan cake berhasil disimpan.';
    redirect('produk.php');
}

$page_title = 'Edit Cake | Bunéa Bakery';
require __DIR__ . '/partials/header.php';
?>
<div class="row justify-content-center"><div class="col-lg-8"><div class="admin-card p-4 p-md-5"><h1 class="h3 fw-bold">Edit Cake</h1>
<form method="post" enctype="multipart/form-data"><div class="row g-3">
<div class="col-md-6"><label class="form-label">Nama Cake</label><input name="nama_kue" class="form-control" value="<?=e($produk['nama_kue'])?>" required></div>
<div class="col-md-6"><label class="form-label">Jenis Cake</label><input name="jenis_kue" class="form-control" value="<?=e($produk['jenis_kue'])?>" required></div>
<div class="col-md-6"><label class="form-label">Harga</label><input type="number" name="harga" class="form-control" value="<?=e($produk['harga'])?>" min="0" required></div>
<div class="col-md-6"><label class="form-label">Stok</label><input type="number" name="stok" class="form-control" value="<?=e($produk['stok'])?>" min="0" required></div>

<div class="col-12">
    <label class="form-label">Foto Cake</label>
    <div class="border rounded-4 p-3 bg-light-subtle">
        <?php if (!empty($produk['foto_kue'])): ?>
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="../uploads/<?=e($produk['foto_kue'])?>" alt="<?=e($produk['nama_kue'])?>" style="width:110px;height:110px;object-fit:cover;border-radius:18px;border:1px solid #ead9d2;">
                <div>
                    <div class="fw-semibold">Foto saat ini</div>
                    <small class="text-muted"><?=e($produk['foto_kue'])?></small>
                </div>
            </div>
        <?php endif; ?>
        <input type="file" name="foto_file" class="form-control" accept="image/jpeg,image/png,image/webp,image/gif">
        <small class="text-muted d-block mt-2">Pilih foto baru dari laptop. JPG, PNG, WEBP, atau GIF • maksimal 5 MB.</small>
    </div>
</div>

<div class="col-12"><label class="form-label">Deskripsi</label><textarea name="deskripsi" class="form-control" rows="4"><?=e($produk['deskripsi'])?></textarea></div>
</div><div class="d-flex gap-2 mt-4"><a href="#" onclick="history.back(); return false;" class="btn btn-soft">← Kembali</a><button class="btn btn-register" type="submit">Simpan Perubahan</button></div></form>
</div></div></div>
<?php require __DIR__.'/partials/footer.php'; ?>
