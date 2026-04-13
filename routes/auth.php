<?php
$secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'] ?? '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();
require_once __DIR__ . '/db.php';

function getUsers(): array
{
    return getDb()->query('SELECT * FROM users ORDER BY id ASC')->fetchAll();
}

function findUserByEmail(string $email): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(:email) LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function findUserById(int $id): ?array
{
    $stmt = getDb()->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();
    return $user ?: null;
}

function getNextUserId(): int
{
    $nextId = (int) getDb()->query('SELECT COALESCE(MAX(id), 0) + 1 FROM users')->fetchColumn();
    return $nextId;
}

function registerUser(string $name, string $email, string $password, string $role = 'user'): array
{
    if (findUserByEmail($email)) {
        return ['success' => false, 'message' => 'Email sudah terdaftar. Silakan gunakan email lain.'];
    }

    $stmt = getDb()->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, :created_at)');
    $stmt->execute([
        ':name' => trim($name),
        ':email' => trim($email),
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':role' => $role,
        ':created_at' => date('Y-m-d H:i:s'),
    ]);

    return ['success' => true, 'user' => findUserById((int) getDb()->lastInsertId())];
}

function updateUser(int $id, array $data): array
{
    $user = findUserById($id);
    if (!$user) {
        return ['success' => false, 'message' => 'User tidak ditemukan.'];
    }

    $email = trim($data['email'] ?? $user['email']);
    if ($email !== $user['email'] && findUserByEmail($email)) {
        return ['success' => false, 'message' => 'Email sudah digunakan oleh akun lain.'];
    }

    $stmt = getDb()->prepare('UPDATE users SET name = :name, email = :email, role = :role' . (!empty($data['password']) ? ', password = :password' : '') . ' WHERE id = :id');
    $params = [
        ':name' => trim($data['name'] ?? $user['name']),
        ':email' => $email,
        ':role' => $data['role'] ?? $user['role'],
        ':id' => $id,
    ];
    if (!empty($data['password'])) {
        $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    $stmt->execute($params);

    return ['success' => true];
}

function deleteUserById(int $id): bool
{
    $stmt = getDb()->prepare('DELETE FROM users WHERE id = :id');
    return $stmt->execute([':id' => $id]);
}

function isAdmin(): bool
{
    return !empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function isStaff(): bool
{
    return !empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'staff';
}

function canApproveRentals(): bool
{
    return isAdmin() || isStaff();
}

function loginUser(string $email, string $password): array
{
    $user = findUserByEmail($email);
    if (!$user) {
        return ['success' => false, 'message' => 'Email tidak ditemukan.'];
    }

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Password salah.'];
    }

    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    return ['success' => true, 'user' => $_SESSION['user']];
}

function logoutUser(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

function requireAuth(): void
{
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function redirectIfAuthenticated(): void
{
    if (!empty($_SESSION['user'])) {
        header('Location: dashboard.php');
        exit;
    }
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}
