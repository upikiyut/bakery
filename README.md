# Bunéa Bakery - 2 Role dengan 1 Halaman Login

Project PHP + MySQL + Bootstrap untuk toko Bunéa Bakery.

## Role
- Pelanggan: belanja cake, keranjang, checkout, pembayaran, riwayat pesanan, profil.
- Admin: dashboard, CRUD produk, kelola pesanan.

## Login
Keduanya memakai **satu halaman login**: `login.php`.

### Demo Admin
- Email: `admin@buneabakery.test`
- Password: `admin123`

### Demo Pelanggan
- Email: `pelanggan@demo.test`
- Password: `password`

Sistem akan mengecek email ke tabel `admin` terlebih dahulu. Jika bukan admin, sistem mengecek tabel `pelanggan`. Setelah login, user otomatis diarahkan sesuai role.

## Logout
Satu file `logout.php` dipakai untuk Admin maupun Pelanggan. Logout dari admin juga kembali ke halaman utama.

## Instalasi singkat
1. Extract folder `bunea_bakery` ke `C:\xampp\htdocs\`.
2. Buat/import database menggunakan `database.sql` melalui phpMyAdmin.
3. Pastikan Apache dan MySQL aktif di XAMPP.
4. Buka `http://localhost/bunea_bakery/`.
5. Klik **Masuk** untuk Admin atau Pelanggan. Tidak ada login admin terpisah.


## Invoice Email
Setelah pembayaran berhasil, sistem membuat halaman invoice bergaya marketplace dan otomatis mencoba mengirim invoice HTML ke email pelanggan menggunakan fungsi `mail()` PHP. Tersedia juga tombol `Kirim Ulang ke Email`. Pada XAMPP/local, SMTP/mail server perlu dikonfigurasi agar email benar-benar terkirim.

### Invoice Email
Setelah pembayaran berhasil, pelanggan diarahkan ke `invoice.php`. Invoice dapat dilihat dan dicetak seperti bukti belanja online, serta tersedia tombol kirim ulang ke email pelanggan. Pengiriman email menggunakan fungsi `mail()` PHP; pada XAMPP/localhost, SMTP/mail server harus dikonfigurasi terlebih dahulu agar email benar-benar masuk ke inbox.


## Pengiriman Invoice via Gmail SMTP
Untuk menjalankan pengiriman invoice dari localhost/XAMPP, buka `config/email.php` dan isi:
- `SMTP_USERNAME` dengan alamat Gmail pengirim.
- `SMTP_APP_PASSWORD` dengan Google App Password 16 karakter, bukan password Gmail biasa.

Pastikan laptop terhubung internet. Email pelanggan pada data akun harus merupakan alamat email yang valid. Tidak perlu membuat database baru.


### Mode Email Demo untuk Tugas Localhost
Versi ini menggunakan `DEMO_EMAIL_MODE = true` di `config/email.php`, sehingga tombol **Kirim Invoice ke Email** dapat didemokan di XAMPP tanpa konfigurasi SMTP. Sistem menampilkan pesan berhasil dan menyimpan salinan HTML email demo di `uploads/email_demo/`. Database tidak perlu dibuat ulang.

Jika suatu saat ingin benar-benar mengirim email melalui Gmail, ubah `DEMO_EMAIL_MODE` menjadi `false` lalu isi `SMTP_USERNAME` dan `SMTP_APP_PASSWORD` dengan kredensial Gmail/App Password.
