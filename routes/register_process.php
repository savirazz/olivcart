<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../config/database.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("
        INSERT INTO users (nama, email, password, role)
        VALUES (:nama, :email, :password, 'pengguna')
    ");

    $stmt->execute([
        'nama' => $nama,
        'email' => $email,
        'password' => $password
    ]);

    header("Location: login.php?success=Registrasi berhasil");
} catch (PDOException $e) {
    header("Location: register.php?error=Email sudah dipakai");
}