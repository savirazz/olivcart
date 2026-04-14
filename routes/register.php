<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
require_once __DIR__ . '/auth.php';
redirectIfAuthenticated();
$error = '';
$message = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirm === '') {
        $error = 'Semua field harus diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        $result = registerUser($name, $email, $password);
        if ($result['success']) {
            $message = 'Registrasi berhasil. Silakan <a href="login.php">masuk</a>.';
            $name = '';
            $email = '';
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Olvart</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- CSS Utama -->
    <link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <!-- Bagian Kiri -->
        <div class="auth-left">
            <h1>Selamat Datang di Olvart</h1>
            <p>Sewa alat lukis berkualitas untuk mewujudkan kreativitas tanpa batas.</p>
            <img src="../assets/img/paint.png" alt="Ilustrasi Alat Lukis">
        </div>

        <!-- Bagian Kanan (Form) -->
        <div class="auth-right">
            <div class="auth-card">
                <h2>Daftar</h2>
                <p class="subtitle">Buat akses multi-user pada Olvart</p>

                <form method="POST" action="register_process.php">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                    </div>

                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>

                    <div class="mb-3">
                        <label>Konfirmasi Password</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Konfirmasi password" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        Daftar Sekarang
                    </button>

                    <p class="text-center mt-3">
                        Sudah punya akun?
                        <a href="login.php">Masuk di sini</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
