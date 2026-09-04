<?php
require "config/helpers.php";

unset($_SESSION["pelanggan"], $_SESSION["admin"]);

$_SESSION["flash"] = "Kamu sudah keluar dari akun.";
redirect("index.php");
?>
