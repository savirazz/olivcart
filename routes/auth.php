<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function requireLogin() {
    if (!currentUser()) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if (currentUser()['role'] !== $role) {
        die("Akses ditolak!");
    }
}

function redirectIfAuthenticated() {
    if (currentUser()) {
        header("Location: dashboard.php");
        exit;
    }
}
?>