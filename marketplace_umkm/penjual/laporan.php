<?php
session_start();
include '../koneksi.php';

// Cek hak akses penjual
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'penjual') {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

$id_penjual = $_SESSION['id_user'];

// Ambil data transaksi pesanan yang masuk
$query_laporan = mysqli_query($koneksi, "SELECT DISTINCT pesanan.*, users.nama_lengkap 
                                         FROM pesanan 
                                         JOIN detail_pesanan ON pesanan.id_pesanan = detail_pesanan.id_pesanan 
                                         JOIN produk ON detail_pesanan.id_produk = produk.id_produk 
                                         JOIN users ON pesanan.id_pembeli = users.id_user 
                                         ORDER BY pesanan.tanggal_pesanan DESC");

// Hitung total pendapatan (hanya status Selesai atau keseluruhan pesanan)
$q_pendapatan = mysqli_query($koneksi, "SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan = 'Selesai'");
$pendapatan = mysqli_fetch_assoc($q_pendapatan)['total'] ?? 0;

$q_total_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan");
$jml_transaksi = mysqli_fetch_assoc($q_total_transaksi)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - UsahaKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <!-- Navbar Penjual -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3 mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard_toko.php">UsahaKita | Panel Penjual</a>
            <div class="d-flex">
                <a href="dashboard_toko.php" class="btn btn-outline-light btn-sm me-2">Kelola Produk</a>
                <a href="pesanan_masuk.php" class="btn btn-outline-light btn-sm me-2">Pesanan Masuk</a>
                <a href="laporan.php" class="btn btn-light btn-sm me-2 fw-bold">Laporan Penjualan</a>
                <a href="../logout.php" class="btn btn-outline-danger btn-sm">Keluar Toko</a>
            </div>
        </div>
    </nav>

    <!-- Konten Utama Laporan -->
    <div class="container my-4">
        <h3 class="fw-bold mb-4">Laporan Penjualan Toko</h3>

        <!-- Ringkasan Statistik Penjual -->
        <div class="row mb-5">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 bg-white p-3 rounded-3">
                    <h6 class="text-muted">Total Pendapatan (Pesanan Selesai)</h6>
                    <h3 class="fw-bold text-success">Rp<?= number_format($pendapatan, 0, ',', '.'); ?></h3>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 bg-white p-3 rounded-3">
                    <h6 class="text-muted">Total Transaksi Masuk</h6>
                    <h3 class="fw-bold text-primary"><?= $jml_transaksi; ?> Pesanan</h3>
                </div>
            </div>
        </div>

        <!-- Tabel Detail Laporan -->
        <div class="card shadow-sm border-0 rounded-3 p-4 bg-white">
            <h5 class="fw-bold mb-3 text-secondary"><i class="bi bi-file-earmark-text"></i> Riwayat Transaksi Penjualan</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>ID Pesanan</th>
                            <th>Tanggal</th>
                            <th>Nama Pembeli</th>
                            <th>Total Belanja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($query_laporan) > 0) : ?>
                            <?php $no = 1; while ($row = mysqli_fetch_assoc($query_laporan)) : ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>#ORD-<?= $row['id_pesanan']; ?></td>
                                    <td><?= $row['tanggal_pesanan']; ?></td>
                                    <td class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td class="text-danger fw-bold">Rp<?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($row['status_pesanan'] === 'Pending') : ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php elseif ($row['status_pesanan'] === 'Diproses') : ?>
                                            <span class="badge bg-info text-dark">Diproses</span>
                                        <?php else : ?>
                                            <span class="badge bg-success">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada laporan penjualan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>