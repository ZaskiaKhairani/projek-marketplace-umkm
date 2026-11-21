<?php
session_start();
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "<script>alert('Silakan masuk terlebih dahulu untuk membeli produk.'); window.location='login.php';</script>";
    exit;
}

// Ambil ID produk dari URL
$id_produk = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query_produk = mysqli_query($koneksi, "SELECT * FROM produk WHERE id_produk = '$id_produk'");
$produk = mysqli_fetch_assoc($query_produk);

if (!$produk) {
    echo "<script>alert('Produk tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

// Ambil nama file foto produk (cek kolom gambar atau foto)
$file_gambar = !empty($produk['gambar']) ? $produk['gambar'] : ($produk['foto'] ?? '');
$path_gambar = (!empty($file_gambar) && file_exists("uploads/" . $file_gambar)) ? "uploads/" . $file_gambar : "assets/img/logo.jpeg";

$pesanan_berhasil = false;
$pesan_error = "";

// Proses saat tombol "Proses Pesanan Sekarang" diklik
if (isset($_POST['proses_beli'])) {
    $id_pembeli = $_SESSION['user']['id_user'] ?? $_SESSION['id_user'] ?? 0;
    $jumlah = (int)$_POST['jumlah'];
    $harga_satuan = (int)$produk['harga'];
    $stok_sekarang = (int)$produk['stok'];

    if ($jumlah < 1) {
        $pesan_error = "Jumlah pembelian minimal 1 produk.";
    } elseif ($jumlah > $stok_sekarang) {
        $pesan_error = "Maaf, jumlah pembelian melebihi stok yang tersedia ($stok_sekarang).";
    } else {
        $total_harga = $harga_satuan * $jumlah;
        $tanggal = date('Y-m-d H:i:s');
        $status = 'Menunggu Konfirmasi';

        // 1. Simpan ke tabel pesanan
        $insert_pesanan = mysqli_query($koneksi, "INSERT INTO pesanan (id_pembeli, tanggal_pesanan, total_harga, status_pesanan) VALUES ('$id_pembeli', '$tanggal', '$total_harga', '$status')");

        if ($insert_pesanan) {
            $id_pesanan_baru = mysqli_insert_id($koneksi);

            // 2. Simpan ke tabel detail_pesanan
            mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, jumlah_beli, subtotal) VALUES ('$id_pesanan_baru', '$id_produk', '$jumlah', '$jumlah', '$total_harga')");

            // 3. Kurangi stok produk
            $sisa_stok = $stok_sekarang - $jumlah;
            mysqli_query($koneksi, "UPDATE produk SET stok = '$sisa_stok' WHERE id_produk = '$id_produk'");

            $pesanan_berhasil = true;
        } else {
            $pesan_error = "Gagal memproses pesanan: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembelian - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <!-- Navbar Minimalis -->
    <nav class="navbar navbar-light bg-white shadow-sm py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                <img src="assets/img/logo.jpeg" alt="NusaKita" style="height: 50px; object-fit: contain;">
            </a>
            <a href="index.php" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali Belanja
            </a>
        </div>
    </nav>

    <!-- Konten Konfirmasi Pembelian -->
    <div class="container my-5 flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                <div class="card-header bg-white border-bottom py-3 px-4">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-bag-check-fill text-success me-2"></i>Konfirmasi Pembelian
                    </h5>
                </div>

                <div class="card-body p-4">
                    <?php if (!empty($pesan_error)): ?>
                        <div class="alert alert-danger py-2 small rounded-3 text-center mb-3">
                            <?= htmlspecialchars($pesan_error); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Detail Produk -->
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light border border-success-subtle mb-4">
                        <img src="<?= htmlspecialchars($path_gambar); ?>" alt="<?= htmlspecialchars($produk['nama_produk']); ?>" class="rounded-3 bg-white border" style="width: 90px; height: 90px; object-fit: cover;">
                        <div class="flex-grow-1">
                            <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1 small mb-1">
                                <?= htmlspecialchars($produk['kategori']); ?>
                            </span>
                            <h5 class="fw-bold text-dark mb-1 text-capitalize"><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                            <div class="fw-bold text-success fs-5">
                                Rp<?= number_format($produk['harga'], 0, ',', '.'); ?>
                            </div>
                            <small class="text-muted">Stok Tersedia: <strong><?= (int)$produk['stok']; ?></strong></small>
                        </div>
                    </div>

                    <!-- Form Jumlah & Checkout -->
                    <form action="" method="POST" id="formBeli">
                        <div class="mb-4">
                            <label for="jumlah" class="form-label fw-semibold small text-secondary">Jumlah Pembelian</label>
                            <div class="input-group" style="max-width: 180px;">
                                <button type="button" class="btn btn-outline-success fw-bold" id="btnMinus">-</button>
                                <input type="number" class="form-control text-center fw-bold border-success-subtle shadow-none" id="jumlah" name="jumlah" value="1" min="1" max="<?= (int)$produk['stok']; ?>" required>
                                <button type="button" class="btn btn-outline-success fw-bold" id="btnPlus">+</button>
                            </div>
                        </div>

                        <!-- Ringkasan Total Harga -->
                        <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-success-subtle mb-4">
                            <span class="fw-semibold text-dark">Total Pembayaran</span>
                            <span class="fw-bold text-success fs-4" id="totalHarga">
                                Rp<?= number_format($produk['harga'], 0, ',', '.'); ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between gap-2">
                            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 fw-semibold">
                                Batal
                            </a>
                            <button type="submit" name="proses_beli" class="btn btn-success-custom rounded-pill px-4 fw-bold shadow-sm flex-grow-1" <?= ($produk['stok'] < 1) ? 'disabled' : ''; ?>>
                                <i class="bi bi-check-circle me-1"></i> Proses Pesanan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 & SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const hargaSatuan = <?= (int)$produk['harga']; ?>;
        const maxStok = <?= (int)$produk['stok']; ?>;
        const inputJumlah = document.getElementById('jumlah');
        const totalHargaEl = document.getElementById('totalHarga');
        const btnMinus = document.getElementById('btnMinus');
        const btnPlus = document.getElementById('btnPlus');

        function updateSubtotal() {
            let qty = parseInt(inputJumlah.value) || 1;
            if (qty < 1) qty = 1;
            if (qty > maxStok) qty = maxStok;
            inputJumlah.value = qty;

            const total = hargaSatuan * qty;
            totalHargaEl.textContent = 'Rp' + total.toLocaleString('id-ID');
        }

        btnMinus.addEventListener('click', () => {
            if (parseInt(inputJumlah.value) > 1) {
                inputJumlah.value = parseInt(inputJumlah.value) - 1;
                updateSubtotal();
            }
        });

        btnPlus.addEventListener('click', () => {
            if (parseInt(inputJumlah.value) < maxStok) {
                inputJumlah.value = parseInt(inputJumlah.value) + 1;
                updateSubtotal();
            }
        });

        inputJumlah.addEventListener('input', updateSubtotal);

        <?php if ($pesanan_berhasil): ?>
        Swal.fire({
            title: 'Pesanan Berhasil Dibuat!',
            text: 'Terima kasih telah mendukung produk UMKM lokal di NusaKita.',
            icon: 'success',
            confirmButtonColor: '#198754',
            confirmButtonText: 'Lihat Pesanan Saya',
            customClass: {
                popup: 'rounded-4 shadow-sm'
            }
        }).then(() => {
            window.location.href = 'pesanan_saya.php';
        });
        <?php endif; ?>
    </script>
</body>
</html>