<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'penjual') {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_pesanan = intval($_GET['id']);
    $status_baru = mysqli_real_escape_string($koneksi, $_GET['status']);

    $query = "UPDATE pesanan SET status_pesanan = '$status_baru' WHERE id_pesanan = '$id_pesanan'";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Status pesanan berhasil diperbarui!'); window.location='pesanan_masuk.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui status pesanan!'); window.location='pesanan_masuk.php';</script>";
    }
}
?>