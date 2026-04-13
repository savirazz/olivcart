<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/rental_helper.php';

requireAuth();
$user = currentUser();

if (!canApproveRentals()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$message = '';
$rentals = array_filter(getRentals(), function($r) {
    return $r['status'] === 'approved';
});

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'returned') {
        $message = '✓ Alat telah dikembalikan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if ($postAction === 'return' && $id !== null) {
        $result = updateRentalStatus($id, 'returned', null, $user['name']);
        if ($result['success']) {
            header('Location: pengembalian.php?success=returned');
            exit;
        }
        $error = $result['message'];
    }

    $rentals = array_filter(getRentals(), function($r) {
        return $r['status'] === 'approved';
    });
}

// Sort rentals by end_date (soonest first)
usort($rentals, function($a, $b) {
    return strtotime($a['end_date']) - strtotime($b['end_date']);
});
?>
<!DOCTYPE html>
<html>
<head>
<title>Pengembalian Alat - Olvart Admin</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<div class="navbar">
<div class="logo">OLVART</div>
<div class="nav-links">
<a href="dashboard.php">Dashboard</a>
<a href="katalog.php">Katalog</a>
<a href="kelola_peminjaman.php">Kelola Peminjaman</a>
<a href="pengembalian.php">Pengembalian</a>
<a href="kelola_pembayaran.php">Kelola Pembayaran</a>
<a href="logout.php">Logout</a>
</div>
</div>

<div class="container">
<h1 class="title">Pengembalian Alat</h1>
<p class="subtitle">Kelola pengembalian alat yang sedang dipinjam</p>

<?php if ($message): ?>
<div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
<div class="error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
<?php if (empty($rentals)): ?>
<p>Tidak ada peminjaman yang disetujui untuk dikembalikan.</p>
<?php else: ?>
<table class="table">
<thead>
<tr>
<th>ID</th>
<th>Alat</th>
<th>Peminjam</th>
<th>Jumlah</th>
<th>Tanggal Mulai</th>
<th>Tanggal Selesai</th>
<th>Status</th>
<th>Denda</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($rentals as $rental): ?>
<?php $tool = findToolById($rental['tool_id']); ?>
<?php $renter = findUserById($rental['user_id']); ?>
<?php $overdue = isOverdue($rental); ?>
<?php $lateDays = calculateLateDays($rental); ?>
<?php $lateFee = calculateLateFee($rental, $lateDays); ?>
<tr class="<?php echo $overdue ? 'overdue' : ''; ?>">
<td><?php echo $rental['id']; ?></td>
<td><?php echo htmlspecialchars($tool ? $tool['name'] : 'Alat tidak ditemukan'); ?></td>
<td><?php echo htmlspecialchars($renter ? $renter['name'] : 'User tidak ditemukan'); ?></td>
<td><?php echo $rental['quantity']; ?></td>
<td><?php echo date('d/m/Y', strtotime($rental['start_date'])); ?></td>
<td><?php echo date('d/m/Y', strtotime($rental['end_date'])); ?><?php if ($overdue): ?> <strong>(Terlambat)</strong><?php endif; ?></td>
<td><span style="background:<?php echo getStatusColor($rental['status']); ?>;padding:4px 8px;border-radius:4px;"><?php echo getStatusLabel($rental['status']); ?></span></td>
<td><?php echo $lateDays > 0 ? $lateDays . ' hari / Rp ' . number_format($lateFee, 0, ',', '.') : '-'; ?></td>
<td>
<form method="post" style="display:inline;">
<input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
<input type="hidden" name="action" value="return">
<button type="submit" class="btn btn-return" onclick="return confirm('Apakah alat ini sudah dikembalikan?')">Kembalikan</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</div>
</div>
</body>
</html>