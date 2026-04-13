<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/payment_helper.php';
require_once __DIR__ . '/rental_helper.php';
require_once __DIR__ . '/tool_helper.php';

requireAuth();
$user = currentUser();

if (!canApproveRentals()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$message = '';
$payments = getPayments();
$paymentMethod = $_GET['method'] ?? '';
$paymentStatus = $_GET['status'] ?? '';

// Filter by method
if ($paymentMethod && in_array($paymentMethod, ['cash', 'gateway'])) {
    $payments = array_filter($payments, function($p) use ($paymentMethod) {
        return $p['method'] === $paymentMethod;
    });
}

// Filter by status
if ($paymentStatus && in_array($paymentStatus, ['unpaid', 'paid', 'pending', 'cancelled'])) {
    $payments = array_filter($payments, function($p) use ($paymentStatus) {
        return $p['status'] === $paymentStatus;
    });
}

// Handle payment verification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $paymentId = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : null;
    
    if ($postAction === 'verify' && $paymentId !== null) {
        // Verify cash payment as paid
        $result = updatePaymentStatus($paymentId, 'paid', $user['name']);
        if ($result['success']) {
            // Get payment and mark rentals as paid
            $payment = findPaymentById($paymentId);
            if ($payment) {
                markRentalsAsPaid($payment['rental_ids']);
            }
            $message = '✓ Pembayaran telah diverifikasi dan dikonfirmasi.';
            $payments = getPayments();
            if ($paymentMethod && in_array($paymentMethod, ['cash', 'gateway'])) {
                $payments = array_filter($payments, function($p) use ($paymentMethod) {
                    return $p['method'] === $paymentMethod;
                });
            }
            if ($paymentStatus && in_array($paymentStatus, ['unpaid', 'paid', 'pending', 'cancelled'])) {
                $payments = array_filter($payments, function($p) use ($paymentStatus) {
                    return $p['status'] === $paymentStatus;
                });
            }
        } else {
            $error = $result['message'];
        }
    }
    
    if ($postAction === 'reject' && $paymentId !== null) {
        $result = updatePaymentStatus($paymentId, 'cancelled', $user['name']);
        if ($result['success']) {
            $message = '✓ Pembayaran telah ditolak/dibatalkan.';
            $payments = getPayments();
            if ($paymentMethod && in_array($paymentMethod, ['cash', 'gateway'])) {
                $payments = array_filter($payments, function($p) use ($paymentMethod) {
                    return $p['method'] === $paymentMethod;
                });
            }
        } else {
            $error = $result['message'];
        }
    }
}

// Sort by created_at descending
usort($payments, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>
<!DOCTYPE html>
<html>
<head>
<title>Kelola Pembayaran - Olvart Admin</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<div class="navbar">
<div class="logo">Olvart Admin</div>
<div class="nav-links">
<a href="dashboard.php">Dashboard</a>
<a href="user_management.php">User</a>
<a href="alat_management.php">Alat</a>
<a href="logout.php">Logout</a>
</div>
</div>

<div class="container">
<h1 class="title">Kelola Pembayaran</h1>
<p class="subtitle">Verifikasi dan kelola pembayaran dari pengguna</p>

<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
<div class="filter-group">
<div class="filter-group-title">FILTER METODE PEMBAYARAN</div>
<div class="filters">
<a href="kelola_pembayaran.php" class="filter-btn <?= !$paymentMethod ? 'active' : '' ?>">Semua</a>
<a href="kelola_pembayaran.php?method=cash" class="filter-btn <?= $paymentMethod === 'cash' ? 'active' : '' ?>">Tunai (<?= count(array_filter(getPayments(), function($p) { return $p['method'] === 'cash'; })) ?>)</a>
<a href="kelola_pembayaran.php?method=gateway" class="filter-btn <?= $paymentMethod === 'gateway' ? 'active' : '' ?>">Gateway (<?= count(array_filter(getPayments(), function($p) { return $p['method'] === 'gateway'; })) ?>)</a>
</div>
</div>

<div class="filter-group">
<div class="filter-group-title">FILTER STATUS</div>
<div class="filters">
<a href="<?= !$paymentMethod ? 'kelola_pembayaran.php' : 'kelola_pembayaran.php?method=' . $paymentMethod ?>" class="filter-btn <?= !$paymentStatus ? 'active' : '' ?>">Semua (<?= count($paymentMethod ? ($paymentMethod === 'cash' ? array_filter(getPayments(), function($p) { return $p['method'] === 'cash'; }) : array_filter(getPayments(), function($p) { return $p['method'] === 'gateway'; })) : getPayments()) ?>)</a>
<a href="<?= !$paymentMethod ? 'kelola_pembayaran.php?status=pending' : 'kelola_pembayaran.php?method=' . $paymentMethod . '&status=pending' ?>" class="filter-btn <?= $paymentStatus === 'pending' ? 'active' : '' ?>">Menunggu Verifikasi (<?= count(array_filter(getPayments(), function($p) { return $p['status'] === 'pending'; })) ?>)</a>
<a href="<?= !$paymentMethod ? 'kelola_pembayaran.php?status=unpaid' : 'kelola_pembayaran.php?method=' . $paymentMethod . '&status=unpaid' ?>" class="filter-btn <?= $paymentStatus === 'unpaid' ? 'active' : '' ?>">Belum Bayar (<?= count(array_filter(getPayments(), function($p) { return $p['status'] === 'unpaid'; })) ?>)</a>
<a href="<?= !$paymentMethod ? 'kelola_pembayaran.php?status=paid' : 'kelola_pembayaran.php?method=' . $paymentMethod . '&status=paid' ?>" class="filter-btn <?= $paymentStatus === 'paid' ? 'active' : '' ?>">Dibayar (<?= count(array_filter(getPayments(), function($p) { return $p['status'] === 'paid'; })) ?>)</a>
</div>
</div>

<?php if (count($payments) > 0): ?>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Kode</th>
<th>User</th>
<th>Metode</th>
<th>Jumlah</th>
<th>Items</th>
<th>Status</th>
<th>Tanggal</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($payments as $payment):
    $userInfo = findUserById($payment['user_id']);
?>
<tr>
<td><span class="payment-code"><?= htmlspecialchars(substr($payment['payment_code'], 0, 8)) ?> </span></td>
<td><?= htmlspecialchars($userInfo['name'] ?? 'N/A') ?></td>
<td><span class="payment-badge"><?= htmlspecialchars(getPaymentMethodLabel($payment['method'])) ?></span></td>
<td style="font-weight:700;">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
<td><?= count($payment['rental_ids']) ?> item</td>
<td>
<span class="status-badge" style="background-color:<?= getPaymentStatusColor($payment['status']) ?>;">
<?= htmlspecialchars(getPaymentStatusLabel($payment['status'])) ?>
</span>
</td>
<td><?= htmlspecialchars(date('d M Y', strtotime($payment['created_at']))) ?></td>
<td>
<?php if ($payment['status'] === 'pending' || $payment['status'] === 'unpaid'): ?>
<form method="POST" action="kelola_pembayaran.php<?= ($paymentMethod ? '?method=' . $paymentMethod : '') . ($paymentStatus ? ($paymentMethod ? '&' : '?') . 'status=' . $paymentStatus : '') ?>" class="form-inline">
<input type="hidden" name="action" value="verify">
<input type="hidden" name="payment_id" value="<?= intval($payment['id']) ?>">
<button type="submit" class="btn btn-verify">Verifikasi</button>
</form>
<?php elseif ($payment['status'] === 'paid'): ?>
<span style="color:#106c35;font-weight:700;font-size:12px;">✓ Terbayar</span>
<?php endif; ?>
<a href="payment_status.php?id=<?= intval($payment['id']) ?>&admin=1" class="btn btn-view">Detail</a>
</td>
</tr>

<?php if ($payment['verified_by']): ?>
<tr>
<td colspan="8">
<div class="notes-row">
<div class="notes-label">✓ Diverifikasi oleh: <?= htmlspecialchars($payment['verified_by']) ?> pada <?= htmlspecialchars(date('d M Y H:i', strtotime($payment['verified_at'] ?? $payment['updated_at']))) ?></div>
</div>
</td>
</tr>
<?php endif; ?>

<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="empty">Tidak ada pembayaran untuk ditampilkan.</div>
<?php endif; ?>
</div>
</div>
</body>
</html>
