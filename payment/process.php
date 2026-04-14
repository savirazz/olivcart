<?php
session_start();
require_once "../config/db.php";

$user_id = $_SESSION['user_id'];
$total = $_POST['total'];

// simpan transaksi
$stmt = $conn->prepare("
INSERT INTO payments (user_id, total, status)
VALUES (?, ?, 'paid')
");
$stmt->execute([$user_id, $total]);

// kosongkan cart
$conn->prepare("DELETE FROM cart WHERE user_id=?")
     ->execute([$user_id]);

// notifikasi
$conn->prepare("
INSERT INTO notifications (user_id, message)
VALUES (?, 'Pembayaran berhasil!')
")->execute([$user_id]);

header("Location: ../user/history.php");
exit;