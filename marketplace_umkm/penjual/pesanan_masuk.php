<?php
session_start();
include '../koneksi.php';

// Cek hak akses penjual
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'penjual') {
    echo "<script>alert('Silakan login terlebih dahulu sebagai penjual!'); window.location='../login.php';</script>";
    exit;
}

// Ambil data pesanan dan detail produk yang dipesan pembeli
$query = "SELECT pesanan.id_pesanan, pesanan.tanggal_pesanan, pesanan.total_harga, pesanan.status_pesanan, 
                 users.nama_lengkap AS nama_pembeli, produk.nama_produk, detail_pesanan.jumlah, detail_pesanan.subtotal 
          FROM pesanan 
          JOIN users ON pesanan.id_pembeli = users.id_user 
          JOIN detail_pesanan ON pesanan.id_pesanan = detail_pesanan.id_pesanan 
          JOIN produk ON detail_pesanan.id_produk = produk.id_produk 
          ORDER BY pesanan.tanggal_pesanan DESC";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Masuk - UsahaKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar Penjual -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard_toko.php">UsahaKita | Panel Penjual (UMKM)</a>
            <div class="d-flex">
                <a href="dashboard_toko.php" class="btn btn-outline-light btn-sm me-2">Kelola Produk</a>
                
                <!-- 👉 TAMBAHKAN TOMBOL LAPORAN DI SINI JUGA -->
                <a href="laporan.php" class="btn btn-outline-light btn-sm me-2">Laporan Penjualan</a>

                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Keluar Toko</a>
            </div>
        </div>
    </nav>

    <!-- Konten Utama Pesanan Masuk -->
    <div class="container my-5">
        <h3 class="fw-bold mb-4">Daftar Pesanan Masuk dari Pembeli</h3>
        
        <div class="card shadow-sm border-0 rounded-3 p-3 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Pembeli</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['tanggal_pesanan']; ?></td>
                                    <td><?= htmlspecialchars($row['nama_pembeli']); ?></td>
                                    <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                                    <td><?= $row['jumlah']; ?> pcs</td>
                                    <td>Rp<?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($row['status_pesanan'] == 'Pending') : ?>
                                            <span class="badge bg-warning text-dark mb-1">Pending</span><br>
                                            <a href="update_status.php?id=<?= $row['id_pesanan']; ?>&status=Diproses" class="btn btn-sm btn-info py-0 px-1">Proses</a>
                                        <?php elseif ($row['status_pesanan'] == 'Diproses') : ?>
                                            <span class="badge bg-info text-dark mb-1">Diproses</span><br>
                                            <a href="update_status.php?id=<?= $row['id_pesanan']; ?>&status=Selesai" class="btn btn-sm btn-success py-0 px-1">Selesaikan</a>
                                        <?php else : ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada pesanan masuk dari pembeli.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>