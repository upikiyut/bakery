<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, "UTF-8");
}

function rupiah($v)
{
    return "Rp " . number_format((float) $v, 0, ",", ".");
}

function redirect($u)
{
    header("Location: $u");
    exit;
}

function is_login()
{
    return isset($_SESSION["pelanggan"]);
}

function is_admin()
{
    return isset($_SESSION["admin"]);
}

function wajib_login()
{
    if (!is_login() && !is_admin()) {
        $_SESSION["flash"] = "Silakan login terlebih dahulu.";
        redirect("login.php");
    }
}

function wajib_admin()
{
    if (!is_admin()) {
        $_SESSION["flash"] = "Silakan masuk sebagai admin terlebih dahulu.";
        redirect("../login.php");
    }
}

function cart_count()
{
    return array_sum($_SESSION["cart"] ?? []);
}

function flash()
{
    if (!empty($_SESSION["flash"])) {
        $m = $_SESSION["flash"];
        unset($_SESSION["flash"]);
        return $m;
    }

    return null;
}
?>
