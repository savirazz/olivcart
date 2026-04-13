<?php
require_once __DIR__ . '/auth.php';
requireAuth();
$user = currentUser();
if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$message = '';
$users = getUsers();
$editUser = null;
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (isset($_GET['success'])) {
    $successType = $_GET['success'];
    if ($successType === 'created') {
        $message = 'User berhasil ditambahkan.';
    } elseif ($successType === 'updated') {
        $message = 'User berhasil diperbarui.';
    } elseif ($successType === 'deleted') {
        $message = 'User berhasil dihapus.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $targetId = isset($_POST['id']) ? intval($_POST['id']) : null;

    if ($postAction === 'create') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            $error = 'Semua field harus diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif ($password !== $confirm) {
            $error = 'Password dan konfirmasi password tidak cocok.';
        } else {
            $result = registerUser($name, $email, $password, $role);
            if ($result['success']) {
                header('Location: user_management.php?success=created');
                exit;
            }
            $error = $result['message'];
        }
    }

    if ($postAction === 'update' && $targetId !== null) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '') {
            $error = 'Nama dan email harus diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } elseif ($password !== '' && strlen($password) < 6) {
            $error = 'Password minimal 6 karakter jika ingin mengubah password.';
        } elseif ($password !== $confirm) {
            $error = 'Password dan konfirmasi password tidak cocok.';
        } else {
            $result = updateUser($targetId, [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'password' => $password,
            ]);
            if ($result['success']) {
                header('Location: user_management.php?success=updated');
                exit;
            }
            $error = $result['message'];
        }
    }

    if ($postAction === 'delete' && $targetId !== null) {
        if ($targetId === $user['id']) {
            $error = 'Tidak dapat menghapus akun sendiri.';
        } elseif (deleteUserById($targetId)) {
            header('Location: user_management.php?success=deleted');
            exit;
        } else {
            $error = 'User tidak ditemukan.';
        }
    }

    $users = getUsers();
}

if ($action === 'edit' && $id !== null) {
    $editUser = findUserById($id);
    if (!$editUser) {
        $error = 'User tidak ditemukan.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen User - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container">
<div class="card">
<h1 class="title">Manajemen Data User</h1>
<p>Kelola akun user aplikasi Olvart. Hanya admin yang dapat mengakses halaman ini.</p>
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
</div>
<div class="card">
<h2 class="title"><?= $editUser ? 'Edit User' : 'Tambah User Baru' ?></h2>
<form method="POST" action="user_management.php<?= $editUser ? '?action=edit&id=' . intval($editUser['id']) : '' ?>">
<input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">
<?php if ($editUser): ?>
<input type="hidden" name="id" value="<?= intval($editUser['id']) ?>">
<?php endif; ?>
<div class="form-grid">
<div>
<span>Nama Lengkap</span>
<input type="text" name="name" value="<?= htmlspecialchars($editUser['name'] ?? '') ?>" required>
</div>
<div>
<span>Email</span>
<input type="email" name="email" value="<?= htmlspecialchars($editUser['email'] ?? '') ?>" required>
</div>
<div>
<span>Role</span>
<select name="role">
<option value="user" <?= (!isset($editUser['role']) || $editUser['role'] === 'user') ? 'selected' : '' ?>>User</option>
<option value="admin" <?= isset($editUser['role']) && $editUser['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
</select>
</div>
<div>
<span>Password <?= $editUser ? '(kosongkan jika tidak ingin diubah)' : '' ?></span>
<input type="password" name="password" <?= $editUser ? '' : 'required' ?> >
</div>
<div>
<span>Konfirmasi Password <?= $editUser ? '(kosongkan jika tidak ingin diubah)' : '' ?></span>
<input type="password" name="confirm_password" <?= $editUser ? '' : 'required' ?> >
</div>
</div>
<div class="submit-area">
<button type="submit" class="btn btn-primary"><?= $editUser ? 'Perbarui User' : 'Buat User Baru' ?></button>
<?php if ($editUser): ?>
<a href="user_management.php" class="btn btn-secondary">Batal</a>
<?php endif; ?>
</div>
</form>
</div>
<div class="card">
<h2 class="title">Daftar User</h2>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Nama</th>
<th>Email</th>
<th>Role</th>
<th>Dibuat</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($users as $item): ?>
<tr>
<td><?= intval($item['id']) ?></td>
<td><?= htmlspecialchars($item['name']) ?></td>
<td><?= htmlspecialchars($item['email']) ?></td>
<td><?= htmlspecialchars($item['role']) ?></td>
<td><?= htmlspecialchars($item['created_at']) ?></td>
<td>
<a href="user_management.php?action=edit&id=<?= intval($item['id']) ?>" class="btn btn-secondary">Edit</a>
<?php if ($item['id'] !== $user['id']): ?>
<form method="POST" action="user_management.php" style="display:inline-block;margin-left:8px;" onsubmit="return confirm('Hapus user ini?');">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= intval($item['id']) ?>">
<button type="submit" class="btn btn-danger">Hapus</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>
</div>
</body>
</html>
