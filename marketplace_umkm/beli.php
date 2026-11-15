<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    echo "<script>alert('Silakan masuk terlebih dahulu untuk melakukan pembelian!'); window.location='login.php';</script>";
    exit;
}

// Ambil ID produk dari URL
$id_produk = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data produk yang dipilih
$result = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
$produk = mysqli_fetch_assoc($result);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// Jika tombol konfirmasi pesanan diklik
if (isset($_POST['proses_pesanan'])) {
    $id_pembeli  = $_SESSION['id_user'];
    $jumlah      = intval($_POST['jumlah']);
    $total_harga = $produk['harga'] * $jumlah;
    $tanggal     = date('Y-m-d H:i:s');

    // 1. Masukkan data ke tabel pesanan
    $query_pesanan = "INSERT INTO pesanan (id_pembeli, tanggal_pesanan, total_harga, status_pesanan) 
                      VALUES ('$id_pembeli', '$tanggal', '$total_harga', 'Pending')";
    
    if (mysqli_query($koneksi, $query_pesanan)) {
        // Ambil ID pesanan yang baru saja dimasukkan
        $id_pesanan_baru = mysqli_insert_id($koneksi);

        // 2. Masukkan data ke tabel detail_pesanan (menyesuaikan kolom: jumlah & subtotal)
        $id_produk_val = $produk['id_produk'];
        $subtotal      = $produk['harga'] * $jumlah;
        
        $query_detail  = "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal) 
                          VALUES ('$id_pesanan_baru', '$id_produk_val', '$jumlah', '$subtotal')";
        
        if (mysqli_query($koneksi, $query_detail)) {
            echo "<script>alert('Pesanan berhasil dibuat! Terima kasih sudah berbelanja.'); window.location='index.php';</script>";
            exit;
        } else {
            $pesan_error = mysqli_error($koneksi);
            echo "<script>alert('Gagal pada detail pesanan: " . addslashes($pesan_error) . "');</script>";
        }
    } else {
        $pesan_error = mysqli_error($koneksi);
        echo "<script>alert('Gagal pada pesanan: " . addslashes($pesan_error) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - UsahaKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 rounded-3 p-4 bg-white">
                    <h3 class="fw-bold mb-3">Konfirmasi Pembelian</h3>
                    <hr>
                    <h5 class="fw-bold text-dark"><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                    <p class="text-muted small">Kategori: <?= htmlspecialchars($produk['kategori']); ?></p>
                    <p class="text-danger fw-bold fs-5">Harga Satuan: Rp<?= number_format($produk['harga'], 0, ',', '.'); ?></p>
                    <p class="text-muted small">Stok Tersedia: <?= $produk['stok']; ?></p>

                    <form action="" method="POST" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Jumlah Pembelian</label>
                            <input type="number" name="jumlah" class="form-control" value="1" min="1" max="<?= $produk['stok']; ?>" required>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" name="proses_pesanan" class="btn btn-dark">Proses Pesanan Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>