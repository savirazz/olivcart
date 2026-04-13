<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/rental_helper.php';
requireAuth();
$user = currentUser();
if ($user['role'] !== 'admin' && $user['role'] !== 'petugas') {
    header('Location: dashboard.php');
    exit;
}

$message = '';
$error = '';

$rentals = getAllRentals();
$overdueRentals = array_filter($rentals, function($rental) {
    return strtotime($rental['return_date']) < time() && $rental['status'] === 'active';
});

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_fine'])) {
    $rentalId = intval($_POST['rental_id']);
    $fineAmount = floatval($_POST['fine_amount']);
    // Asumsikan ada fungsi untuk apply fine, tapi untuk sekarang, simpan di rental
    // Update rental with fine
    $rentalsData = json_decode(file_get_contents(__DIR__ . '/../storage/rentals.json'), true);
    foreach ($rentalsData as &$r) {
        if ($r['id'] == $rentalId) {
            $r['fine'] = $fineAmount;
            break;
        }
    }
    file_put_contents(__DIR__ . '/../storage/rentals.json', json_encode($rentalsData, JSON_PRETTY_PRINT));
    $message = 'Denda berhasil diterapkan.';
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Denda - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container">
<div class="card">
<h1 class="title">Manajemen Denda</h1>
<p>Kelola denda untuk peminjaman yang terlambat.</p>
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
</div>
<div class="card">
<h2>Peminjaman Terlambat</h2>
<table class="table">
<thead>
<tr>
<th>ID Peminjaman</th>
<th>User</th>
<th>Alat</th>
<th>Tanggal Kembali</th>
<th>Denda Saat Ini</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($overdueRentals as $rental): ?>
<tr>
<td><?= htmlspecialchars($rental['id']) ?></td>
<td><?= htmlspecialchars($rental['user_name']) ?></td>
<td><?= htmlspecialchars($rental['tool_name']) ?></td>
<td><?= htmlspecialchars($rental['return_date']) ?></td>
<td>Rp <?= number_format($rental['fine'] ?? 0) ?></td>
<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="rental_id" value="<?= $rental['id'] ?>">
<input type="number" name="fine_amount" placeholder="Jumlah denda" required>
<button type="submit" name="apply_fine">Terapkan Denda</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>