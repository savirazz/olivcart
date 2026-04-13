<?php
require_once __DIR__ . '/db.php';

define('IMAGE_DIR', __DIR__ . '/../storage/images');

if (!is_dir(IMAGE_DIR)) {
    mkdir(IMAGE_DIR, 0755, true);
}

function getTools(): array
{
    return getDb()->query('SELECT * FROM tools ORDER BY id ASC')->fetchAll();
}

function findToolById(int $id): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM tools WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $tool = $stmt->fetch();
    return $tool ?: null;
}

function findToolByName(string $name): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM tools WHERE LOWER(name) = LOWER(:name) LIMIT 1');
    $stmt->execute([':name' => trim($name)]);
    $tool = $stmt->fetch();
    return $tool ?: null;
}

function getNextToolId(): int
{
    return (int) getDb()->query('SELECT COALESCE(MAX(id), 0) + 1 FROM tools')->fetchColumn();
}

function uploadToolImage($file): ?string
{
    if (!isset($file['tmp_name']) || !isset($file['name'])) {
        return null;
    }

    $maxSize = 5 * 1024 * 1024;
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $filesize = $file['size'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($filesize > $maxSize) {
        return null;
    }
    if (!in_array($ext, $allowed)) {
        return null;
    }

    $newname = 'tool_' . time() . '_' . uniqid() . '.' . $ext;
    $destpath = IMAGE_DIR . '/' . $newname;

    if (move_uploaded_file($file['tmp_name'], $destpath)) {
        return $newname;
    }
    return null;
}

function deleteToolImage(string $imagename): bool
{
    if (empty($imagename)) {
        return true;
    }
    $filepath = IMAGE_DIR . '/' . basename($imagename);
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return true;
}

function createTool(string $name, string $description, int $stock, float $price, string $image = ''): array
{
    $name = trim($name);
    $description = trim($description);

    if ($name === '') {
        return ['success' => false, 'message' => 'Nama alat harus diisi.'];
    }
    if ($stock < 0) {
        return ['success' => false, 'message' => 'Stok harus bernilai 0 atau lebih.'];
    }
    if ($price < 0) {
        return ['success' => false, 'message' => 'Harga harus bernilai 0 atau lebih.'];
    }
    if (findToolByName($name)) {
        return ['success' => false, 'message' => 'Nama alat sudah ada.'];
    }

    $stmt = getDb()->prepare('INSERT INTO tools (name, description, stock, price, image, created_at) VALUES (:name, :description, :stock, :price, :image, :created_at)');
    $stmt->execute([
        ':name' => $name,
        ':description' => $description,
        ':stock' => $stock,
        ':price' => $price,
        ':image' => $image,
        ':created_at' => date('Y-m-d H:i:s'),
    ]);

    return ['success' => true, 'tool' => findToolById((int) getDb()->lastInsertId())];
}

function updateTool(int $id, string $name, string $description, int $stock, float $price, string $image = ''): array
{
    $name = trim($name);
    $description = trim($description);

    if ($name === '') {
        return ['success' => false, 'message' => 'Nama alat harus diisi.'];
    }
    if ($stock < 0) {
        return ['success' => false, 'message' => 'Stok harus bernilai 0 atau lebih.'];
    }
    if ($price < 0) {
        return ['success' => false, 'message' => 'Harga harus bernilai 0 atau lebih.'];
    }

    $existing = findToolByName($name);
    if ($existing && (int) $existing['id'] !== $id) {
        return ['success' => false, 'message' => 'Nama alat sudah digunakan oleh alat lain.'];
    }

    $stmt = getDb()->prepare('UPDATE tools SET name = :name, description = :description, stock = :stock, price = :price' . ($image !== '' ? ', image = :image' : '') . ' WHERE id = :id');
    $params = [
        ':name' => $name,
        ':description' => $description,
        ':stock' => $stock,
        ':price' => $price,
        ':id' => $id,
    ];
    if ($image !== '') {
        $params[':image'] = $image;
    }
    $existingTool = findToolById($id);
    $previousImage = $existingTool['image'] ?? '';

    $stmt->execute($params);

    if ($image !== '' && $previousImage !== '' && $previousImage !== $image) {
        deleteToolImage($previousImage);
    }

    return ['success' => true, 'tool' => findToolById($id)];
}

function deleteTool(int $id): bool
{
    $tool = findToolById($id);
    if ($tool && !empty($tool['image'])) {
        deleteToolImage($tool['image']);
    }

    $stmt = getDb()->prepare('DELETE FROM tools WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}

function getToolImage(string $imagename): string
{
    if (empty($imagename) || !file_exists(IMAGE_DIR . '/' . basename($imagename))) {
        return 'https://via.placeholder.com/400x300?text=Gambar+Alat';
    }
    return '../storage/images/' . urlencode(basename($imagename));
}

function updateToolStock(int $id, int $quantity): bool
{
    $tool = findToolById($id);
    if (!$tool) {
        return false;
    }

    $newStock = intval($tool['stock']) + $quantity;
    if ($newStock < 0) {
        return false;
    }

    $stmt = getDb()->prepare('UPDATE tools SET stock = :stock WHERE id = :id');
    return $stmt->execute([':stock' => $newStock, ':id' => $id]);
}
