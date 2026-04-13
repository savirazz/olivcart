<?php

function getCart(): array
{
    return $_SESSION['cart'] ?? [];
}

function saveCart(array $cart): void
{
    $_SESSION['cart'] = $cart;
}

function addToCart(int $toolId, int $quantity, string $startDate, string $endDate): array
{
    $tool = findToolById($toolId);
    if (!$tool) {
        return ['success' => false, 'message' => 'Alat tidak ditemukan.'];
    }

    if ($quantity <= 0) {
        return ['success' => false, 'message' => 'Jumlah harus lebih dari 0.'];
    }

    if (intval($tool['stock']) < $quantity) {
        return ['success' => false, 'message' => 'Stok tidak mencukupi.'];
    }

    $start = strtotime($startDate);
    $end = strtotime($endDate);
    
    if ($start === false || $end === false) {
        return ['success' => false, 'message' => 'Format tanggal tidak valid.'];
    }

    if ($start >= $end) {
        return ['success' => false, 'message' => 'Tanggal selesai harus setelah tanggal mulai.'];
    }

    $cart = getCart();
    $itemKey = null;

    foreach ($cart as $key => $item) {
        if ((int)$item['tool_id'] === $toolId && $item['start_date'] === $startDate && $item['end_date'] === $endDate) {
            $itemKey = $key;
            break;
        }
    }

    if ($itemKey !== null) {
        $cart[$itemKey]['quantity'] += $quantity;
        if (intval($tool['stock']) < $cart[$itemKey]['quantity']) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi untuk total permintaan.'];
        }
    } else {
        $cart[] = [
            'tool_id' => $toolId,
            'quantity' => $quantity,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    saveCart($cart);
    return ['success' => true, 'message' => 'Alat berhasil ditambahkan ke keranjang.'];
}

function removeFromCart(int $toolId, string $startDate, string $endDate): bool
{
    $cart = getCart();
    foreach ($cart as $key => $item) {
        if ((int)$item['tool_id'] === $toolId && $item['start_date'] === $startDate && $item['end_date'] === $endDate) {
            array_splice($cart, $key, 1);
            saveCart($cart);
            return true;
        }
    }
    return false;
}

function updateCartItemQuantity(int $toolId, string $startDate, string $endDate, int $quantity): array
{
    if ($quantity <= 0) {
        if (removeFromCart($toolId, $startDate, $endDate)) {
            return ['success' => true];
        }
        return ['success' => false, 'message' => 'Item tidak ditemukan.'];
    }

    $tool = findToolById($toolId);
    if (!$tool) {
        return ['success' => false, 'message' => 'Alat tidak ditemukan.'];
    }

    if (intval($tool['stock']) < $quantity) {
        return ['success' => false, 'message' => 'Stok tidak mencukupi.'];
    }

    $cart = getCart();
    foreach ($cart as $key => $item) {
        if ((int)$item['tool_id'] === $toolId && $item['start_date'] === $startDate && $item['end_date'] === $endDate) {
            $cart[$key]['quantity'] = $quantity;
            saveCart($cart);
            return ['success' => true];
        }
    }

    return ['success' => false, 'message' => 'Item tidak ditemukan di keranjang.'];
}

function clearCart(): void
{
    $_SESSION['cart'] = [];
}

function getCartTotal(): float
{
    $total = 0.0;
    $cart = getCart();
    foreach ($cart as $item) {
        $tool = findToolById($item['tool_id']);
        if ($tool) {
            $total += floatval($tool['price']) * intval($item['quantity']);
        }
    }
    return $total;
}

function getCartItemCount(): int
{
    $cart = getCart();
    $count = 0;
    foreach ($cart as $item) {
        $count += intval($item['quantity']);
    }
    return $count;
}
