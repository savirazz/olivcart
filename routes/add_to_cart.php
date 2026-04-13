<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/cart_helper.php';
requireAuth();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list_alat.php');
    exit;
}

$toolId = isset($_POST['tool_id']) ? intval($_POST['tool_id']) : null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$startDate = trim($_POST['start_date'] ?? '');
$endDate = trim($_POST['end_date'] ?? '');

if ($toolId === null || $startDate === '' || $endDate === '') {
    header('Location: list_alat.php');
    exit;
}

$result = addToCart($toolId, $quantity, $startDate, $endDate);

if ($result['success']) {
    header('Location: keranjang.php?added=1');
} else {
    header('Location: list_alat.php?error=' . urlencode($result['message']));
}
exit;
