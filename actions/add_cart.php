<?php
session_start();
require_once "../config/db.php";

$user_id = $_SESSION['user_id'];
$product_id = $_GET['id'];

// cek apakah sudah ada
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
$stmt->execute([$user_id, $product_id]);

if ($stmt->rowCount() > 0) {
    $conn->prepare("UPDATE cart SET qty = qty + 1 WHERE user_id=? AND product_id=?")
         ->execute([$user_id, $product_id]);
} else {
    $conn->prepare("INSERT INTO cart (user_id, product_id, qty) VALUES (?, ?, 1)")
         ->execute([$user_id, $product_id]);
}

header("Location: ../public/cart.php");