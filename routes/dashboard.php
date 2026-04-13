<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/category_helper.php';
require_once __DIR__ . '/tool_helper.php';
requireAuth();
$user = currentUser();

$totalUsers = count(getUsers());
$totalCategories = count(getCategories());
$totalTools = count(getTools());
$totalToolsStock = 0;
foreach (getTools() as $tool) {
    $totalToolsStock += intval($tool['stock']);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body class="dashboard-page">
<?php include __DIR__ . '/header.php'; ?>
<div class="hero">
<h1>Wujudkan Karya Senimu Bersama Olvart</h1>
<p>Peminjaman alat lukis premium dengan paket lengkap.</p>
<div class="user-card">
<strong>Pengguna:</strong> <?= htmlspecialchars($user['name']) ?><br>
<strong>Email:</strong> <?= htmlspecialchars($user['email']) ?><br>
<strong>Peran:</strong> <?= htmlspecialchars($user['role']) ?>
</div>
</div>
<div class="container">
<div class="section">
<?php if ($user['role'] === 'admin'): ?>
<h2 class="title">Statistik Sistem</h2>
<div class="stats-grid">
<div class="stat-card">
<h3>Total User</h3>
<div class="number"><?= $totalUsers ?></div>
<p>Pengguna terdaftar</p>
</div>
<div class="stat-card">
<h3>Total Kategori</h3>
<div class="number"><?= $totalCategories ?></div>
<p>Kategori produk</p>
</div>
<div class="stat-card">
<h3>Total Alat</h3>
<div class="number"><?= $totalTools ?></div>
<p>Item dalam sistem</p>
</div>
<div class="stat-card">
<h3>Total Stok</h3>
<div class="number"><?= $totalToolsStock ?></div>
<p>Unit tersedia</p>
</div>
</div>
<?php endif; ?>
<h2 class="title">Pilih Paket Lukismu</h2>
<a href="list_alat.php" class="btn btn-primary">Lihat Katalog</a>
<div class="grid">
<?php
$paket = ["J","A","E","Y","U","K"];
foreach ($paket as $p) {
    echo "<div class='card'><img src='https://picsum.photos/400/300?random=$p'><div class='card-body'><h3>Paket $p</h3><ul><li>Set Kuas Profesional</li><li>Palet Lukis</li><li>Kanvas Premium</li><li>Set Cat (Gratis)</li></ul><div class='btn'>Pinjam Sekarang</div></div></div>";
}
?>
</div>
<h2 class="title">Status Peminjaman</h2>
<div class="table-box">
<table>
<tr><th>Paket</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr>
<tr><td>Paket E</td><td>2026-02-10</td><td><span class="status pinjam">Dipinjam</span></td><td style="color:red;">Kembalikan</td></tr>
<tr><td>Paket J</td><td>2026-01-25</td><td><span class="status kembali">Dikembalikan</span></td><td>-</td></tr>
</table>
</div>
</div>
</div>
<footer>
© 2026 Olvart - Painting Tool Rental System
</footer>
</body>
</html>
