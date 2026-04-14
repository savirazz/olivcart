<?php
require_once "../config/db.php";
include "../components/header.php";

session_start();
$user_id = $_SESSION['user_id'] ?? 0;

// ambil item cart
$stmt = $conn->prepare("
SELECT products.name, products.price, cart.qty,
(products.price * cart.qty) as subtotal
FROM cart
JOIN products ON products.id = cart.product_id
WHERE cart.user_id = ?
");

$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// total
$total = 0;
foreach ($items as $i) {
    $total += $i['subtotal'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran OlivCart</title>
    <style>
        body { font-family: Arial; background:#f5f5f5; }
        .container { width:80%; margin:auto; padding:20px; }
        .box { background:#fff; padding:20px; border-radius:10px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { padding:10px; border-bottom:1px solid #ddd; text-align:left; }
        .total { font-size:20px; font-weight:bold; margin-top:20px; }
        .btn {
            background:#28a745;
            color:#fff;
            padding:10px 20px;
            border:none;
            border-radius:5px;
            cursor:pointer;
            margin-top:15px;
        }
        .btn:hover { background:#218838; }
        .pay-method {
            margin-top:15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="box">
        <h2>💳 Pembayaran OlivCart</h2>

        <table>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>

            <?php foreach ($items as $i): ?>
            <tr>
                <td><?= $i['name'] ?></td>
                <td>Rp <?= number_format($i['price']) ?></td>
                <td><?= $i['qty'] ?></td>
                <td>Rp <?= number_format($i['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="total">
            Total Bayar: Rp <?= number_format($total) ?>
        </div>

        <div class="pay-method">
            <h3>Metode Pembayaran</h3>

            <label><input type="radio" name="pay" checked> Transfer Bank</label><br>
            <label><input type="radio" name="pay"> E-Wallet (OVO / DANA)</label><br>
            <label><input type="radio" name="pay"> COD</label>
        </div>

        <form method="POST" action="process.php">
            <input type="hidden" name="total" value="<?= $total ?>">
            <button class="btn">Bayar Sekarang</button>
        </form>
    </div>
</div>

</body>
</html>