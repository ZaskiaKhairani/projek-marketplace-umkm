<?php
session_start();
include '../koneksi.php';

// Cek hak akses admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

if (isset($_GET['id'])) {
    $id_user = intval($_GET['id']);
    
    // Jangan biarkan admin menghapus akunnya sendiri yang sedang aktif
    if ($id_user == $_SESSION['id_user']) {
        echo "<script>alert('Anda tidak dapat menghapus akun admin yang sedang aktif!'); window.location='dashboard.php';</script>";
        exit;
    }

    mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id_user'");
    echo "<script>alert('Pengguna berhasil dihapus dari sistem.'); window.location='dashboard.php';</script>";
} else {
    header("Location: dashboard.php");
    exit;
}
?>