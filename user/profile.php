<?php
require_once "../config/db.php";
include "../components/header.php";

$user_id = $_SESSION['user_id'];

$user = $conn->prepare("SELECT * FROM users WHERE id=?");
$user->execute([$user_id]);
$data = $user->fetch(PDO::FETCH_ASSOC);
?>

<h2>Profile</h2>

<p>Nama: <?= $data['name'] ?></p>
<p>Email: <?= $data['email'] ?></p>