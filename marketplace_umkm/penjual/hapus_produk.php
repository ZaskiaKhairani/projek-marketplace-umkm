<?php
include '../koneksi.php';
/** @var mysqli $koneksi */

// Ambil ID produk dari URL
$id = $_GET['id'];

// Perintah SQL untuk menghapus data produk berdasarkan ID
$query = "DELETE FROM produk WHERE id_produk = '$id'";

if (mysqli_query($koneksi, $query)) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location='dashboard_toko.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus produk!'); window.location='dashboard_toko.php';</script>";
}
?>