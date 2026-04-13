<?php

function getDb(): PDO
{
    static $db = null;

    if ($db !== null) {
        return $db;
    }

    $databasePath = __DIR__ . '/../storage/olivcart.sqlite';
    if (!is_dir(dirname($databasePath))) {
        mkdir(dirname($databasePath), 0755, true);
    }

    $db = new PDO('sqlite:' . $databasePath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA foreign_keys = ON');

    initializeDatabase($db);
    return $db;
}

function initializeDatabase(PDO $db): void
{
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT NOT NULL,
        created_at TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE,
        created_at TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS tools (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE,
        description TEXT NOT NULL,
        stock INTEGER NOT NULL DEFAULT 0,
        price REAL NOT NULL DEFAULT 0,
        image TEXT,
        created_at TEXT NOT NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS rentals (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        tool_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL,
        start_date TEXT NOT NULL,
        end_date TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'pending',
        rejection_reason TEXT,
        approved_by TEXT,
        approved_at TEXT,
        returned_at TEXT,
        late_days INTEGER NOT NULL DEFAULT 0,
        late_fee REAL NOT NULL DEFAULT 0,
        payment_status TEXT NOT NULL DEFAULT 'unpaid',
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(tool_id) REFERENCES tools(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS payments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        payment_code TEXT NOT NULL UNIQUE,
        order_id TEXT,
        rental_ids TEXT NOT NULL,
        amount REAL NOT NULL,
        method TEXT NOT NULL,
        status TEXT NOT NULL,
        notes TEXT,
        gateway_response TEXT,
        paid_at TEXT,
        verified_by TEXT,
        verified_at TEXT,
        created_at TEXT NOT NULL,
        updated_at TEXT NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $defaultUserCount = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($defaultUserCount === 0) {
        $stmt = $db->prepare('INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, :role, :created_at)');
        $stmt->execute([
            ':name' => 'Admin Olvart',
            ':email' => 'admin@olvart.test',
            ':password' => password_hash('admin123', PASSWORD_DEFAULT),
            ':role' => 'admin',
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
        $stmt->execute([
            ':name' => 'User Demo',
            ':email' => 'user@olvart.test',
            ':password' => password_hash('user123', PASSWORD_DEFAULT),
            ':role' => 'user',
            ':created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
