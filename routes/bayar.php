<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cart_helper.php';
require_once __DIR__ . '/rental_helper.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/payment_helper.php';

requireAuth();
$user = currentUser();

$error = '';
$message = '';
$cart = getCart();
$paymentMethod = '';
$totalAmount = 0;

if (empty($cart)) {
    header('Location: keranjang.php');
    exit;
}

// Calculate total amount
foreach ($cart as $item) {
    $tool = findToolById($item['tool_id']);
    if ($tool) {
        $start = new DateTime($item['start_date']);
        $end = new DateTime($item['end_date']);
        $days = $end->diff($start)->days + 1;
        $subtotal = floatval($tool['price']) * intval($item['quantity']) * $days;
        $totalAmount += $subtotal;
    }
}
$totalAmount = round($totalAmount, 2);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $paymentMethod = trim($_POST['payment_method'] ?? '');

    if ($postAction === 'select_payment' && !empty($paymentMethod)) {
        if (!in_array($paymentMethod, ['cash', 'gateway'])) {
            $error = 'Metode pembayaran tidak valid.';
        } else {
            // Create rentals first
            $rentalIds = [];
            $success = true;
            
            foreach ($cart as $item) {
                $result = createRental(
                    $user['id'],
                    $item['tool_id'],
                    $item['quantity'],
                    $item['start_date'],
                    $item['end_date']
                );
                
                if (!$result['success']) {
                    $error = 'Gagal membuat peminjaman: ' . $result['message'];
                    $success = false;
                    break;
                }
                
                $rentalIds[] = (int)$result['rental']['id'];
            }

            if ($success && !empty($rentalIds)) {
                // Create payment record
                $paymentResult = createPayment(
                    $user['id'],
                    $totalAmount,
                    $rentalIds,
                    $paymentMethod,
                    'Pembayaran untuk ' . count($rentalIds) . ' peminjaman'
                );

                if ($paymentResult['success']) {
                    $paymentId = (int)$paymentResult['payment']['id'];
                    clearCart();
                    
                    if ($paymentMethod === 'cash') {
                        // Redirect to pending payment page
                        header('Location: payment_status.php?id=' . $paymentId . '&new=1');
                        exit;
                    } else {
                        // Redirect to payment gateway
                        header('Location: bayar_gateway.php?id=' . $paymentId);
                        exit;
                    }
                } else {
                    $error = 'Gagal membuat pembayaran: ' . $paymentResult['message'];
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pembayaran - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
<script>
function selectMethod(method) {
    document.querySelectorAll('.method-card').forEach(card => {
        card.classList.remove('selected');
    });
    document.querySelector('[data-method="' + method + '"]').classList.add('selected');
    document.getElementById('paymentMethodInput').value = method;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.method-card').forEach(card => {
        card.addEventListener('click', function() {
            const method = this.getAttribute('data-method');
            this.querySelector('input[type="radio"]').checked = true;
            selectMethod(method);
        });
    });
});

function submitPayment() {
    const method = document.getElementById('paymentMethodInput').value;
    if (!method) {
        alert('Silakan pilih metode pembayaran!');
        return false;
    }
    document.getElementById('paymentForm').submit();
}
</script>
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
<a href="keranjang.php" class="back-link">← Kembali ke Keranjang</a>
<h1 class="title">Pembayaran</h1>

<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
<h2 style="color:#7a0000;margin-top:0;">Ringkasan Pesanan</h2>
<div class="summary">
<div class="summary-row">
<span>Item dalam keranjang:</span>
<span><?= count($cart) ?> item</span>
</div>
<div class="summary-row">
<span>Total biaya rental:</span>
<span style="font-weight:700;">Rp <?= number_format($totalAmount, 0, ',', '.') ?></span>
</div>
<div class="summary-total">
Total Pembayaran: Rp <?= number_format($totalAmount, 0, ',', '.') ?>
</div>
</div>
</div>

<div class="card">
<h2 style="color:#7a0000;margin-top:0;">Pilih Metode Pembayaran</h2>

<form id="paymentForm" method="POST" action="bayar.php">
<input type="hidden" name="action" value="select_payment">
<input type="hidden" name="payment_method" id="paymentMethodInput" value="">

<div class="methods">
<div class="method-card" data-method="cash">
<div class="method-icon">💰</div>
<div class="method-title">Pembayaran Tunai</div>
<div class="method-desc">
Bayar langsung di tempat saat penjemputan alat. Admin akan verifikasi pembayaran Anda.
</div>
<input type="radio" name="payment_method_radio" value="cash">
</div>

<div class="method-card" data-method="gateway">
<div class="method-icon">💳</div>
<div class="method-title">Payment Gateway (Midtrans)</div>
<div class="method-desc">
Bayar menggunakan kartu kredit, debit, e-wallet, atau transfer bank melalui Midtrans.
</div>
<input type="radio" name="payment_method_radio" value="gateway">
</div>
</div>

<div class="btn-group">
<button type="button" class="btn btn-secondary" onclick="window.location.href='keranjang.php';">Batal</button>
<button type="button" class="btn btn-primary" onclick="submitPayment();">Lanjutkan Pembayaran</button>
</div>
</form>
</div>
</div>
</body>
</html>
