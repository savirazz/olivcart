<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/cart_helper.php';
require_once __DIR__ . '/rental_helper.php';
requireAuth();
$user = currentUser();

$error = '';
$message = '';
$cart = getCart();

if (isset($_GET['success'])) {
    $message = 'Semua peminjaman berhasil diajukan!';
}

if (isset($_GET['added'])) {
    $message = 'Item berhasil ditambahkan ke keranjang.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'remove') {
        $toolId = isset($_POST['tool_id']) ? intval($_POST['tool_id']) : null;
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');

        if ($toolId !== null && removeFromCart($toolId, $startDate, $endDate)) {
            header('Location: keranjang.php');
            exit;
        }
        $error = 'Gagal menghapus item dari keranjang.';
        $cart = getCart();
    }

    if ($postAction === 'update') {
        $toolId = isset($_POST['tool_id']) ? intval($_POST['tool_id']) : null;
        $startDate = trim($_POST['start_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        if ($toolId !== null) {
            $result = updateCartItemQuantity($toolId, $startDate, $endDate, $quantity);
            if (!$result['success']) {
                $error = $result['message'];
            }
        }
        $cart = getCart();
    }

    if ($postAction === 'checkout') {
        if (count($cart) === 0) {
            $error = 'Keranjang kosong.';
        } else {
            // Redirect to payment page instead of directly creating rentals
            header('Location: bayar.php');
            exit;
        }
    }

    if ($postAction === 'clear') {
        clearCart();
        header('Location: keranjang.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Keranjang - Olvart</title>
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
<h1 class="title">Keranjang Peminjaman</h1>
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
<?php if (count($cart) > 0): ?>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>Alat</th>
<th>Tanggal Mulai</th>
<th>Tanggal Selesai</th>
<th>Harga/Unit</th>
<th>Qty</th>
<th>Subtotal</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($cart as $item): 
    $tool = findToolById($item['tool_id']);
    if (!$tool) continue;
    $subtotal = floatval($tool['price']) * intval($item['quantity']);
?>
<tr>
<td><?= htmlspecialchars($tool['name']) ?></td>
<td><?= htmlspecialchars($item['start_date']) ?></td>
<td><?= htmlspecialchars($item['end_date']) ?></td>
<td>Rp <?= number_format($tool['price'], 0, ',', '.') ?></td>
<td>
<form method="POST" action="keranjang.php" style="display:flex;gap:6px;">
<input type="hidden" name="action" value="update">
<input type="hidden" name="tool_id" value="<?= intval($item['tool_id']) ?>">
<input type="hidden" name="start_date" value="<?= htmlspecialchars($item['start_date']) ?>">
<input type="hidden" name="end_date" value="<?= htmlspecialchars($item['end_date']) ?>">
<input type="number" name="quantity" min="1" max="<?= intval($tool['stock']) ?>" value="<?= intval($item['quantity']) ?>" onchange="this.form.submit();">
</form>
</td>
<td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
<td>
<form method="POST" action="keranjang.php" style="display:inline;">
<input type="hidden" name="action" value="remove">
<input type="hidden" name="tool_id" value="<?= intval($item['tool_id']) ?>">
<input type="hidden" name="start_date" value="<?= htmlspecialchars($item['start_date']) ?>">
<input type="hidden" name="end_date" value="<?= htmlspecialchars($item['end_date']) ?>">
<button type="submit" class="remove-btn">Hapus</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<div class="summary">
<div class="summary-row">
<span>Jumlah Item:</span>
<span><?= getCartItemCount() ?> unit</span>
</div>
<div class="summary-row">
<span>Jumlah Peminjaman:</span>
<span><?= count($cart) ?> paket</span>
</div>
<div class="summary-total">
<div class="summary-row">
<span>Total Harga:</span>
<span>Rp <?= number_format(getCartTotal(), 0, ',', '.') ?></span>
</div>
</div>
</div>

<div class="action-buttons">
<a href="list_alat.php" class="btn btn-secondary">Lanjut Belanja</a>
<form method="POST" action="keranjang.php" style="display:inline;">
<input type="hidden" name="action" value="clear">
<button type="submit" class="btn btn-secondary">Kosongkan Keranjang</button>
</form>
<form method="POST" action="keranjang.php" style="display:inline;">
<input type="hidden" name="action" value="checkout">
<button type="submit" class="btn btn-primary">Ajukan Semua Peminjaman</button>
</form>
</div>

<?php else: ?>
<div class="empty">
<p>Keranjang Anda kosong.</p>
<a href="list_alat.php" class="btn btn-primary">Mulai Berbelanja</a>
</div>
<?php endif; ?>
</div>
</div>
</body>
</html>
