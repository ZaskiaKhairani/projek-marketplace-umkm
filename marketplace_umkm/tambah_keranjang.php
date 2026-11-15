<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pembeli') {
    echo "<script>alert('Silakan login terlebih dahulu sebagai pembeli!'); window.location='login.php';</script>";
    exit;
}

$id_pembeli = $_SESSION['id_user'];
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Cek apakah produk sudah ada di keranjang pembeli ini
$cek = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_pembeli = '$id_pembeli' AND id_produk = '$id_produk'");

if (mysqli_num_rows($cek) > 0) {
    // Jika sudah ada, tambahkan jumlahnya (+1)
    mysqli_query($koneksi, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_pembeli = '$id_pembeli' AND id_produk = '$id_produk'");
} else {
    // Jika belum ada, masukkan baris baru
    mysqli_query($koneksi, "INSERT INTO keranjang (id_pembeli, id_produk, jumlah) VALUES ('$id_pembeli', '$id_produk', 1)");
}

echo "<script>alert('Produk berhasil ditambahkan ke keranjang!'); window.location='keranjang.php';</script>";
?>