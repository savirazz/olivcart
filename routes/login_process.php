<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../config/database.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: login.php?error=Email dan password wajib diisi");
    exit;
}

// Ambil data pengguna
$stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifikasi password
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: login.php?error=Email atau password salah");
    exit;
}
?>