<?php
session_start();
include 'koneksi.php';

// Fitur Pencarian Produk
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, $_GET['keyword']) : '';
if ($keyword != '') {
    $query = mysqli_query($koneksi, "SELECT * FROM produk WHERE nama_produk LIKE '%$keyword%' OR kategori LIKE '%$keyword%'");
} else {
    $query = mysqli_query($koneksi, "SELECT * FROM produk");
}
?>


<!-- Script JavaScript-nya -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function konfirmasiKeluarUser() {
    Swal.fire({
        title: 'Keluar dari Akun?',
        text: "Anda harus masuk kembali untuk berbelanja produk UMKM.",
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
            window.location.href = 'logout.php';
        }
    });
}
</script>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NusaKita - Marketplace Produk UMKM Indonesia</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- CSS Eksternal -->
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
                <img src="assets/img/logo.jpg" alt="NusaKita" style="height: 60px; object-fit: contain;">
            </a>

            <!-- Form pencarian dan menu navbar lainnya -->
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <form action="" method="GET" class="d-flex mx-auto col-lg-6 my-3 my-lg-0">
                    <div class="input-group shadow-sm rounded-pill overflow-hidden border border-success-subtle">
                        <input class="form-control border-0 px-4 py-2 shadow-none" type="search" name="keyword" placeholder="Cari produk, kuliner, fashion, atau kerajinan..." value="<?= htmlspecialchars($keyword); ?>">
                        <button class="btn btn-success-custom px-4" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link text-dark fw-semibold" href="keranjang.php"><i class="bi bi-cart3 me-1"></i> Keranjang</a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link text-dark fw-semibold" href="pesanan_saya.php"><i class="bi bi-clock-history me-1"></i> Riwayat</a>
                    </li>

                    <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                        <!-- Jika SUDAH LOGIN: Tampilkan nama & tombol Keluar modern -->
                        <li class="nav-item me-3">
                            <span class="text-secondary small fw-semibold">Halo, <?= htmlspecialchars($_SESSION['user']['nama_lengkap'] ?? 'User'); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm px-4 fw-bold rounded-pill shadow-sm text-white" href="javascript:void(0);" onclick="konfirmasiKeluarUser()" style="background-color: #dc3545 !important; border-color: #dc3545 !important;">Keluar</a>
                        </li>
                    <?php else: ?>
                        <!-- Jika BELUM LOGIN: Hanya tampilkan tombol Masuk -->
                        <li class="nav-item">
                            <a class="btn btn-success-custom btn-sm px-4 fw-bold rounded-pill shadow-sm" href="login.php">Masuk</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section: Teks di Kiri, Carousel Slider Gambar di Kanan -->
    <div class="container my-4">
        <div class="hero-section rounded-4 p-4 p-lg-5 shadow-sm">
            <div class="row align-items-center g-4">
                <!-- Sisi Kiri: Teks & Call to Action -->
                <div class="col-lg-5">
                    <span class="badge badge-green px-3 py-2 rounded-pill fw-bold mb-3">
                        <i class="bi bi-shop me-1"></i> Marketplace UMKM Lokal
                    </span>
                    <h1 class="fw-bold display-6 text-dark mb-3">Bangga Buatan Lokal, Temukan Karya Terbaik Anak Bangsa</h1>
                    <p class="text-secondary mb-4">Dukung kemajuan ekonomi lokal dengan berbelanja produk berkualitas tinggi langsung dari ribuan pelaku UMKM Indonesia.</p>
                    <div class="d-flex gap-3 align-items-center mb-4">
                        <a href="#list-produk" class="btn btn-success-custom px-4 py-2 fw-bold rounded-pill shadow-sm">
                            Mulai Belanja <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <!-- Trust Badges Kecil -->
                    <div class="row text-muted small g-3 border-top pt-3">
                        <div class="col-4 d-flex align-items-center gap-1">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>100% Aman</span>
                        </div>
                        <div class="col-4 d-flex align-items-center gap-1">
                            <i class="bi bi-tags text-success fs-5"></i>
                            <span>Harga Lokal</span>
                        </div>
                        <div class="col-4 d-flex align-items-center gap-1">
                            <i class="bi bi-award text-success fs-5"></i>
                            <span>Kurasi Terbaik</span>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan: Carousel Slider Gambar yang Bisa Digeser -->
                <div class="col-lg-7">
                    <div id="heroCarousel" class="carousel slide shadow rounded-4 overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="assets/img/carausel2.jpg" class="d-block w-100" alt="Banner 1" style="height: 340px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/img/carausel1.jpg" class="d-block w-100" alt="Banner 2" style="height: 340px; object-fit: cover;">
                            </div>
                            <div class="carousel-item">
                                <img src="assets/img/carausel3.jpg" class="d-block w-100" alt="Banner 3" style="height: 340px; object-fit: cover;">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon bg-dark bg-opacity-50 rounded-circle p-2"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon bg-dark bg-opacity-50 rounded-circle p-2"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Pilihan (Icon Lingkar) -->
    <div class="container my-5">
        <h5 class="fw-bold text-dark mb-4">Kategori Pilihan UMKM</h5>
        <div class="row row-cols-2 row-cols-md-4 g-4 text-center">
            <div class="col">
                <a href="index.php?keyword=Kuliner" class="category-item d-block p-3 bg-white rounded-4 shadow-sm border text-dark">
                    <img src="assets/img/kuliner.jpeg" class="rounded-circle category-circle mb-2" alt="Kuliner">
                    <h6 class="fw-semibold mb-0 text-success">Kuliner Nusantara</h6>
                </a>
            </div>
            <div class="col">
                <a href="index.php?keyword=Fashion" class="category-item d-block p-3 bg-white rounded-4 shadow-sm border text-dark">
                    <img src="assets/img/fashion.jpeg" class="rounded-circle category-circle mb-2" alt="Fashion">
                    <h6 class="fw-semibold mb-0 text-success">Fashion Lokal</h6>
                </a>
            </div>
            <div class="col">
                <a href="index.php?keyword=Kerajinan" class="category-item d-block p-3 bg-white rounded-4 shadow-sm border text-dark">
                    <img src="assets/img/kerajinan.jpeg" class="rounded-circle category-circle mb-2" alt="Kerajinan">
                    <h6 class="fw-semibold mb-0 text-success">Kerajinan Tangan</h6>
                </a>
            </div>
            <div class="col">
                <a href="pesanan_saya.php" class="category-item d-block p-3 bg-white rounded-4 shadow-sm border text-dark">
                    <img src="assets/img/lacak.jpeg" class="rounded-circle category-circle mb-2" alt="Lacak">
                    <h6 class="fw-semibold mb-0 text-success">Lacak Pesanan</h6>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content: Daftar Produk -->
    <div id="list-produk" class="container my-4 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Produk Yang Sedang Diminati</h4>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <div class="col">
                        <div class="card product-card h-100 shadow-sm rounded-4 overflow-hidden border">
                            <?php 
                                $file_foto = trim($row['foto'] ?? '');
                                $kategori = trim($row['kategori'] ?? '');

                                if (!empty($file_foto) && file_exists('uploads/' . $file_foto)) {
                                    $path_foto = 'uploads/' . $file_foto;
                                } else {
                                    $lower_kat = strtolower($kategori);
                                    if ($lower_kat == 'kuliner') {
                                        $path_foto = 'https://images.unsplash.com/photo-1599487488170-d3319c256053?auto=format&fit=crop&w=600&q=80';
                                    } elseif ($lower_kat == 'fashion') {
                                        $path_foto = 'https://images.unsplash.com/photo-1445205170230-053b83016050?auto=format&fit=crop&w=600&q=80';
                                    } else {
                                        $path_foto = 'https://images.unsplash.com/photo-1513519245088-0e12902e5a38?auto=format&fit=crop&w=600&q=80';
                                    }
                                }
                            ?>
                            <div class="product-img-wrapper">
                                <img src="<?= $path_foto; ?>" alt="<?= htmlspecialchars($row['nama_produk']); ?>">
                            </div>
                            
                            <div class="card-body d-flex flex-column p-4">
                                <div class="mb-2">
                                    <span class="badge badge-green px-2 py-1 small fw-bold rounded-pill"><?= htmlspecialchars($row['kategori']); ?></span>
                                </div>
                                <h5 class="card-title fw-bold text-dark mb-2"><?= htmlspecialchars($row['nama_produk']); ?></h5>
                                <p class="card-text text-muted small flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($row['deskripsi']); ?></p>
                                
                                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                    <span class="fw-bold text-success fs-5">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></span>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 pb-4 px-4">
                                <div class="d-flex gap-2">
                                    <a href="tambah_keranjang.php?id=<?= $row['id_produk']; ?>" 
                                    class="btn btn-outline-success fw-bold py-2 rounded-3 flex-fill d-flex align-items-center justify-content-center gap-1" 
                                    style="font-size: 0.85rem;">
                                        <i class="bi bi-cart-plus"></i> Keranjang
                                    </a>
                                    <a href="beli.php?id=<?= $row['id_produk']; ?>" 
                                    class="btn btn-success-custom fw-bold py-2 rounded-3 shadow-sm flex-fill d-flex align-items-center justify-content-center gap-1" 
                                    style="font-size: 0.85rem;">
                                        <i class="bi bi-bag-check"></i> Beli Langsung
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="p-5 bg-white rounded-4 shadow-sm border">
                        <i class="bi bi-box2 text-success display-4 mb-3"></i>
                        <p class="text-muted fs-5 mb-0">Belum ada produk yang tersedia atau sesuai dengan kata kunci pencarian kamu.</p>
                    </div>
                </div>
            <?php endif; ?>
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