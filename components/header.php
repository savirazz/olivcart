<?php
session_start();
require_once "../config/db.php";

$user_id = $_SESSION['user_id'] ?? null;

// cart count
$cartCount = 0;
if ($user_id) {
    $q = $conn->query("SELECT COUNT(*) as total FROM cart WHERE user_id=$user_id");
    $cartCount = $q->fetch(PDO::FETCH_ASSOC)['total'];
}

// notif
$notifCount = 0;
if ($user_id) {
    $q = $conn->query("SELECT COUNT(*) as total FROM notifications WHERE user_id=$user_id AND is_read=0");
    $notifCount = $q->fetch(PDO::FETCH_ASSOC)['total'];
}
?>

<header style="display:flex;justify-content:space-between;padding:15px;background:#fff;border-bottom:1px solid #ddd;">
    <h2>OlivCart</h2>

    <nav>
        <a href="/public/catalog.php">Catalog</a>
        <a href="/public/cart.php">Cart (<?= $cartCount ?>)</a>
        <a href="/user/history.php">History</a>
        <a href="/user/notifications.php">Notif (<?= $notifCount ?>)</a>
        <a href="/user/profile.php">Profile</a>
    </nav>
</header>