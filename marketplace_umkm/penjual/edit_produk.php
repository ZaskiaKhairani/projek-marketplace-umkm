<?php
session_start();
include '../koneksi.php';
/** @var mysqli $koneksi */

// Validasi hak akses penjual
$role = trim(strtolower($_SESSION['user']['role'] ?? ''));
if (!isset($_SESSION['login']) || $role != 'penjual') {
    echo "<script>alert('Akses ditolak!'); window.location='../login.php';</script>";
    exit;
}

// Ambil ID produk dari URL menggunakan id_produk yang sesuai database
$id = $_GET['id'] ?? 0;
$query = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id'");
$p = mysqli_fetch_assoc($query);

if (!$p) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='dashboard_toko.php';</script>";
    exit;
}

// Proses update data produk
if (isset($_POST['update'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok'] ?? 0);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    $update = mysqli_query($koneksi, "UPDATE produk SET nama_produk = '$nama_produk', kategori = '$kategori', harga = '$harga', stok = '$stok', deskripsi = '$deskripsi' WHERE id_produk = '$id'");

    if ($update) {
        echo "<script>alert('Produk berhasil diperbarui!'); window.location='dashboard_toko.php';</script>";
    } else {
        $error = "Gagal memperbarui produk ke database.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- CSS Eksternal Terpisah -->
    <link rel="stylesheet" href="../style.css">
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- Navbar Penjual -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard_toko.php">
                <img src="../assets/img/logo.jpg" alt="NusaKita" style="height: 60px; object-fit: contain;">
            </a>
            <a href="dashboard_toko.php" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold"><i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard</a>
        </div>
    </nav>

    <!-- Konten Form Edit Produk -->
    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-dark mb-4"><i class="bi bi-pencil-square text-success me-2"></i> Edit Produk UMKM</h4>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger py-2 small rounded-3"><?= $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Nama Produk</label>
                            <input type="text" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="nama_produk" value="<?= htmlspecialchars($p['nama_produk'] ?? ''); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Kategori</label>
                            <select class="form-select rounded-3 py-2 shadow-none border-success-subtle" name="kategori" required>
                                <option value="Kuliner" <?= (($p['kategori'] ?? '') == 'Kuliner') ? 'selected' : ''; ?>>Kuliner Nusantara</option>
                                <option value="Fashion" <?= (($p['kategori'] ?? '') == 'Fashion') ? 'selected' : ''; ?>>Fashion Lokal</option>
                                <option value="Kerajinan" <?= (($p['kategori'] ?? '') == 'Kerajinan') ? 'selected' : ''; ?>>Kerajinan Tangan</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Harga (Rp)</label>
                                <input type="number" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="harga" value="<?= htmlspecialchars($p['harga'] ?? 0); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Stok</label>
                                <input type="number" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="stok" value="<?= htmlspecialchars($p['stok'] ?? 0); ?>" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary">Deskripsi Produk</label>
                            <textarea class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="deskripsi" rows="4" required><?= htmlspecialchars($p['deskripsi'] ?? ''); ?></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="dashboard_toko.php" class="btn btn-outline-success px-4 fw-bold rounded-pill">Kembali</a>
                            <button type="submit" name="update" class="btn btn-success-custom px-4 fw-bold rounded-pill shadow-sm">Simpan Perubahan</button>
                        </div>
                    </form>
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
</body>
</html>