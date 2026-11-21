<?php
session_start();
include 'koneksi.php';

// Cek login user
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Ambil ID pembeli dari berbagai kemungkinan key session
$id_pembeli = $_SESSION['user']['id_user'] 
    ?? $_SESSION['user']['id'] 
    ?? $_SESSION['id_user'] 
    ?? $_SESSION['id'] 
    ?? 0;

// Ambil data keranjang dari database digabung dengan tabel produk
$query_keranjang = mysqli_query($koneksi, "
    SELECT keranjang.id_keranjang, keranjang.jumlah, produk.id_produk, produk.nama_produk, produk.kategori, produk.harga, produk.stok, produk.gambar, produk.foto
    FROM keranjang
    JOIN produk ON keranjang.id_produk = produk.id_produk
    WHERE keranjang.id_pembeli = '$id_pembeli'
    ORDER BY keranjang.id_keranjang DESC
");

$items = [];
if ($query_keranjang) {
    while ($row = mysqli_fetch_assoc($query_keranjang)) {
        $row['subtotal'] = $row['harga'] * $row['jumlah'];
        $items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Eksternal Terpisah -->
    <link rel="stylesheet" href="style.css">
    <style>
        .form-check-input:checked {
            background-color: #198754;
            border-color: #198754;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

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
                        <a class="nav-link fw-semibold text-dark" href="pesanan_saya.php"><i class="bi bi-clock-history me-1 text-success"></i> Riwayat Belanja</a>
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

    <!-- Main Content: Keranjang Belanja -->
    <div class="container my-5 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-cart3 text-success me-2"></i> Keranjang Belanja Anda</h3>
            <a href="index.php" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk Lain
            </a>
        </div>

        <form action="checkout_keranjang.php" method="POST" id="formCheckoutKeranjang">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table cart-table mb-0 align-middle">
                            <thead class="table-success">
                                <tr>
                                    <th class="py-3 ps-4" style="width: 50px;">
                                        <?php if (!empty($items)): ?>
                                            <input type="checkbox" class="form-check-input border-secondary shadow-none" id="checkAllTop" style="width: 1.2rem; height: 1.2rem; cursor: pointer;" title="Pilih Semua">
                                        <?php endif; ?>
                                    </th>
                                    <th class="py-3">No</th>
                                    <th class="py-3">Produk</th>
                                    <th class="py-3">Harga Satuan</th>
                                    <th class="py-3 text-center">Jumlah</th>
                                    <th class="py-3">Subtotal</th>
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-cart-x display-4 text-secondary mb-3 d-block"></i>
                                            Keranjang belanja kamu masih kosong. Yuk pilih produk di katalog!
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($items as $item): ?>
                                        <?php
                                            $file_foto = !empty($item['gambar']) ? $item['gambar'] : ($item['foto'] ?? '');
                                            $ada_foto  = (!empty($file_foto) && file_exists("uploads/" . $file_foto));
                                        ?>
                                        <tr>
                                            <td class="ps-4">
                                                <input type="checkbox" 
                                                       class="form-check-input item-checkbox border-secondary shadow-none" 
                                                       name="pilih_keranjang[]" 
                                                       value="<?= (int)$item['id_keranjang']; ?>" 
                                                       data-subtotal="<?= (int)$item['subtotal']; ?>"
                                                       style="width: 1.2rem; height: 1.2rem; cursor: pointer;">
                                            </td>
                                            <td class="fw-semibold"><?= $no++; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <?php if ($ada_foto): ?>
                                                        <img src="<?= htmlspecialchars("uploads/" . $file_foto); ?>" alt="<?= htmlspecialchars($item['nama_produk']); ?>" class="rounded-3 border bg-light flex-shrink-0" style="width: 60px; height: 60px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <!-- Kotak kosong polos jika belum ada foto produk -->
                                                        <div class="rounded-3 border bg-light flex-shrink-0" style="width: 60px; height: 60px;"></div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold text-dark text-capitalize"><?= htmlspecialchars($item['nama_produk']); ?></div>
                                                        <span class="badge bg-success-subtle text-success rounded-pill small"><?= htmlspecialchars($item['kategori']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="fw-semibold text-secondary">Rp<?= number_format($item['harga'], 0, ',', '.'); ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border px-3 py-2 fs-6"><?= (int)$item['jumlah']; ?></span>
                                            </td>
                                            <td class="fw-bold text-success">Rp<?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                            <td class="text-center">
                                                <button type="button" onclick="hapusItem(<?= (int)$item['id_keranjang']; ?>)" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (!empty($items)): ?>
                        <div class="p-4 bg-light border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                            <div class="d-flex align-items-center gap-4">
                                <div class="form-check d-flex align-items-center gap-2 mb-0">
                                    <input class="form-check-input border-secondary shadow-none mt-0" type="checkbox" id="checkAllBottom" style="width: 1.2rem; height: 1.2rem; cursor: pointer;">
                                    <label class="form-check-label fw-semibold text-dark" for="checkAllBottom" style="cursor: pointer;">
                                        Pilih Semua (<?= count($items); ?>)
                                    </label>
                                </div>
                                <div class="border-start ps-4">
                                    <span class="text-muted small d-block">Total Pembayaran (<span id="jumlahJenis">0</span> produk dipilih)</span>
                                    <span class="fw-bold text-success fs-3" id="totalBayar">Rp0</span>
                                </div>
                            </div>
                            <button type="submit" name="checkout" class="btn btn-success-custom fw-bold px-5 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-bag-check-fill me-1"></i> Checkout (<span id="jumlahTombol">0</span>)
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </form>
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

    <!-- Bootstrap 5 & SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const checkAllTop = document.getElementById('checkAllTop');
        const checkAllBottom = document.getElementById('checkAllBottom');
        const totalBayarEl = document.getElementById('totalBayar');
        const jumlahJenisEl = document.getElementById('jumlahJenis');
        const jumlahTombolEl = document.getElementById('jumlahTombol');
        const formCheckout = document.getElementById('formCheckoutKeranjang');

        function hitungTotal() {
            let total = 0;
            let terpilih = 0;

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    total += parseInt(cb.dataset.subtotal) || 0;
                    terpilih++;
                }
            });

            if (totalBayarEl) totalBayarEl.textContent = 'Rp' + total.toLocaleString('id-ID');
            if (jumlahJenisEl) jumlahJenisEl.textContent = terpilih;
            if (jumlahTombolEl) jumlahTombolEl.textContent = terpilih;

            const semuaTercentang = checkboxes.length > 0 && terpilih === checkboxes.length;
            if (checkAllTop) checkAllTop.checked = semuaTercentang;
            if (checkAllBottom) checkAllBottom.checked = semuaTercentang;
        }

        function setSemua(status) {
            checkboxes.forEach(cb => cb.checked = status);
            hitungTotal();
        }

        if (checkAllTop) {
            checkAllTop.addEventListener('change', (e) => setSemua(e.target.checked));
        }
        if (checkAllBottom) {
            checkAllBottom.addEventListener('change', (e) => setSemua(e.target.checked));
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', hitungTotal);
        });

        if (formCheckout) {
            formCheckout.addEventListener('submit', function(e) {
                const terpilih = document.querySelectorAll('.item-checkbox:checked').length;
                if (terpilih === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Pilih Produk Dulu',
                        text: 'Centang minimal satu produk yang ingin kamu checkout.',
                        icon: 'warning',
                        confirmButtonColor: '#198754'
                    });
                }
            });
        }

        function hapusItem(idKeranjang) {
            Swal.fire({
                title: 'Hapus dari Keranjang?',
                text: 'Produk ini akan dikeluarkan dari keranjang belanja kamu.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_keranjang.php?id=' + idKeranjang;
                }
            });
        }
    </script>
</body>
</html>