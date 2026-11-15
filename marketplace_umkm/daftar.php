<?php
session_start();
include 'koneksi.php';

$pesan_error = "";

if (isset($_POST['daftar'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password_raw = trim($_POST['password']);
    // Ambil pilihan role dari form, default ke 'pembeli' jika kosong
    $role = mysqli_real_escape_string($koneksi, $_POST['role'] ?? 'pembeli');
    
    // Cek apakah email sudah terdaftar
    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $pesan_error = "Email sudah terdaftar! Gunakan email lain.";
    } else {
        // Enskripsi password agar aman
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        
        // Simpan ke database dengan role yang dipilih
        $query = mysqli_query($koneksi, "INSERT INTO users (nama_lengkap, email, password, role) VALUES ('$nama_lengkap', '$email', '$password', '$role')");
        
        if ($query) {
            echo "<script>alert('Pendaftaran berhasil! Silakan masuk.'); window.location='login.php';</script>";
            exit;
        } else {
            $pesan_error = "Gagal mendaftar ke database: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- CSS Eksternal Terpisah -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100 py-4">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="text-center mb-4">
                        <a href="index.php" class="d-inline-block mb-2">
                            <img src="assets/img/logo.jpg" alt="NusaKita" style="height: 65px; object-fit: contain;">
                        </a>
                        <h4 class="fw-bold text-dark">Daftar Akun</h4>
                        <p class="text-muted small">Bergabunglah bersama ribuan UMKM & Pembeli lokal</p>
                    </div>

                    <?php if (!empty($pesan_error)): ?>
                        <div class="alert alert-danger py-2 small text-center rounded-3"><?= $pesan_error; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label fw-semibold small text-secondary">Nama Lengkap</label>
                            <input type="text" class="form-control rounded-3 py-2 shadow-none border-success-subtle" id="nama_lengkap" name="nama_lengkap" placeholder="Nama lengkap kamu" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-secondary">Email</label>
                            <input type="email" class="form-control rounded-3 py-2 shadow-none border-success-subtle" id="email" name="email" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                            <input type="password" class="form-control rounded-3 py-2 shadow-none border-success-subtle" id="password" name="password" placeholder="Buat password aman" required>
                        </div>
                        
                        <!-- Pilihan Role Menggunakan Tombol Interaktif (Button Check) -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary d-block mb-2">Daftar Sebagai</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="role_pembeli" value="pembeli" checked autocomplete="off">
                                    <label class="btn btn-outline-success w-100 py-2 rounded-3 fw-semibold small d-flex align-items-center justify-content-center gap-1" for="role_pembeli">
                                        <i class="bi bi-bag-heart"></i> Pembeli
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="role_penjual" value="penjual" autocomplete="off">
                                    <label class="btn btn-outline-success w-100 py-2 rounded-3 fw-semibold small d-flex align-items-center justify-content-center gap-1" for="role_penjual">
                                        <i class="bi bi-shop"></i> Penjual
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="daftar" class="btn btn-success-custom w-100 fw-bold py-2 rounded-3 shadow-sm mb-3">Daftar Sekarang</button>
                        
                        <div class="text-center">
                            <p class="small text-muted mb-0">Sudah punya akun? <a href="login.php" class="text-success text-decoration-none fw-semibold">Masuk di sini</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>