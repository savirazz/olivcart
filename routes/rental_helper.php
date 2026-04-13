<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/tool_helper.php';

define('LATE_FEE_RATE', 0.1); // 10% dari harga sewa harian per hari keterlambatan

function getRentals(): array
{
    return getDb()->query('SELECT * FROM rentals ORDER BY id ASC')->fetchAll();
}

function getRentalsByUser(int $userId): array
{
    $stmt = getDb()->prepare('SELECT * FROM rentals WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

function getAllRentals(): array
{
    return getRentals();
}

function findRentalById(int $id): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM rentals WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $rental = $stmt->fetch();
    return $rental ?: null;
}

function createRental(int $userId, int $toolId, int $quantity, string $startDate, string $endDate): array
{
    if ($quantity <= 0) {
        return ['success' => false, 'message' => 'Jumlah harus lebih dari 0.'];
    }

    $tool = findToolById($toolId);
    if (!$tool) {
        return ['success' => false, 'message' => 'Alat tidak ditemukan.'];
    }

    if (intval($tool['stock']) < $quantity) {
        return ['success' => false, 'message' => 'Stok tidak mencukupi untuk jumlah yang diminta.'];
    }

    $start = strtotime($startDate);
    $end = strtotime($endDate);

    if ($start === false || $end === false) {
        return ['success' => false, 'message' => 'Format tanggal tidak valid.'];
    }

    if ($start >= $end) {
        return ['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai.'];
    }

    $stmt = getDb()->prepare('INSERT INTO rentals (user_id, tool_id, quantity, start_date, end_date, status, rejection_reason, approved_by, approved_at, returned_at, late_days, late_fee, payment_status, created_at, updated_at) VALUES (:user_id, :tool_id, :quantity, :start_date, :end_date, :status, NULL, NULL, NULL, NULL, 0, 0.0, :payment_status, :created_at, :updated_at)');
    $stmt->execute([
        ':user_id' => $userId,
        ':tool_id' => $toolId,
        ':quantity' => $quantity,
        ':start_date' => $startDate,
        ':end_date' => $endDate,
        ':status' => 'pending',
        ':payment_status' => 'unpaid',
        ':created_at' => date('Y-m-d H:i:s'),
        ':updated_at' => date('Y-m-d H:i:s'),
    ]);

    return ['success' => true, 'rental' => findRentalById((int) getDb()->lastInsertId())];
}

function updateRentalStatus(int $id, string $status, ?string $reason = null, ?string $approvedBy = null): array
{
    if (!in_array($status, ['pending', 'approved', 'rejected', 'returned'])) {
        return ['success' => false, 'message' => 'Status tidak valid.'];
    }

    $rental = findRentalById($id);
    if (!$rental) {
        return ['success' => false, 'message' => 'Peminjaman tidak ditemukan.'];
    }

    if ($status === 'approved') {
        if (!updateToolStock($rental['tool_id'], -$rental['quantity'])) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi untuk menyetujui peminjaman.'];
        }
    }

    if ($status === 'returned') {
        updateToolStock($rental['tool_id'], $rental['quantity']);
        $returnedAt = date('Y-m-d H:i:s');
        $lateDays = calculateLateDays($rental, new DateTime($returnedAt));
        $lateFee = calculateLateFee($rental, $lateDays);

        $stmt = getDb()->prepare('UPDATE rentals SET status = :status, returned_at = :returned_at, late_days = :late_days, late_fee = :late_fee, updated_at = :updated_at WHERE id = :id');
        $stmt->execute([
            ':status' => $status,
            ':returned_at' => $returnedAt,
            ':late_days' => $lateDays,
            ':late_fee' => $lateFee,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $id,
        ]);
    } else {
        $stmt = getDb()->prepare('UPDATE rentals SET status = :status, updated_at = :updated_at' . ($status === 'rejected' ? ', rejection_reason = :rejection_reason' : '') . ($status === 'approved' ? ', approved_by = :approved_by, approved_at = :approved_at' : '') . ' WHERE id = :id');
        $params = [
            ':status' => $status,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $id,
        ];

        if ($status === 'rejected') {
            $params[':rejection_reason'] = $reason;
        }

        if ($status === 'approved') {
            $params[':approved_by'] = $approvedBy;
            $params[':approved_at'] = date('Y-m-d H:i:s');
        }

        $stmt->execute($params);
    }

    return ['success' => true, 'rental' => findRentalById($id)];
}

function calculateLateDays(array $rental, ?DateTime $returnDate = null): int
{
    $endDate = new DateTime($rental['end_date']);
    $returnDate = $returnDate ?? new DateTime();

    if ($returnDate <= $endDate) {
        return 0;
    }

    $interval = $endDate->diff($returnDate);
    return max(0, (int) $interval->days);
}

function calculateLateFee(array $rental, int $lateDays): float
{
    if ($lateDays <= 0) {
        return 0.0;
    }

    $tool = findToolById((int) $rental['tool_id']);
    if (!$tool || empty($tool['price'])) {
        return 0.0;
    }

    $pricePerDay = floatval($tool['price']);
    $quantity = intval($rental['quantity']);
    $fee = $lateDays * $quantity * $pricePerDay * LATE_FEE_RATE;
    return round($fee, 2);
}

function deleteRental(int $id): bool
{
    $stmt = getDb()->prepare('DELETE FROM rentals WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}

function getRentalsByUserId(int $userId): array
{
    $stmt = getDb()->prepare('SELECT * FROM rentals WHERE user_id = :user_id ORDER BY id ASC');
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

function getStatusLabel(string $status): string
{
    $labels = [
        'pending' => 'Menunggu Verifikasi',
        'approved' => 'Disetujui',
        'rejected' => 'Ditolak',
        'returned' => 'Dikembalikan',
    ];
    return $labels[$status] ?? $status;
}

function getStatusColor(string $status): string
{
    $colors = [
        'pending' => '#cfe0ff',
        'approved' => '#cdeccd',
        'rejected' => '#fdecea',
        'returned' => '#d9e7f7',
    ];
    return $colors[$status] ?? '#f0f0f0';
}

function isOverdue(array $rental): bool
{
    if ($rental['status'] === 'returned' && !empty($rental['returned_at'])) {
        return calculateLateDays($rental, new DateTime($rental['returned_at'])) > 0;
    }

    if ($rental['status'] === 'approved') {
        return strtotime($rental['end_date']) < time();
    }

    return false;
}
