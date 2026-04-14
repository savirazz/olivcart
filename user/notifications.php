<?php
require_once "../config/db.php";
include "../components/header.php";

$user_id = $_SESSION['user_id'];

$notif = $conn->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC");
$notif->execute([$user_id]);
$data = $notif->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Notifikasi</h2>

<?php foreach ($data as $n): ?>
    <p><?= $n['message'] ?></p>
<?php endforeach; ?>