<?php
/**
 * Konfigurasi email Bunéa Bakery untuk localhost/XAMPP.
 *
 * Gunakan Gmail + App Password (16 karakter), BUKAN password Gmail biasa.
 * Isi tiga nilai di bawah sebelum mencoba mengirim invoice.
 */
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_PORT = 587;
const SMTP_USERNAME = 'ISI_EMAIL_GMAIL_KAMU';
const SMTP_APP_PASSWORD = 'ISI_APP_PASSWORD_16_KARAKTER';
const SMTP_FROM_NAME = 'Bunéa Bakery';

// Mode demo untuk tugas localhost: tidak membutuhkan SMTP/XAMPP/mail server.
// Invoice dianggap terkirim dan salinan email demo disimpan di uploads/email_demo/.
const DEMO_EMAIL_MODE = true;

function smtp_read($socket): string {
    $data = '';
    while ($line = fgets($socket, 515)) {
        $data .= $line;
        if (strlen($line) < 4 || $line[3] === ' ') break;
    }
    return $data;
}

function smtp_expect($socket, array $codes): bool {
    $response = smtp_read($socket);
    if ($response === '') return false;
    $code = (int) substr(trim($response), 0, 3);
    return in_array($code, $codes, true);
}

function smtp_command($socket, string $command, array $codes): bool {
    fwrite($socket, $command . "\r\n");
    return smtp_expect($socket, $codes);
}

function smtp_dot_escape(string $message): string {
    $message = str_replace(["\r\n", "\r"], "\n", $message);
    $message = str_replace("\n.", "\n..", $message);
    return str_replace("\n", "\r\n", $message);
}

/** Kirim email HTML sederhana melalui Gmail SMTP STARTTLS. */
function kirimEmailSMTP(string $to, string $subject, string $html): bool {
    // DEMO LOCALHOST: tombol email tetap dapat didemokan tanpa akun Gmail/SMTP.
    // Validasi alamat dilewati dalam mode demo agar fitur tetap bisa dipresentasikan.
    if (DEMO_EMAIL_MODE) {
        $dir = __DIR__ . '/../uploads/email_demo';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        $safeTo = preg_replace('/[^a-zA-Z0-9._-]/', '_', $to);
        $safeSubject = preg_replace('/[^a-zA-Z0-9._-]/', '_', $subject);
        $filename = $dir . '/' . date('Ymd_His') . '_' . $safeTo . '_' . $safeSubject . '.html';
        $demoHtml = "<!-- DEMO EMAIL LOCALHOST -->\n<!-- Tujuan: " . htmlspecialchars($to, ENT_QUOTES, 'UTF-8') . " -->\n<!-- Subjek: " . htmlspecialchars($subject, ENT_QUOTES, 'UTF-8') . " -->\n" . $html;
        @file_put_contents($filename, $demoHtml);
        return true;
    }

    if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;

    if (SMTP_USERNAME === 'ISI_EMAIL_GMAIL_KAMU' || SMTP_APP_PASSWORD === 'ISI_APP_PASSWORD_16_KARAKTER') return false;

    $socket = @fsockopen('tcp://' . SMTP_HOST, SMTP_PORT, $errno, $errstr, 15);
    if (!$socket) return false;
    stream_set_timeout($socket, 15);

    try {
        if (!smtp_expect($socket, [220])) throw new Exception('SMTP greeting gagal');
        if (!smtp_command($socket, 'EHLO localhost', [250])) throw new Exception('EHLO gagal');
        if (!smtp_command($socket, 'STARTTLS', [220])) throw new Exception('STARTTLS gagal');

        $crypto = @stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        if ($crypto !== true) throw new Exception('TLS gagal');
        if (!smtp_command($socket, 'EHLO localhost', [250])) throw new Exception('EHLO setelah TLS gagal');
        if (!smtp_command($socket, 'AUTH LOGIN', [334])) throw new Exception('AUTH LOGIN gagal');
        if (!smtp_command($socket, base64_encode(SMTP_USERNAME), [334])) throw new Exception('Username SMTP ditolak');
        if (!smtp_command($socket, base64_encode(SMTP_APP_PASSWORD), [235])) throw new Exception('App Password SMTP ditolak');
        if (!smtp_command($socket, 'MAIL FROM:<' . SMTP_USERNAME . '>', [250])) throw new Exception('MAIL FROM gagal');
        if (!smtp_command($socket, 'RCPT TO:<' . $to . '>', [250, 251])) throw new Exception('RCPT TO gagal');
        if (!smtp_command($socket, 'DATA', [354])) throw new Exception('DATA gagal');

        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers = [];
        $headers[] = 'From: ' . SMTP_FROM_NAME . ' <' . SMTP_USERNAME . '>';
        $headers[] = 'To: <' . $to . '>';
        $headers[] = 'Subject: ' . $encodedSubject;
        $headers[] = 'Date: ' . date(DATE_RFC2822);
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $data = implode("\r\n", $headers) . "\r\n\r\n" . smtp_dot_escape($html) . "\r\n.";
        fwrite($socket, $data . "\r\n");
        if (!smtp_expect($socket, [250])) throw new Exception('Email ditolak server');
        smtp_command($socket, 'QUIT', [221]);
        fclose($socket);
        return true;
    } catch (Throwable $e) {
        @fwrite($socket, "QUIT\r\n");
        @fclose($socket);
        return false;
    }
}
