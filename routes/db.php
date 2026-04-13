<?php
$dbPath = __DIR__ . '/../storage/olivcart.sqlite';

$conn = new PDO("sqlite:" . $dbPath);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>