<?php
// Konfig db
$host = "sql109.infinityfree.com";
$user = "if0_43448786";
$pass = "ZADkia12345";
$db   = "if0_43448786_marketplace_umkm";

// sambungkan ke db
$koneksi = mysqli_connect($host, $user, $pass, $db);

// cdk koneksi
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
