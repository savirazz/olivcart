<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/payment_helper.php';
require_once __DIR__ . '/rental_helper.php';
require_once __DIR__ . '/tool_helper.php';

requireAuth();
$user = currentUser();

$error = '';
$paymentId = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$paymentId) {
    header('Location: dashboard.php');
    exit;
}

$payment = findPaymentById($paymentId);
if (!$payment || (int)$payment['user_id'] !== (int)$user['id'] || $payment['method'] !== 'gateway') {
    header('Location: dashboard.php');
    exit;
}

// Midtrans Configuration
// Note: Untuk production, gunakan server key dan client key yang sebenarnya
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-DEMO-KEY-DO-NOT-USE-IN-PRODUCTION');
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-DEMO-KEY-DO-NOT-USE-IN-PRODUCTION');
define('MIDTRANS_API_URL', 'https://api.sandbox.midtrans.com/v2');
define('MIDTRANS_SNAP_URL', 'https://snap.sandbox.midtrans.com/snap.js');

// If payment already paid, redirect to status page
if ($payment['status'] === 'paid') {
    header('Location: payment_status.php?id=' . $paymentId);
    exit;
}

// Get rental details
$rentals = [];
foreach ($payment['rental_ids'] as $rentalId) {
    $rental = findRentalById($rentalId);
    if ($rental) {
        $rentals[] = $rental;
    }
}

// Handle payment callback/webhook
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_status_callback'])) {
    $statusCode = isset($_POST['status_code']) ? trim($_POST['status_code']) : '';
    
    if ($statusCode === 'midtrans_processed') {
        // Simulate Midtrans processing
        $result = updatePaymentStatus($paymentId, 'paid', $user['name']);
        if ($result['success']) {
            // Mark rentals as paid
            markRentalsAsPaid($payment['rental_ids']);
        }
        header('Location: payment_status.php?id=' . $paymentId . '&paid=1');
        exit;
    }
}

// Build transaction item details
$itemDetails = [];
foreach ($rentals as $rental) {
    $tool = findToolById((int)$rental['tool_id']);
    $start = new DateTime($rental['start_date']);
    $end = new DateTime($rental['end_date']);
    $days = $end->diff($start)->days + 1;
    
    $itemDetails[] = [
        'id' => 'RENTAL-' . $rental['id'],
        'price' => floatval($tool['price']) * $days,
        'quantity' => intval($rental['quantity']),
        'name' => htmlspecialchars($tool['name']) . ' (' . $days . ' hari)'
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Gateway - Olvart</title>
<script src="<?= MIDTRANS_SNAP_URL ?>" data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>
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
<h1 class="title">Pembayaran dengan Payment Gateway</h1>

<div class="card">
<div class="info-section">
<div class="section-title" style="color:#7a0000;font-weight:700;font-size:16px;margin-bottom:15px;">Ringkasan Pembayaran</div>
<div class="info-row">
<span class="info-label">Kode Pembayaran:</span>
<span class="info-value"><span class="payment-code"><?= htmlspecialchars($payment['payment_code']) ?></span></span>
</div>
<div class="info-row">
<span class="info-label">Total Pembayaran:</span>
<span class="info-value" style="font-size:18px;color:#7a0000;">Rp <?= number_format($payment['amount'], 0, ',', '.') ?></span>
</div>
<div class="info-row">
<span class="info-label">Item yang dibayar:</span>
<span class="info-value"><?= count($rentals) ?> peminjaman</span>
</div>
</div>

<div class="security-badge">
🔒 Pembayaran Anda dilindungi oleh Midtrans dengan enkripsi SSL. Data kartu kredit tidak akan disimpan di server kami.
</div>

<h3 style="color:#7a0000;margin-top:20px;">Klik tombol dibawah untuk melanjutkan pembayaran:</h3>

<div class="btn-group">
<button id="pay-button" class="btn btn-secure">Lanjutkan ke Midtrans</button>
<button class="btn" style="background:#f2e7cf;color:#7a0000;border:1px solid #7a0000;" onclick="window.location.href='bayar.php';">Batalkan</button>
</div>

<div style="margin-top:30px;padding:15px;background:#fef9f3;border-radius:8px;font-size:13px;line-height:1.6;">
<strong>Metode Pembayaran yang Tersedia:</strong><br>
✓ Kartu Kredit (Visa, Mastercard, JCB)<br>
✓ Debit (Visa, Mastercard)<br>
✓ E-wallet (GCash, OVO, Dana, LinkAja)<br>
✓ Transfer Bank (BNI, BCA, Mandiri, BRI)<br>
✓ COD (Cash on Delivery)
</div>
</div>

<form id="payment-form" method="POST" style="display:none;">
<input type="hidden" name="payment_status_callback" value="1">
<input type="hidden" name="status_code" id="status_code">
</form>
</div>

<script>
// Snap Midtrans initialization
// For this demo, we'll simulate the payment process
// In production, replace with real Midtrans snap.redirectPrompt()

document.getElementById('pay-button').addEventListener('click', function() {
    // For demo/development: Simulate payment success
    // In production, initialize real Midtrans payment with actual server key
    
    if (confirm('Demo Mode: Simulasi pembayaran berhasil?')) {
        document.getElementById('status_code').value = 'midtrans_processed';
        document.getElementById('payment-form').submit();
    }
});

// For production Midtrans integration, uncomment below:
/*
snap.redirectPrompt({
    redirectUrl: 'https://example.com/payment_status.php?id=<?php echo $paymentId; ?>'
}, function(result) {
    if(result.status == 'success') {
        // Redirect to payment status page
        window.location.href = 'payment_status.php?id=<?php echo $paymentId; ?>&paid=1';
    }
});
*/
</script>

</body>
</html>
