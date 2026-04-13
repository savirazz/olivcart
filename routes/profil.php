<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/rental_helper.php';
requireAuth();
$user = currentUser();

$rentals = getRentalsByUser($user['id']);
$recentRentals = array_slice($rentals, 0, 5); // Ambil 5 terbaru

// Untuk admin, ambil summary
if ($user['role'] === 'admin') {
    $totalUsers = count(getUsers());
    $totalCategories = count(getCategories());
    $totalTools = count(getTools());
    $totalRentals = count(getAllRentals());
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Profil - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body class="dashboard-page">
<?php include __DIR__ . '/header.php'; ?>
<div class="navbar">
<div class="logo">Olvart</div>
<div class="right-nav">
<span>Halo, <?= htmlspecialchars($user['name']) ?> (<?= htmlspecialchars($user['role']) ?>)</span>
<a href="dashboard.php" class="logout">Dashboard</a>
<a href="logout.php" class="logout">Keluar</a>
</div>
</div>
<div class="container">
<div class="section">
<h2 class="title">Profil Pengguna</h2>
<div class="profile-card">
<div class="profile-info">
<h3>Informasi Akun</h3>
<p><strong>Nama:</strong> <?= htmlspecialchars($user['name']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
<p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
<p><strong>Bergabung:</strong> <?= htmlspecialchars($user['created_at']) ?></p>
</div>
<div class="profile-avatar">
<div class="avatar-placeholder">
<?= strtoupper(substr($user['name'], 0, 1)) ?>
</div>
</div>
</div>

<h2 class="title">Aktivitas Terbaru</h2>
<?php if ($user['role'] === 'admin'): ?>
<div class="activity-grid">
<div class="activity-card">
<h4>Manajemen Sistem</h4>
<ul>
<li>Total Pengguna: <?= $totalUsers ?></li>
<li>Total Kategori: <?= $totalCategories ?></li>
<li>Total Alat: <?= $totalTools ?></li>
<li>Total Peminjaman: <?= $totalRentals ?></li>
</ul>
</div>
<div class="activity-card">
<h4>Aktivitas Terakhir</h4>
<p>Sebagai admin, Anda dapat mengelola pengguna, kategori, dan alat.</p>
<p>Lihat dashboard untuk detail lebih lanjut.</p>
</div>
</div>
<?php else: ?>
<?php if (empty($recentRentals)): ?>
<p>Anda belum memiliki aktivitas peminjaman.</p>
<?php else: ?>
<div class="activity-list">
<?php foreach ($recentRentals as $rental): ?>
<div class="activity-item">
<div class="activity-details">
<h4>Peminjaman Paket <?= htmlspecialchars($rental['tool_id']) ?></h4>
<p>Tanggal: <?= htmlspecialchars($rental['start_date']) ?> - <?= htmlspecialchars($rental['end_date']) ?></p>
<p>Status: <span class="status <?= $rental['status'] ?>"><?= ucfirst($rental['status']) ?></span></p>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
</div>
</body>
</html>