<?php
require_once "../config/db.php";
include "../components/header.php";

$user_id = $_SESSION['user_id'];

$data = $conn->prepare("SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC");
$data->execute([$user_id]);
$result = $data->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Riwayat</h2>

<?php foreach ($result as $r): ?>
    <div>
        <p>Total: Rp <?= $r['total'] ?></p>
        <p>Status: <?= $r['status'] ?></p>
    </div>
<?php endforeach; ?>