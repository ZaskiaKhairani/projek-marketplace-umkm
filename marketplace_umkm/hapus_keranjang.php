<?php
session_start();
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id_keranjang = intval($_GET['id']);
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_keranjang = '$id_keranjang'");
}

header("Location: keranjang.php");
exit;
?>