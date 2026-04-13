<?php
require_once __DIR__ . '/db.php';

function getPayments(): array
{
    $rows = getDb()->query('SELECT * FROM payments ORDER BY id ASC')->fetchAll();
    foreach ($rows as &$row) {
        $row['rental_ids'] = json_decode($row['rental_ids'], true) ?: [];
    }
    return $rows;
}

function findPaymentById(int $id): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM payments WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $payment = $stmt->fetch();
    if (!$payment) {
        return null;
    }
    $payment['rental_ids'] = json_decode($payment['rental_ids'], true) ?: [];
    return $payment;
}

function getNextPaymentId(): int
{
    return (int) getDb()->query('SELECT COALESCE(MAX(id), 0) + 1 FROM payments')->fetchColumn();
}

function createPayment(int $userId, float $amount, array $rentalIds, string $method, ?string $notes = null): array
{
    if ($amount <= 0) {
        return ['success' => false, 'message' => 'Jumlah pembayaran harus lebih dari 0.'];
    }

    if (!in_array($method, ['cash', 'gateway'])) {
        return ['success' => false, 'message' => 'Metode pembayaran tidak valid.'];
    }

    if (empty($rentalIds)) {
        return ['success' => false, 'message' => 'Minimal ada satu peminjaman.'];
    }

    $paymentId = getNextPaymentId();
    $paymentCode = 'PAY-' . strtoupper(substr(md5(time() . $paymentId), 0, 8));
    $status = $method === 'cash' ? 'pending' : 'unpaid';
    $orderId = $method === 'gateway' ? 'ORDER-' . date('YmdHis') . '-' . $paymentId : null;

    $stmt = getDb()->prepare('INSERT INTO payments (user_id, payment_code, order_id, rental_ids, amount, method, status, notes, gateway_response, paid_at, verified_by, verified_at, created_at, updated_at) VALUES (:user_id, :payment_code, :order_id, :rental_ids, :amount, :method, :status, :notes, NULL, NULL, NULL, NULL, :created_at, :updated_at)');
    $stmt->execute([
        ':user_id' => $userId,
        ':payment_code' => $paymentCode,
        ':order_id' => $orderId,
        ':rental_ids' => json_encode(array_values($rentalIds)),
        ':amount' => floatval($amount),
        ':method' => $method,
        ':status' => $status,
        ':notes' => $notes,
        ':created_at' => date('Y-m-d H:i:s'),
        ':updated_at' => date('Y-m-d H:i:s'),
    ]);

    return ['success' => true, 'payment' => findPaymentById((int) getDb()->lastInsertId())];
}

function updatePaymentStatus(int $id, string $status, ?string $verifiedBy = null): array
{
    if (!in_array($status, ['unpaid', 'paid', 'cancelled', 'expired'])) {
        return ['success' => false, 'message' => 'Status pembayaran tidak valid.'];
    }

    $payment = findPaymentById($id);
    if (!$payment) {
        return ['success' => false, 'message' => 'Pembayaran tidak ditemukan.'];
    }

    $stmt = getDb()->prepare('UPDATE payments SET status = :status, updated_at = :updated_at' . ($status === 'paid' ? ', paid_at = :paid_at, verified_by = :verified_by, verified_at = :verified_at' : '') . ' WHERE id = :id');
    $params = [
        ':status' => $status,
        ':updated_at' => date('Y-m-d H:i:s'),
        ':id' => $id,
    ];

    if ($status === 'paid') {
        $params[':paid_at'] = date('Y-m-d H:i:s');
        $params[':verified_by'] = $verifiedBy;
        $params[':verified_at'] = date('Y-m-d H:i:s');
    }

    $stmt->execute($params);
    return ['success' => true, 'payment' => findPaymentById($id)];
}

function getPaymentStatusLabel(string $status): string
{
    $labels = [
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Sudah Dibayar',
        'pending' => 'Menunggu Verifikasi',
        'cancelled' => 'Dibatalkan',
        'expired' => 'Expired',
    ];
    return $labels[$status] ?? $status;
}

function getPaymentStatusColor(string $status): string
{
    $colors = [
        'unpaid' => '#fdecea',
        'paid' => '#cdeccd',
        'pending' => '#cfe0ff',
        'cancelled' => '#f0f0f0',
        'expired' => '#ffe5cc',
    ];
    return $colors[$status] ?? '#f0f0f0';
}

function getPaymentMethodLabel(string $method): string
{
    $labels = [
        'cash' => 'Tunai (Cash)',
        'gateway' => 'Payment Gateway (Midtrans)',
    ];
    return $labels[$method] ?? $method;
}

function calculateTotalAmountForRentals(array $rentalIds): float
{
    require_once __DIR__ . '/rental_helper.php';
    require_once __DIR__ . '/tool_helper.php';

    $total = 0.0;
    foreach ($rentalIds as $rentalId) {
        $rental = findRentalById((int) $rentalId);
        if (!$rental) {
            continue;
        }

        $start = new DateTime($rental['start_date']);
        $end = new DateTime($rental['end_date']);
        $days = $end->diff($start)->days + 1;
        $tool = findToolById((int) $rental['tool_id']);
        if ($tool && !empty($tool['price'])) {
            $total += floatval($tool['price']) * intval($rental['quantity']) * $days;
        }
    }

    return round($total, 2);
}

function markRentalsAsPaid(array $rentalIds): int
{
    $updated = 0;
    $stmt = getDb()->prepare('UPDATE rentals SET payment_status = :status, updated_at = :updated_at WHERE id = :id');

    foreach ($rentalIds as $rentalId) {
        $stmt->execute([':status' => 'paid', ':updated_at' => date('Y-m-d H:i:s'), ':id' => (int) $rentalId]);
        $updated += $stmt->rowCount();
    }

    return $updated;
}
