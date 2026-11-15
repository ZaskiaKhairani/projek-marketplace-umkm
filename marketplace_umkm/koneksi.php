<?php
// Konfig db
$host = "localhost";
$user = "root";
$pass = "";
$db   = "marketplace_umkm"; 

// sambungkan ke db
$koneksi = mysqli_connect($host, $user, $pass, $db);

// cdk koneksi
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
