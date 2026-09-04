<?php
require_once __DIR__ . '/../config/koneksi.php';
$id=(int)($_GET['id']??0);
$stmt=$conn->prepare('DELETE FROM produk_kue WHERE id_produk=?'); $stmt->bind_param('i',$id); $stmt->execute(); redirect('produk.php');
