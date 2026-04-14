<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbPath = __DIR__ . '/../storage/olivcart.sqlite';

try {
    $conn = new PDO("sqlite:" . $dbPath);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>