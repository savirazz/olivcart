<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/rental_helper.php';
requireAuth();
$user = currentUser();

$error = '';
$message = '';
$tools = getTools();
$myRentals = getRentalsByUserId($user['id']);
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$toolIdParam = isset($_GET['tool_id']) ? intval($_GET['tool_id']) : 0;

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'created') {
        $message = 'Permintaan peminjaman berhasil diajukan. Tunggu konfirmasi dari admin.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $toolId = isset($_POST['tool_id']) ? intval($_POST['tool_id']) : null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $startDate = trim($_POST['start_date'] ?? '');
    $endDate = trim($_POST['end_date'] ?? '');

    if ($postAction === 'request') {
        if ($toolId === null || $startDate === '' || $endDate === '') {
            $error = 'Semua field harus diisi.';
        } else {
            $result = createRental($user['id'], $toolId, $quantity, $startDate, $endDate);
            if ($result['success']) {
                header('Location: ajukan_peminjaman.php?success=created');
                exit;
            }
            $error = $result['message'];
        }
    }

    if ($postAction === 'cancel' && $id !== null) {
        $rental = findRentalById($id);
        if (!$rental) {
            $error = 'Peminjaman tidak ditemukan.';
        } elseif ((int)$rental['user_id'] !== $user['id']) {
            $error = 'Anda tidak berhak membatalkan peminjaman ini.';
        } elseif ($rental['status'] !== 'pending') {
            $error = 'Hanya peminjaman yang masih ditunggu verifikasi yang bisa dibatalkan.';
        } elseif (deleteRental($id)) {
            header('Location: ajukan_peminjaman.php?success=cancelled');
            exit;
        } else {
            $error = 'Gagal membatalkan peminjaman.';
        }
    }

    $myRentals = getRentalsByUserId($user['id']);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pengajuan Peminjaman - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<div class="navbar">
<div class="logo">Olvart</div>
<div class="right-nav">
<span>Halo, <?= htmlspecialchars($user['name']) ?></span>
<a href="list_alat.php">Katalog</a>
<a href="dashboard.php">Dashboard</a>
<a href="logout.php">Logout</a>
</div>
</div>
<div class="container">
<h1 class="title">Pengajuan Peminjaman</h1>
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
<h2 style="color:#7a0000;margin-top:0;">Ajukan Peminjaman Baru</h2>
<form method="POST" action="ajukan_peminjaman.php">
<input type="hidden" name="action" value="request">
<div class="form-grid">
<div>
<label>Pilih Alat</label>
<select name="tool_id" required>
<option value="">-- Pilih Alat --</option>
<?php foreach ($tools as $tool): ?>
<option value="<?= intval($tool['id']) ?>" <?= intval($tool['id']) === $toolIdParam ? 'selected' : '' ?>><?= htmlspecialchars($tool['name']) ?> (Stok: <?= intval($tool['stock']) ?>)</option>
<?php endforeach; ?>
</select>
</div>
<div>
<label>Jumlah</label>
<input type="number" name="quantity" min="1" value="1" required>
</div>
<div>
<label>Tanggal Mulai</label>
<input type="date" name="start_date" required>
</div>
<div>
<label>Tanggal Selesai</label>
<input type="date" name="end_date" required>
</div>
</div>
<div class="submit-area">
<button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
</div>
</form>
</div>

<div class="card">
<h2 style="color:#7a0000;margin-top:0;">Riwayat Peminjaman Saya</h2>
<?php if (count($myRentals) > 0): ?>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Alat</th>
<th>Jumlah</th>
<th>Tanggal Mulai</th>
<th>Tanggal Selesai</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($myRentals as $rental): 
    $tool = findToolById($rental['tool_id']);
?>
<tr>
<td><?= intval($rental['id']) ?></td>
<td><?= htmlspecialchars($tool['name'] ?? '-') ?></td>
<td><?= intval($rental['quantity']) ?></td>
<td><?= htmlspecialchars($rental['start_date']) ?></td>
<td><?= htmlspecialchars($rental['end_date']) ?></td>
<td>
<span class="status-badge" style="background-color:<?= getStatusColor($rental['status']) ?>;">
<?= htmlspecialchars(getStatusLabel($rental['status'])) ?>
</span>
</td>
<td>
<?php if ($rental['status'] === 'pending'): ?>
<form method="POST" action="ajukan_peminjaman.php" style="display:inline;" onsubmit="return confirm('Batalkan peminjaman ini?');">
<input type="hidden" name="action" value="cancel">
<input type="hidden" name="id" value="<?= intval($rental['id']) ?>">
<button type="submit" class="btn btn-danger">Batalkan</button>
</form>
<?php else: ?>
-
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="empty">Belum ada peminjaman. <a href="list_alat.php">Ajukan peminjaman sekarang</a></div>
<?php endif; ?>
</div>
</div>
</body>
</html>
