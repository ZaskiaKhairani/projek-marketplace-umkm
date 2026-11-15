<?php
session_start();
include 'koneksi.php';

// Ambil data pesanan dari database (sesuaikan nama tabel jika berbeda)
// $query = mysqli_query($koneksi, "SELECT * FROM pesanan ORDER BY tanggal DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belanja - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- CSS Eksternal Terpisah -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Top Announcement Bar -->
    <div class="announcement-bar text-white py-2 text-center">
        <div class="container">
            <span>🚚 Gratis Ongkir UMKM Seluruh Indonesia &nbsp;|&nbsp; 🔒 100% Pembayaran Aman &nbsp;|&nbsp; ⭐ Dukung Karya Lokal</span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <img src="assets/img/logo.jpg" alt="NusaKita">
            </a>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link fw-semibold text-dark" href="index.php"><i class="bi bi-shop me-1 text-success"></i> Katalog Produk</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link fw-semibold text-dark" href="keranjang.php"><i class="bi bi-cart3 me-1 text-success"></i> Keranjang</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link fw-semibold text-success active" href="pesanan_saya.php"><i class="bi bi-clock-history me-1"></i> Riwayat</a>
                    </li>
                    <?php if (isset($_SESSION['login'])): ?>
                        <li class="nav-item">
                            <a class="btn btn-success-custom btn-sm px-4 fw-bold rounded-pill shadow-sm" href="logout.php">Keluar</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-success-custom btn-sm px-4 fw-bold rounded-pill shadow-sm" href="login.php">Masuk</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content: Riwayat Belanja Saya -->
    <div class="container my-5 flex-grow-1">
        <h3 class="fw-bold text-dark mb-4"><i class="bi bi-clock-history text-success me-2"></i> Riwayat Belanja Saya</h3>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table cart-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3">Tanggal Pesanan</th>
                                <th class="py-3">Nama Produk</th>
                                <th class="py-3 text-center">Jumlah</th>
                                <th class="py-3">Total Subtotal</th>
                                <th class="py-3 text-center">Status Pesanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contoh data statis sesuai riwayatmu, nanti bisa dihubungkan ke while loop database -->
                            <tr>
                                <td class="px-4 fw-semibold">1</td>
                                <td>2026-11-14 17:23:10</td>
                                <td class="fw-semibold">Keripik Pisang</td>
                                <td class="text-center">3 pcs</td>
                                <td class="fw-bold text-success">Rp45.000</td>
                                <td class="text-center">
                                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill fw-semibold">Diproses</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-4 fw-semibold">2</td>
                                <td>2026-11-14 17:22:59</td>
                                <td class="fw-semibold">Keripik Pisang</td>
                                <td class="text-center">1 pcs</td>
                                <td class="fw-bold text-success">Rp15.000</td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-semibold">Pending</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Profesional -->
    <footer class="text-white mt-auto pt-5 pb-3" style="background-color: #0f291e;">
        <div class="container mb-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="fw-bold text-success mb-3">NusaKita</h5>
                    <p class="text-light small opacity-75">Platform marketplace terdepan yang menghubungkan karya terbaik UMKM lokal langsung ke tangan masyarakat Indonesia.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-white mb-3">Kategori Pilihan</h6>
                    <ul class="list-unstyled small text-light opacity-75">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-light">Kuliner Nusantara</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-light">Fashion & Aksesoris</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-light">Kerajinan Tangan</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold text-white mb-3">Bantuan</h6>
                    <ul class="list-unstyled small text-light opacity-75">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-light">Lacak Pesanan</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-light">Kebijakan Retur</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-light">Hubungi Kami</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-white mb-3">Tetap Terhubung</h6>
                    <p class="small text-light opacity-75">Dapatkan info promo dan produk UMKM terbaru langsung ke inbox kamu.</p>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start small text-light opacity-75">
                    &copy; 2026 NusaKita. Marketplace pilihan untuk karya terbaik UMKM Indonesia.
                </div>
                <div class="col-md-6 text-center text-md-end small text-light opacity-75">
                    <span>Indonesia 🇮🇩</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>