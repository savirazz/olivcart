<?php
session_start();
require 'db.php';

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user'] = $user;

    switch ($user['role']) {
        case 'admin':
            header("Location: dashboard.php");
            break;
        case 'petugas':
            header("Location: dashboard.php");
            break;
        default:
            header("Location: katalog.php");
    }

} else {
    echo "<script>alert('Login gagal');window.location='login.php';</script>";
}
?>