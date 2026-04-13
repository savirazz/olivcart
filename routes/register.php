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
<title>Daftar Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body class="auth-page">
<div class="container">
<div class="card">
<div class="header">
<div class="header-row">
<div class="logo">Olvart</div>
<svg class="header-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z" fill="var(--wine)"/>
</svg>
</div>
<h2 class="auth-title">daftar</h2>
<p>Buat akses multi-user pada Olvart</p>
</div>
<div class="form">
<?php if ($error): ?>
<div class="message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($message): ?>
<div class="message" style="background:#d8f5dd;color:#106c35;border-color:#a3dfb4;">
<?= $message ?>
</div>
<?php endif; ?>
<form method="POST" action="register.php">
<input type="text" name="name" placeholder="Nama Lengkap" value="<?= htmlspecialchars($name) ?>" required>
<input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
<input type="password" name="password" placeholder="Password" required>
<input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
<button type="submit">Daftar Sekarang</button>
</form>
<p style="text-align:center;margin-top:18px;">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
</div>
</div>
</div>

</body>
</html>
