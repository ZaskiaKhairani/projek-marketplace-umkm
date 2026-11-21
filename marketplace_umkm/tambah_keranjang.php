<?php
session_start();
include 'koneksi.php';

// Cek status login sesuai struktur session NusaKita
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Login Diperlukan - NusaKita</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: 'Silakan Login Terlebih Dahulu',
            text: 'Kamu harus masuk ke akun terlebih dahulu untuk menambahkan produk ke keranjang.',
            icon: 'warning',
            confirmButtonColor: '#198754',
            confirmButtonText: 'Login Sekarang'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>
    </body>
    </html>
    <?php
    exit;
}

// Ambil id_pembeli dari array $_SESSION['user'] (dengan cadangan $_SESSION['id_user'])
$id_pembeli = $_SESSION['user']['id_user'] ?? $_SESSION['id_user'] ?? 0;
$id_produk  = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_pembeli > 0 && $id_produk > 0) {
    // Cek apakah produk sudah ada di keranjang pembeli ini
    $cek = mysqli_query($koneksi, "SELECT * FROM keranjang WHERE id_pembeli = '$id_pembeli' AND id_produk = '$id_produk'");

    if (mysqli_num_rows($cek) > 0) {
        // Jika sudah ada, tambahkan jumlahnya (+1)
        mysqli_query($koneksi, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_pembeli = '$id_pembeli' AND id_produk = '$id_produk'");
    } else {
        // Jika belum ada, masukkan baris baru
        mysqli_query($koneksi, "INSERT INTO keranjang (id_pembeli, id_produk, jumlah) VALUES ('$id_pembeli', '$id_produk', 1)");
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berhasil Masuk Keranjang - NusaKita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        title: 'Berhasil Masuk Keranjang!',
        text: 'Produk berhasil ditambahkan ke keranjang belanja kamu.',
        icon: 'success',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Lihat Keranjang',
        cancelButtonText: 'Lanjut Belanja'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'keranjang.php';
        } else {
            window.location.href = 'index.php';
        }
    });
</script>
</body>
</html>