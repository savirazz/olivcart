<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/category_helper.php';
require_once __DIR__ . '/rental_helper.php';
require_once __DIR__ . '/cart_helper.php';
requireAuth();
$user = currentUser();

$search = trim($_GET['search'] ?? '');
$priceMin = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
$priceMax = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 999999;
$inStock = isset($_GET['in_stock']) ? $_GET['in_stock'] === '1' : false;
$error = isset($_GET['error']) ? trim($_GET['error']) : '';

$allTools = getTools();
$tools = [];

foreach ($allTools as $tool) {
    $match = true;

    if (!empty($search)) {
        $searchLower = strtolower($search);
        $nameLower = strtolower($tool['name']);
        $descLower = strtolower($tool['description']);
        if (strpos($nameLower, $searchLower) === false && strpos($descLower, $searchLower) === false) {
            $match = false;
        }
    }

    if ($match && $tool['price'] < $priceMin) {
        $match = false;
    }

    if ($match && $tool['price'] > $priceMax) {
        $match = false;
    }

    if ($match && $inStock && intval($tool['stock']) <= 0) {
        $match = false;
    }

    if ($match) {
        $tools[] = $tool;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Katalog Alat - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<div class="navbar">
<div class="logo">Olvart</div>
<div class="right-nav">
<span>Halo, <?= htmlspecialchars($user['name']) ?></span>
<a href="keranjang.php">Keranjang (<?= getCartItemCount() ?>)</a>
<a href="ajukan_peminjaman.php">Riwayat</a>
<a href="dashboard.php">Dashboard</a>
<a href="logout.php">Logout</a>
</div>
</div>
<div class="container">
<h1 class="title">Katalog Alat Lukis</h1>
<?php if ($error): ?>
<div class="error-msg"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<div class="search-filter">
<form method="GET" action="list_alat.php">
<div class="filter-grid">
<input type="text" name="search" placeholder="Cari alat..." value="<?= htmlspecialchars($search) ?>">
<input type="number" name="price_min" placeholder="Harga min" min="0" step="1000" value="<?= $priceMin > 0 ? $priceMin : '' ?>">
<input type="number" name="price_max" placeholder="Harga max" min="0" step="1000" value="<?= $priceMax < 999999 ? $priceMax : '' ?>">
<label style="display:flex;align-items:center;gap:8px;margin:0;">
<input type="checkbox" name="in_stock" value="1" <?= $inStock ? 'checked' : '' ?>>
Stok tersedia
</label>
<div>
<button type="submit" class="btn-search">Cari</button>
<a href="list_alat.php" class="btn-reset">Reset</a>
</div>
</div>
</form>
</div>
<div class="results-info">
Menampilkan <?= count($tools) ?> dari <?= count($allTools) ?> alat
</div>
<?php if (count($tools) > 0): ?>
<div class="tools-grid">
<?php foreach ($tools as $tool): ?>
<div class="tool-card">
<div class="tool-image">
<img src="<?= htmlspecialchars(getToolImage($tool['image'] ?? '')) ?>" alt="<?= htmlspecialchars($tool['name']) ?>">
</div>
<div class="tool-body">
<div class="tool-name"><?= htmlspecialchars($tool['name']) ?></div>
<div class="tool-desc"><?= htmlspecialchars(substr($tool['description'], 0, 60)) ?><?= strlen($tool['description']) > 60 ? '...' : '' ?></div>
<div class="tool-stock <?= intval($tool['stock']) > 0 ? 'stock-in' : 'stock-out' ?>">
<?= intval($tool['stock']) > 0 ? 'Stok: ' . intval($tool['stock']) : 'Habis' ?>
</div>
<div class="tool-price">Rp <?= number_format($tool['price'], 0, ',', '.') ?></div>
<div class="tool-action">
<?php if (intval($tool['stock']) > 0): ?>
<form method="POST" action="add_to_cart.php" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
<input type="hidden" name="tool_id" value="<?= intval($tool['id']) ?>">
<input type="date" name="start_date" required>
<input type="date" name="end_date" required>
<input type="number" name="quantity" value="1" min="1" max="<?= intval($tool['stock']) ?>" style="grid-column:1/-1;" required>
<button type="submit" style="grid-column:1/-1;background:#7a0000;color:white;border:none;padding:8px;border-radius:6px;cursor:pointer;font-weight:700;">+ Keranjang</button>
</form>
<?php else: ?>
<div style="background:#ddd;color:#999;padding:8px;border-radius:6px;text-align:center;">Habis</div>
<?php endif; ?>
<div class="btn-detail">Detail</div>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php else: ?>
<div class="empty">
<p>Tidak ada alat yang sesuai dengan pencarian Anda.</p>
<a href="list_alat.php" style="color:#7a0000;text-decoration:underline;">Lihat semua alat</a>
</div>
<?php endif; ?>
</div>
</body>
</html>
