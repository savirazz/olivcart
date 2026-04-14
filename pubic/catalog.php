<?php
require_once "../config/db.php";
include "../components/header.php";

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$sql = "SELECT * FROM products WHERE 1=1";

if ($search) {
    $sql .= " AND name LIKE '%$search%'";
}

if ($category) {
    $sql .= " AND category='$category'";
}

$products = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Katalog Produk</h2>

<form method="GET">
    <input type="text" name="search" placeholder="Cari produk...">
    <select name="category">
        <option value="">Semua</option>
        <option value="game">Game</option>
        <option value="movie">Movie</option>
    </select>
    <button>Cari</button>
</form>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;">
<?php foreach ($products as $p): ?>
    <div style="border:1px solid #ddd;padding:10px;">
        <h3><?= $p['name'] ?></h3>
        <p>Rp <?= $p['price'] ?></p>

        <a href="../actions/add_cart.php?id=<?= $p['id'] ?>">+ Keranjang</a>
    </div>
<?php endforeach; ?>
</div>