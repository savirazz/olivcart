<?php
require_once __DIR__ . '/db.php';

function getCategories(): array
{
    return getDb()->query('SELECT * FROM categories ORDER BY id ASC')->fetchAll();
}

function findCategoryById(int $id): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $category = $stmt->fetch();
    return $category ?: null;
}

function findCategoryByName(string $name): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM categories WHERE LOWER(name) = LOWER(:name) LIMIT 1');
    $stmt->execute([':name' => trim($name)]);
    $category = $stmt->fetch();
    return $category ?: null;
}

function createCategory(string $name): array
{
    $name = trim($name);
    if ($name === '') {
        return ['success' => false, 'message' => 'Nama kategori harus diisi.'];
    }

    if (findCategoryByName($name)) {
        return ['success' => false, 'message' => 'Kategori sudah ada.'];
    }

    $stmt = getDb()->prepare('INSERT INTO categories (name, created_at) VALUES (:name, :created_at)');
    $stmt->execute([
        ':name' => $name,
        ':created_at' => date('Y-m-d H:i:s'),
    ]);

    return ['success' => true, 'category' => findCategoryById((int) getDb()->lastInsertId())];
}

function updateCategory(int $id, string $name): array
{
    $name = trim($name);
    if ($name === '') {
        return ['success' => false, 'message' => 'Nama kategori harus diisi.'];
    }

    $existing = findCategoryByName($name);
    if ($existing && (int) $existing['id'] !== $id) {
        return ['success' => false, 'message' => 'Nama kategori sudah digunakan.'];
    }

    $stmt = getDb()->prepare('UPDATE categories SET name = :name WHERE id = :id');
    $stmt->execute([':name' => $name, ':id' => $id]);

    return ['success' => true, 'category' => findCategoryById($id)];
}

function deleteCategory(int $id): bool
{
    $stmt = getDb()->prepare('DELETE FROM categories WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}
