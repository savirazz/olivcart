<?php
require_once __DIR__ . '/auth.php';
redirectIfAuthenticated();
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email dan password harus diisi.';
    } else {
        $result = loginUser($email, $password);
        if ($result['success']) {
            header('Location: dashboard.php');
            exit;
        }
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login Olvart</title>
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
<h2 class="auth-title">masuk</h2>
<p class="subtitle-small">Autentikasi multi-user untuk akses dashboard</p>
</div>
<div class="form">
<?php if ($error): ?>
<div class="message"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="POST" action="login.php">
<input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email) ?>" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Masuk Sekarang</button>
</form>
<p style="text-align:center;margin-top:18px;">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</div>
</div>
</div>

</body>
</html>
