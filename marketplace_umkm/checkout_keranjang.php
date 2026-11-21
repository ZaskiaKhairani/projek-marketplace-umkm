<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

$id_pembeli = $_SESSION['user']['id_user'] 
    ?? $_SESSION['user']['id'] 
    ?? $_SESSION['id_user'] 
    ?? $_SESSION['id'] 
    ?? 0;

$pilih_keranjang = $_POST['pilih_keranjang'] ?? [];

if ($id_pembeli <= 0 || empty($pilih_keranjang) || !is_array($pilih_keranjang)) {
    header("Location: keranjang.php");
    exit;
}

// Amankan array ID keranjang menjadi integer
$ids = array_map('intval', $pilih_keranjang);
$id_list = implode(',', $ids);

// Ambil hanya item keranjang yang dicentang milik user ini
$query = mysqli_query($koneksi, "
    SELECT keranjang.id_keranjang, keranjang.id_produk, keranjang.jumlah, produk.nama_produk, produk.harga, produk.stok
    FROM keranjang
    JOIN produk ON keranjang.id_produk = produk.id_produk
    WHERE keranjang.id_pembeli = '$id_pembeli' AND keranjang.id_keranjang IN ($id_list)
");

$items_co = [];
$total_harga = 0;
$error_stok = "";

if ($query) {
    while ($row = mysqli_fetch_assoc($query)) {
        if ($row['jumlah'] > $row['stok']) {
            $error_stok = "Stok produk '" . $row['nama_produk'] . "' tidak mencukupi (tersisa " . $row['stok'] . ").";
            break;
        }
        $row['subtotal'] = $row['harga'] * $row['jumlah'];
        $total_harga += $row['subtotal'];
        $items_co[] = $row;
    }
}

$sukses = false;
$pesan_hasil = $error_stok ?: "Tidak ada produk valid yang dipilih.";

if (empty($error_stok) && !empty($items_co)) {
    $tanggal = date('Y-m-d H:i:s');
    $status = 'Menunggu Konfirmasi';

    $insert_pesanan = mysqli_query($koneksi, "INSERT INTO pesanan (id_pembeli, tanggal_pesanan, total_harga, status_pesanan) VALUES ('$id_pembeli', '$tanggal', '$total_harga', '$status')");

    if ($insert_pesanan) {
        $id_pesanan = mysqli_insert_id($koneksi);

        foreach ($items_co as $item) {
            $id_produk    = (int)$item['id_produk'];
            $id_keranjang = (int)$item['id_keranjang'];
            $jumlah       = (int)$item['jumlah'];
            $subtotal     = (int)$item['subtotal'];

            // Simpan ke detail_pesanan
            mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, jumlah_beli, subtotal) VALUES ('$id_pesanan', '$id_produk', '$jumlah', '$jumlah', '$subtotal')");

            // Kurangi stok produk
            mysqli_query($koneksi, "UPDATE produk SET stok = stok - $jumlah WHERE id_produk = '$id_produk'");

            // Hapus HANYA item yang sudah di-checkout dari keranjang
            mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_keranjang = '$id_keranjang' AND id_pembeli = '$id_pembeli'");
        }

        $sukses = true;
        $pesan_hasil = count($items_co) . " produk pilihanmu berhasil diproses!";
    } else {
        $pesan_hasil = "Gagal membuat pesanan: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Checkout - NusaKita</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php if ($sukses): ?>
    Swal.fire({
        title: 'Checkout Berhasil!',
        text: <?= json_encode($pesan_hasil); ?>,
        icon: 'success',
        confirmButtonColor: '#198754',
        confirmButtonText: 'Lihat Riwayat Pesanan'
    }).then(() => {
        window.location.href = 'pesanan_saya.php';
    });
<?php else: ?>
    Swal.fire({
        title: 'Checkout Gagal',
        text: <?= json_encode($pesan_hasil); ?>,
        icon: 'error',
        confirmButtonColor: '#198754',
        confirmButtonText: 'Kembali ke Keranjang'
    }).then(() => {
        window.location.href = 'keranjang.php';
    });
<?php endif; ?>
</script>
</body>
</html>