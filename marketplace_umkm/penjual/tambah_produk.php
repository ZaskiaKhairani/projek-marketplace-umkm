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

if (isset($_POST['simpan'])) {
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $stok = mysqli_real_escape_string($koneksi, $_POST['stok'] ?? 0);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);

    // Proses upload gambar produk ke folder uploads/ di direktori utama
    $nama_file = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];
    
    if ($nama_file != "") {
        $ekstensi_boleh = array('png','jpg','jpeg','webp');
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));
        
        if (in_array($ekstensi, $ekstensi_boleh) === true) {
            $nama_file_baru = uniqid() . '-' . $nama_file;
            
            // Pastikan folder uploads di luar folder penjual tersedia
            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            
            move_uploaded_file($tmp_file, '../uploads/' . $nama_file_baru);
            
            // Simpan ke database
            $query = mysqli_query($koneksi, "INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi, foto) VALUES ('$nama_produk', '$kategori', '$harga', '$stok', '$deskripsi', '$nama_file_baru')");
            
            if ($query) {
                // Diarahkan ke dashboard_toko.php yang benar
                echo "<script>alert('Produk berhasil ditambahkan!'); window.location='dashboard_toko.php';</script>";
                exit;
            } else {
                $error = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
            }
        } else {
            $error = "Ekstensi file foto tidak diizinkan! Gunakan PNG, JPG, JPEG, atau WEBP.";
        }
    } else {
        $error = "Foto produk wajib di-upload!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - NusaKita</title>
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

    <!-- Konten Form Tambah Produk -->
    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h4 class="fw-bold text-dark mb-4"><i class="bi bi-plus-circle text-success me-2"></i> Tambah Produk UMKM Baru</h4>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger py-2 small rounded-3"><?= $error; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Nama Produk</label>
                            <input type="text" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="nama_produk" placeholder="Contoh: Keripik Pisang Bali" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Kategori</label>
                            <select class="form-select rounded-3 py-2 shadow-none border-success-subtle" name="kategori" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Kuliner">Kuliner Nusantara</option>
                                <option value="Fashion">Fashion Lokal</option>
                                <option value="Kerajinan">Kerajinan Tangan</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Harga (Rp)</label>
                                <input type="number" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="harga" placeholder="Contoh: 25000" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold small text-secondary">Stok</label>
                                <input type="number" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="stok" placeholder="Contoh: 50" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">Deskripsi Produk</label>
                            <textarea class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="deskripsi" rows="4" placeholder="Jelaskan kualitas dan keunggulan produkmu..." required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary">Foto Produk</label>
                            <input type="file" class="form-control rounded-3 py-2 shadow-none border-success-subtle" name="foto" required>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="dashboard_toko.php" class="btn btn-outline-success px-4 fw-bold rounded-pill">Kembali</a>
                            <button type="submit" name="simpan" class="btn btn-success-custom px-4 fw-bold rounded-pill shadow-sm">Simpan Produk</button>
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