<?php
require "config/koneksi.php";
require "config/helpers.php";

if (is_login() || is_admin()) {
    redirect(is_admin() ? "admin/index.php" : "index.php");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $pw = $_POST["password"] ?? "";

    // Cek admin terlebih dahulu. Satu halaman login dipakai untuk 2 role.
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($pw, $admin["password"])) {
        $_SESSION["admin"] = [
            "id" => $admin["id_admin"],
            "nama" => $admin["nama_admin"],
            "email" => $admin["email"]
        ];
        $_SESSION["flash"] = "Selamat datang, Admin " . $admin["nama_admin"] . "!";
        redirect("admin/index.php");
    }

    // Kalau bukan admin, cek akun pelanggan.
    $stmt = $conn->prepare("SELECT * FROM pelanggan WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $pelanggan = $stmt->get_result()->fetch_assoc();

    if ($pelanggan && password_verify($pw, $pelanggan["password"])) {
        $_SESSION["pelanggan"] = [
            "id" => $pelanggan["id_pelanggan"],
            "nama" => $pelanggan["nama_pelanggan"],
            "email" => $pelanggan["email"]
        ];
        $_SESSION["flash"] = "Selamat datang " . $pelanggan["nama_pelanggan"] . " di Bunéa Bakery! 💗";
        redirect("index.php");
    }

    $error = "Email atau password salah.";
}

$page_title = "Masuk | Bunéa Bakery";
require "partials/header.php";
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="form-card">
                <div class="text-center">
                    <div class="brand-mark mx-auto mb-3">B</div>
                    <div class="section-kicker">Bunéa account</div>
                    <h1>Selamat datang</h1>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <label class="form-label">Email</label>
                    <input
                        class="form-control mb-3"
                        name="email"
                        type="email"
                        required
                        autocomplete="email"
                    >

                    <label class="form-label">Password</label>
                    <input
                        class="form-control mb-4"
                        name="password"
                        type="password"
                        required
                        autocomplete="current-password"
                    >

                    <button class="btn btn-bunea w-100" type="submit">
                        Masuk
                    </button>
                </form>

                <p class="text-center small-muted mt-3">
                    Belum punya akun?
                    <a href="register.php">Daftar sebagai pelanggan</a>
                </p>

                <div class="d-flex gap-2 justify-content-center mt-3">
                    <a class="btn btn-soft btn-sm" href="#" onclick="history.back(); return false;">
                        ← Kembali
                    </a>
                    </a>
                </div>

                <div class="login-info mt-4">
                    <strong>Demo Admin</strong><br>
                    Email: admin@buneabakery.test<br>
                    Password: admin123
                </div>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php"; ?>
