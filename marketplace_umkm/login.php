<?php
session_start();
include 'koneksi.php';

$error_msg = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password = trim($_POST['password']);
    
    // Ambil data user berdasarkan email
    $result = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    
    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Verifikasi password (mendukung plain text maupun password_verify)
        if (($password == $row['password']) || password_verify($password, $row['password'])) {
            
            // Hapus session lama agar data bersih total
            session_unset();
            session_destroy();
            session_start();
            
            // Set session baru
            $_SESSION['login'] = true;
            $_SESSION['user'] = $row;
            
            // Bersihkan teks role dari spasi atau huruf kapital
            $role = trim(strtolower($row['role'] ?? 'pembeli'));
            
            // Redirect sesuai role
            if ($role == 'super admin' || $role == 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } elseif ($role == 'penjual') {
                header("Location: penjual/dashboard_toko.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }
        } else {
            $error_msg = "Password yang kamu masukkan salah!";
        }
    } else {
        $error_msg = "Email tidak terdaftar di database!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - NusaKita</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- CSS Eksternal Terpisah -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <div class="text-center mb-4">
                        <a href="index.php" class="d-inline-block mb-2">
                            <img src="assets/img/logo.jpg" alt="NusaKita" style="height: 65px; object-fit: contain;">
                        </a>
                        <h4 class="fw-bold text-dark">Masuk Ke Akun</h4>
                        <p class="text-muted small">Silakan masuk sesuai akun Anda</p>
                    </div>

                    <?php if (!empty($error_msg)): ?>
                        <div class="alert alert-danger py-2 small text-center rounded-3"><?= $error_msg; ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-secondary">Email</label>
                            <input type="email" class="form-control rounded-3 py-2 shadow-none border-success-subtle" id="email" name="email" placeholder="nama@email.com" required>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold small text-secondary">Password</label>
                            <input type="password" class="form-control rounded-3 py-2 shadow-none border-success-subtle" id="password" name="password" placeholder="Masukan password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-success-custom w-100 fw-bold py-2 rounded-3 shadow-sm mb-3">Masuk</button>
                        
                        <div class="text-center">
                            <p class="small text-muted mb-0">Belum punya akun? <a href="daftar.php" class="text-success text-decoration-none fw-semibold">Daftar di sini</a></p>
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