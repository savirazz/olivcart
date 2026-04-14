<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
require_once "../config/db.php";

$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-Type: application/json");
echo json_encode($products);