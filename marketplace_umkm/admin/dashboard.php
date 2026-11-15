<?php
session_start();
include '../koneksi.php';
/** @var mysqli $koneksi */

// Validasi hak akses khusus Admin / Super Admin
$role = trim(strtolower($_SESSION['user']['role'] ?? ''));
if (!isset($_SESSION['login']) || ($role != 'admin' && $role != 'super admin')) {
    echo "<script>alert('Akses ditolak! Halaman ini khusus untuk Admin.'); window.location='../login.php';</script>";
    exit;
}

// Ambil statistik ringkas dengan aman
$jml_user = 0;
$q_user = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users");
if ($q_user) {
    $d_user = mysqli_fetch_assoc($q_user);
    $jml_user = $d_user['total'] ?? 0;
}

$jml_produk = 0;
$q_prod = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
if ($q_prod) {
    $d_prod = mysqli_fetch_assoc($q_prod);
    $jml_produk = $d_prod['total'] ?? 0;
}

$jml_pesanan = 0;
$q_pesanan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan");
if ($q_pesanan) {
    $d_pesanan = mysqli_fetch_assoc($q_pesanan);
    $jml_pesanan = $d_pesanan['total'] ?? 0;
}

// Ambil data user dari database
$result_users = mysqli_query($koneksi, "SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
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

    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <img src="../assets/img/logo.jpg" alt="NusaKita" style="height: 60px; object-fit: contain;">
            </a>
            <span class="badge badge-green px-3 py-2 rounded-pill fw-bold">Panel Super Admin</span>
            
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <span class="text-secondary small fw-semibold">Halo, <?= htmlspecialchars($_SESSION['user']['nama_lengkap'] ?? 'Admin'); ?></span>
                </li>
                <li class="nav-item">
                    <!-- Tombol Keluar Admin dengan SweetAlert2 -->
                    <a class="btn btn-sm px-4 fw-bold rounded-pill shadow-sm text-white" href="javascript:void(0);" onclick="konfirmasiKeluarAdmin()" style="background-color: #dc3545 !important; border-color: #dc3545 !important;">Keluar Sistem</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Konten Dashboard Administrator -->
    <div class="container my-5 flex-grow-1">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1">Dashboard Administrator</h3>
            <p class="text-muted small mb-0">Kelola pengguna, pantau produk, dan tinjau transaksi seluruh platform NusaKita.</p>
        </div>

        <!-- Statistik Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border-start border-success border-4 h-100">
                    <h6 class="text-muted small mb-2">Total Pengguna</h6>
                    <h3 class="fw-bold text-dark mb-1"><?= $jml_user; ?></h3>
                    <p class="text-success small mb-0 fw-semibold">Akun terdaftar</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border-start border-primary border-4 h-100">
                    <h6 class="text-muted small mb-2">Total Produk UMKM</h6>
                    <h3 class="fw-bold text-dark mb-1"><?= $jml_produk; ?></h3>
                    <p class="text-primary small mb-0 fw-semibold">Produk aktif</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white border-start border-warning border-4 h-100">
                    <h6 class="text-muted small mb-2">Total Seluruh Transaksi</h6>
                    <h3 class="fw-bold text-dark mb-1"><?= $jml_pesanan; ?></h3>
                    <p class="text-warning-emphasis small mb-0 fw-semibold">Pesanan masuk</p>
                </div>
            </div>
        </div>

        <!-- Kelola Pengguna -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
            <div class="card-header bg-white py-3 px-4 border-0">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-people text-success me-2"></i> Kelola Pengguna & Verifikasi Akun</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table cart-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="py-3 px-4">No</th>
                                <th class="py-3">Nama Lengkap</th>
                                <th class="py-3">Email</th>
                                <th class="py-3 text-center">Role / Peran</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_users && mysqli_num_rows($result_users) > 0): ?>
                                <?php $no = 1; while ($u = mysqli_fetch_assoc($result_users)): ?>
                                <tr>
                                    <td class="px-4 fw-semibold"><?= $no++; ?></td>
                                    <td class="fw-semibold"><?= htmlspecialchars($u['nama_lengkap'] ?? ''); ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($u['email'] ?? ''); ?></td>
                                    <td class="text-center">
                                        <?php 
                                            $r_item = trim(strtolower($u['role'] ?? 'pembeli'));
                                            $badge_bg = 'bg-secondary';
                                            if ($r_item == 'admin' || $r_item == 'super admin') $badge_bg = 'bg-danger';
                                            elseif ($r_item == 'penjual') $badge_bg = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badge_bg; ?> px-3 py-1 rounded-pill"><?= htmlspecialchars($u['role'] ?? 'pembeli'); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button onclick="konfirmasiHapusUser(<?= $u['id_user'] ?? $u['id'] ?? 0; ?>)" class="btn btn-sm btn-outline-danger rounded-pill px-3">Hapus</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada pengguna terdaftar di database.</td>
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
            &copy; 2026 NusaKita. Panel Super Admin & Pengelola Sistem.
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    function konfirmasiKeluarAdmin() {
        Swal.fire({
            title: 'Keluar dari Panel Admin?',
            text: "Sesi administrator akan diakhiri.",
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

    function konfirmasiHapusUser(id) {
        Swal.fire({
            title: 'Hapus Pengguna Ini?',
            text: "Data akun pengguna akan dihapus permanen!",
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
                window.location.href = 'hapus_user.php?id=' + id;
            }
        });
    }
    </script>
</body>
</html>