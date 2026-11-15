<?php
session_start();
include '../koneksi.php';
/** @var mysqli $koneksi */

// Validasi hak akses penjual
$role = trim(strtolower($_SESSION['user']['role'] ?? ''));
if (!isset($_SESSION['login']) || $role != 'penjual') {
    echo "<script>alert('Akses ditolak! Silakan login menggunakan akun penjual.'); window.location='../login.php';</script>";
    exit;
}

// Ambil data produk dari database
$query_produk = mysqli_query($koneksi, "SELECT * FROM produk");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Toko - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS (Untuk Pop-up Modern) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Eksternal Terpisah -->
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Top Announcement Bar -->
    <div class="announcement-bar text-white py-2 text-center">
        <div class="container">
            <span>🚚 Gratis Ongkir UMKM Seluruh Indonesia &nbsp;|&nbsp; 🔒 100% Pembayaran Aman &nbsp;|&nbsp; ⭐ Dukung Karya Lokal</span>
        </div>
    </div>

    <!-- Navbar Penjual -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard_toko.php">
                <img src="../assets/img/logo.jpg" alt="NusaKita" style="height: 60px; object-fit: contain;">
            </a>
            <span class="badge badge-green px-3 py-2 rounded-pill fw-bold">Panel Penjual UMKM</span>
            
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <span class="text-secondary small fw-semibold">Halo, <?= htmlspecialchars($_SESSION['user']['nama_lengkap'] ?? $_SESSION['nama_lengkap'] ?? 'Penjual'); ?></span>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link fw-semibold text-success" href="tambah_produk.php"><i class="bi bi-plus-circle me-1"></i> Tambah Produk</a>
                </li>
                <li class="nav-item">
                    <!-- Tombol Keluar dengan Konfirmasi Pop-up SweetAlert2 -->
                    <a class="btn btn-sm px-4 fw-bold rounded-pill shadow-sm text-white" href="javascript:void(0);" onclick="konfirmasiKeluar()" style="background-color: #dc3545 !important; border-color: #dc3545 !important;">Keluar</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Konten Dashboard Penjual -->
    <div class="container my-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Kelola Produk Toko Saya</h3>
                <p class="text-muted small mb-0">Tambah, ubah, atau pantau produk UMKM yang kamu tawarkan di NusaKita.</p>
            </div>
            <a href="tambah_produk.php" class="btn btn-success-custom fw-bold py-2 px-4 rounded-pill shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk Baru
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table cart-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3">Foto</th>
                                <th class="py-3">Nama Produk</th>
                                <th class="py-3">Kategori</th>
                                <th class="py-3">Harga</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($query_produk && mysqli_num_rows($query_produk) > 0): ?>
                                <?php $no = 1; while ($p = mysqli_fetch_assoc($query_produk)): ?>
                                <tr>
                                    <td class="px-4 fw-semibold"><?= $no++; ?></td>
                                    <td>
                                        <?php 
                                            $foto_p = trim($p['foto'] ?? '');
                                            $path_p = (!empty($foto_p) && file_exists('../uploads/' . $foto_p)) ? '../uploads/' . $foto_p : 'https://images.unsplash.com/photo-1599487488170-d3319c256053?auto=format&fit=crop&w=200&q=80';
                                        ?>
                                        <img src="<?= $path_p; ?>" alt="" style="width: 50px; height: 50px; object-fit: cover;" class="rounded-3 border">
                                    </td>
                                    <td class="fw-semibold"><?= htmlspecialchars($p['nama_produk'] ?? ''); ?></td>
                                    <td><span class="badge badge-green px-2 py-1"><?= htmlspecialchars($p['kategori'] ?? 'Umum'); ?></span></td>
                                    <td class="fw-bold text-success">Rp<?= number_format($p['harga'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="edit_produk.php?id=<?= $p['id_produk'] ?? 0; ?>" class="btn btn-sm btn-outline-success rounded-pill px-3 me-1">Edit</a>
                                        <!-- Tombol Hapus memicu Pop-up SweetAlert2 -->
                                        <button onclick="konfirmasiHapus(<?= $p['id_produk'] ?? 0; ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-box2 display-5 text-secondary mb-2 d-block"></i>
                                        Belum ada produk yang kamu tambahkan. Yuk klik tombol "Tambah Produk Baru" di atas!
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-white mt-auto pt-4 pb-3" style="background-color: #0f291e;">
        <div class="container text-center small opacity-75">
            &copy; 2026 NusaKita. Panel Khusus Penjual UMKM Indonesia.
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Script JavaScript untuk Pop-up Konfirmasi Modern -->
    <script>
    // Konfirmasi Hapus Produk
    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: "Data produk yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754', 
            cancelButtonColor: '#dc3545',  
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'hapus_produk.php?id=' + id;
            }
        });
    }

    // Konfirmasi Keluar Sistem
    function konfirmasiKeluar() {
        Swal.fire({
            title: 'Keluar dari Panel Penjual?',
            text: "Kamu harus masuk kembali untuk mengelola toko.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', 
            cancelButtonColor: '#198754',  
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-4 shadow-sm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../logout.php';
            }
        });
    }
    </script>
</body>
</html>