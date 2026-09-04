<?php
require_once __DIR__ . '/email.php';

function kirimInvoiceEmail(mysqli $conn, int $idPesanan): bool {
    $stmt = $conn->prepare(
        "SELECT p.*, pl.nama_pelanggan, pl.email, py.tanggal_bayar, py.metode_pembayaran, py.jumlah_bayar, py.status_pembayaran
         FROM pesanan p
         JOIN pelanggan pl ON pl.id_pelanggan = p.id_pelanggan
         LEFT JOIN pembayaran py ON py.id_pesanan = p.id_pesanan
         WHERE p.id_pesanan = ?"
    );
    $stmt->bind_param("i", $idPesanan);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    if (!$order || empty($order['email'])) return false;

    $stmt = $conn->prepare(
        "SELECT d.jumlah, d.harga_satuan, d.subtotal, p.nama_kue
         FROM detail_pesanan d
         JOIN produk_kue p ON p.id_produk = d.id_produk
         WHERE d.id_pesanan = ?"
    );
    $stmt->bind_param("i", $idPesanan);
    $stmt->execute();
    $items = $stmt->get_result();

    $rows = '';
    while ($item = $items->fetch_assoc()) {
        $nama = htmlspecialchars($item['nama_kue'], ENT_QUOTES, 'UTF-8');
        $qty = (int)$item['jumlah'];
        $harga = number_format((float)$item['harga_satuan'], 0, ',', '.');
        $subtotal = number_format((float)$item['subtotal'], 0, ',', '.');
        $rows .= "<tr><td style='padding:10px;border-bottom:1px solid #eee'>{$nama}</td><td style='padding:10px;border-bottom:1px solid #eee;text-align:center'>{$qty}</td><td style='padding:10px;border-bottom:1px solid #eee;text-align:right'>Rp {$harga}</td><td style='padding:10px;border-bottom:1px solid #eee;text-align:right'>Rp {$subtotal}</td></tr>";
    }

    $nama = htmlspecialchars($order['nama_pelanggan'], ENT_QUOTES, 'UTF-8');
    $metode = htmlspecialchars($order['metode_pembayaran'] ?: '-', ENT_QUOTES, 'UTF-8');
    $tanggal = $order['tanggal_bayar'] ? date('d/m/Y H:i', strtotime($order['tanggal_bayar'])) : date('d/m/Y H:i');
    $total = number_format((float)$order['total_harga'], 0, ',', '.');

    $subject = "Invoice Pesanan #{$idPesanan} - Bunéa Bakery";
    $message = "<!doctype html><html><body style='font-family:Arial,sans-serif;background:#fff7fb;padding:24px;color:#493943'>
    <div style='max-width:680px;margin:auto;background:#fff;border:1px solid #f0dbe7;border-radius:18px;padding:28px'>
      <h2 style='margin:0;color:#b84f7d'>Bunéa Bakery</h2>
      <p>Halo <strong>{$nama}</strong> 💗</p>
      <p>Terima kasih sudah berbelanja. Berikut invoice pesanan kamu:</p>
      <div style='background:#fff0f6;border-radius:12px;padding:14px;margin:18px 0'><strong>Invoice #{$idPesanan}</strong><br>Tanggal: {$tanggal}<br>Metode pembayaran: {$metode}</div>
      <table style='width:100%;border-collapse:collapse'><thead><tr><th style='padding:10px;text-align:left;border-bottom:2px solid #f0dbe7'>Produk</th><th style='padding:10px;border-bottom:2px solid #f0dbe7'>Qty</th><th style='padding:10px;text-align:right;border-bottom:2px solid #f0dbe7'>Harga</th><th style='padding:10px;text-align:right;border-bottom:2px solid #f0dbe7'>Subtotal</th></tr></thead><tbody>{$rows}</tbody></table>
      <div style='text-align:right;font-size:20px;font-weight:bold;margin-top:20px'>Total: Rp {$total}</div>
      <p style='margin-top:28px'>Pesanan kamu sedang diproses. Simpan email ini sebagai bukti pembelian.</p>
      <p style='color:#b84f7d;font-weight:bold'>Terima kasih sudah mampir ke Bunéa Bakery! 💗</p>
    </div></body></html>";

    return kirimEmailSMTP($order['email'], $subject, $message);

}
