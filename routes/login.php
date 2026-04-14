<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
require_once __DIR__ . '/auth.php';
if (currentUser()) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Olvart</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- CSS Olvart -->
    <link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>

<body class="auth-page">
    <div class="auth-wrapper">
        
        <!-- Bagian Kiri -->
        <div class="auth-left">
            <h1>Selamat Datang Kembali</h1>
            <p>Masuk untuk melanjutkan penyewaan alat lukis favorit Anda di Olvart.</p>
            <img src="../assets/img/paint.png" alt="Ilustrasi Alat Lukis">
        </div>

        <!-- Bagian Kanan -->
        <div class="auth-right">
            <div class="auth-card">
                <h2>Masuk</h2>
                <p class="subtitle">Silakan login ke akun Anda</p>

                <form method="POST" action="login_process.php">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Masuk
                    </button>

                    <p class="text-center mt-3">
                        Belum punya akun?
                        <a href="register.php">Daftar di sini</a>
                    </p>
                </form>
            </div>
        </div>

    </div>
</body>
</html>