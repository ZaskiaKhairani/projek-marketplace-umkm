<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'pembeli') {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='login.php';</script>";
    exit;
}

$id_pembeli = $_SESSION['id_user'];
$tanggal = date('Y-m-d H:i:s');

// Ambil semua item di keranjang pembeli
$query_cart = mysqli_query($koneksi, "SELECT keranjang.*, produk.harga FROM keranjang JOIN produk ON keranjang.id_produk = produk.id_produk WHERE keranjang.id_pembeli = '$id_pembeli'");

if (mysqli_num_rows($query_cart) == 0) {
    echo "<script>alert('Keranjang belanja kosong!'); window.location='keranjang.php';</script>";
    exit;
}

$total_harga = 0;
$items = [];
while ($row = mysqli_fetch_assoc($query_cart)) {
    $subtotal = $row['harga'] * $row['jumlah'];
    $total_harga += $subtotal;
    $items[] = $row;
}

// 1. Simpan ke tabel pesanan
$query_pesanan = "INSERT INTO pesanan (id_pembeli, tanggal_pesanan, total_harga, status_pesanan) 
                VALUES ('$id_pembeli', '$tanggal', '$total_harga', 'Pending')";

if (mysqli_query($koneksi, $query_pesanan)) {
    $id_pesanan_baru = mysqli_insert_id($koneksi);

    // 2. Simpan setiap item ke detail_pesanan
    foreach ($items as $item) {
        $id_produk = $item['id_produk'];
        $jumlah = $item['jumlah'];
        $subtotal = $item['harga'] * $jumlah;

        mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal) 
                                VALUES ('$id_pesanan_baru', '$id_produk', '$jumlah', '$subtotal')");
    }

    // 3. Kosongkan keranjang
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_pembeli = '$id_pembeli'");

    echo "<script>alert('Checkout berhasil! Pesanan kamu sedang diproses.'); window.location='pesanan_saya.php';</script>";
} else {
    echo "<script>alert('Gagal melakukan checkout!'); window.location='keranjang.php';</script>";
}
?>