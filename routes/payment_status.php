<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/payment_helper.php';
require_once __DIR__ . '/rental_helper.php';
require_once __DIR__ . '/tool_helper.php';

requireAuth();
$user = currentUser();

$error = '';
$message = '';
$paymentId = isset($_GET['id']) ? intval($_GET['id']) : null;
$isNew = isset($_GET['new']) ? true : false;

if (!$paymentId) {
    header('Location: dashboard.php');
    exit;
}

$payment = findPaymentById($paymentId);
if (!$payment || (int)$payment['user_id'] !== (int)$user['id']) {
    header('Location: dashboard.php');
    exit;
}

if ($isNew && $payment['method'] === 'cash' && $payment['status'] === 'pending') {
    $message = '✓ Peminjaman + Pembayaran berhasil dibuat. Silakan lakukan pembayaran tunai saat penjemputan alat.';
}

// Get rental details
$rentals = [];
foreach ($payment['rental_ids'] as $rentalId) {
    $rental = findRentalById($rentalId);
    if ($rental) {
        $rentals[] = $rental;
    }
}

// Calculate rental details
$rentalDetails = [];
foreach ($rentals as $rental) {
    $tool = findToolById((int)$rental['tool_id']);
    $start = new DateTime($rental['start_date']);
    $end = new DateTime($rental['end_date']);
    $days = $end->diff($start)->days + 1;
    $subtotal = floatval($tool['price']) * intval($rental['quantity']) * $days;
    
    $rentalDetails[] = [
        'rental' => $rental,
        'tool' => $tool,
        'days' => $days,
        'subtotal' => $subtotal
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Status Pembayaran - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<div class="navbar">
<div class="logo">Olvart</div>
<div class="right-nav">
<span>Halo, <?= htmlspecialchars($user['name']) ?></span>
<a href="dashboard.php">Dashboard</a>
<a href="logout.php">Logout</a>
</div>
</div>

<div class="container">
<h1 class="title">Status Pembayaran</h1>

<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="card">
<div class="status-section">
<div class="section-title">Informasi Pembayaran</div>
<div class="info-row">
<div class="info-label">Kode Pembayaran:</div>
<div class="info-value"><span class="payment-code"><?= htmlspecialchars($payment['payment_code']) ?></span></div>
</div>
<div class="info-row">
<div class="info-label">Metode Pembayaran:</div>
<div class="info-value"><?= htmlspecialchars(getPaymentMethodLabel($payment['method'])) ?></div>
</div>
<div class="info-row">
<div class="info-label">Status:</div>
<div class="info-value">
<span class="status-badge status-<?= $payment['status'] ?>">
<?= htmlspecialchars(getPaymentStatusLabel($payment['status'])) ?>
</span>
</div>
</div>
<div class="info-row">
<div class="info-label">Jumlah Pembayaran:</div>
<div class="info-value" style="font-weight:700;font-size:18px;color:#7a0000;">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></div>
</div>
<div class="info-row">
<div class="info-label">Dibuat:</div>
<div class="info-value"><?= htmlspecialchars(date('d M Y H:i:s', strtotime($payment['created_at']))) ?></div>
</div>

<?php if ($payment['status'] === 'paid' && $payment['verified_by']): ?>
<div class="info-row">
<div class="info-label">Diverifikasi oleh:</div>
<div class="info-value"><?= htmlspecialchars($payment['verified_by']) ?> pada <?= htmlspecialchars(date('d M Y H:i:s', strtotime($payment['verified_at']))) ?></div>
</div>
<?php endif; ?>
</div>

<?php if ($payment['method'] === 'cash' && $payment['status'] === 'pending'): ?>
<div class="notes">
<strong>⚠️ Informasi Penting:</strong><br>
Pembayaran tunai akan diverifikasi oleh admin/petugas saat Anda menjemput alat. Setelah pembayaran diverifikasi, status akan berubah menjadi "Sudah Dibayar".
</div>
<?php endif; ?>

<div class="status-section">
<div class="section-title">Detail Peminjaman (<?= count($rentalDetails) ?> item)</div>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Alat</th>
<th>Qty</th>
<th>Periode</th>
<th>Hari</th>
<th>Harga/Hari</th>
<th>Subtotal</th>
</tr>
</thead>
<tbody>
<?php foreach ($rentalDetails as $detail): ?>
<tr>
<td><?= htmlspecialchars($detail['tool']['name']) ?></td>
<td><?= intval($detail['rental']['quantity']) ?></td>
<td><?= htmlspecialchars($detail['rental']['start_date']) ?> s/d <?= htmlspecialchars($detail['rental']['end_date']) ?></td>
<td><?= intval($detail['days']) ?> hari</td>
<td>Rp <?= number_format($detail['tool']['price'], 0, ',', '.') ?></td>
<td style="font-weight:700;">Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

<div class="receipt-section">
<div class="receipt-header">
<div class="receipt-title">BUKTI PEMBAYARAN</div>
<div class="receipt-subtitle"><?= htmlspecialchars($payment['payment_code']) ?></div>
</div>

<div class="receipt-row">
<span>Kode Pembayaran:</span>
<span><?= htmlspecialchars($payment['payment_code']) ?></span>
</div>
<div class="receipt-row">
<span>Metode:</span>
<span><?= htmlspecialchars(getPaymentMethodLabel($payment['method'])) ?></span>
</div>
<div class="receipt-row">
<span>Tanggal:</span>
<span><?= htmlspecialchars(date('d M Y H:i:s', strtotime($payment['created_at']))) ?></span>
</div>
<div class="receipt-row">
<span>Jumlah Item:</span>
<span><?= count($rentalDetails) ?> item</span>
</div>

<div class="receipt-row receipt-total">
<span>TOTAL PEMBAYARAN:</span>
<span>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></span>
</div>

<?php if ($payment['status'] === 'paid'): ?>
<div style="text-align:center;margin-top:15px;color:#106c35;font-weight:700;">
✓ PEMBAYARAN TELAH DIKONFIRMASI
</div>
<?php else: ?>
<div style="text-align:center;margin-top:15px;color:#a11717;font-weight:700;">
⏳ MENUNGGU PEMBAYARAN
</div>
<?php endif; ?>
</div>

<div class="btn-group">
<button class="btn btn-secondary" onclick="window.print();">Cetak Bukti</button>
<button class="btn btn-primary" onclick="window.location.href='dashboard.php';">Kembali ke Dashboard</button>
</div>
</div>
</div>
</body>
</html>
