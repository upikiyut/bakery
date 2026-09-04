<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/helpers.php';
if (!isset($_SESSION['admin'])) {
    redirect('login.php');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title ?? 'Admin Bunéa Bakery') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css?v=2" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-bunea border-bottom">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <span class="brand-mark">B</span>
            <span>Bunéa <small>ADMIN</small></span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="d-none d-md-inline text-muted">Hai, <?= e($_SESSION['admin']['nama']) ?></span>
            <a href="review.php" class="btn btn-soft">⭐ Review</a>
            <a href="../index.php" class="btn btn-soft">Lihat Website</a>
            <a href="../logout.php" class="btn btn-dark-bunea">Keluar</a>
        </div>
    </div>
</nav>
<main class="container py-4">
<?php if ($msg = flash()): ?>
<div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
    <strong>✓ Berhasil!</strong> <?= e($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>
