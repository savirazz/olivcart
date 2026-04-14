<?php
require_once "../config/db.php";
include "../components/header.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT cart.*, products.name, products.price
FROM cart
JOIN products ON products.id = cart.product_id
WHERE cart.user_id = ?
");

$stmt->execute([$user_id]);
$cart = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Keranjang</h2>

<?php foreach ($cart as $c): ?>
    <div>
        <p><?= $c['name'] ?> (<?= $c['qty'] ?>) - Rp <?= $c['price'] ?></p>
    </div>
<?php endforeach; ?>

<a href="../payment/index.php">Checkout</a>