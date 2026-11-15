<?php
include 'koneksi.php';

$pesan = '';

if (isset($_POST['register'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];
    $role     = $_POST['role'];

    // Cek apakah email sudah terdaftar
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Email sudah terdaftar, silakan gunakan email lain!";
    } else {
        // Masukkan data ke tabel users
        $query = "INSERT INTO users (nama_lengkap, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Registrasi berhasil! Silakan masuk.'); window.location='login.php';</script>";
        } else {
            $pesan = "Pendaftaran gagal, coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - UsahaKita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 rounded-3 p-4">
                    <h3 class="text-center fw-bold mb-4">Daftar Akun UsahaKita</h3>

                    <?php if (!empty($pesan)) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= $pesan; ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Nama kamu">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="nama@email.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="Buat password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Daftar Sebagai</label>
                            <select name="role" class="form-select" required>
                                <option value="pembeli">Pembeli</option>
                                <option value="penjual">Penjual (UMKM)</option>
                            </select>
                        </div>
                        <button type="submit" name="register" class="btn btn-dark w-100 py-2">Daftar Sekarang</button>
                    </form>

                    <p class="text-center mt-3 small text-muted">
                        Sudah punya akun? <a href="login.php" class="text-dark fw-bold">Masuk di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>