<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/category_helper.php';
requireAuth();
$user = currentUser();
if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$message = '';
$categories = getCategories();
$editCategory = null;
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (isset($_GET['success'])) {
    $successType = $_GET['success'];
    if ($successType === 'created') {
        $message = 'Kategori berhasil dibuat.';
    } elseif ($successType === 'updated') {
        $message = 'Kategori berhasil diperbarui.';
    } elseif ($successType === 'deleted') {
        $message = 'Kategori berhasil dihapus.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $targetId = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = trim($_POST['name'] ?? '');

    if ($postAction === 'create') {
        $result = createCategory($name);
        if ($result['success']) {
            header('Location: kategori_management.php?success=created');
            exit;
        }
        $error = $result['message'];
    }

    if ($postAction === 'update' && $targetId !== null) {
        $result = updateCategory($targetId, $name);
        if ($result['success']) {
            header('Location: kategori_management.php?success=updated');
            exit;
        }
        $error = $result['message'];
    }

    if ($postAction === 'delete' && $targetId !== null) {
        if (deleteCategory($targetId)) {
            header('Location: kategori_management.php?success=deleted');
            exit;
        }
        $error = 'Kategori tidak ditemukan.';
    }

    $categories = getCategories();
}

if ($action === 'edit' && $id !== null) {
    $editCategory = findCategoryById($id);
    if (!$editCategory) {
        $error = 'Kategori tidak ditemukan.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen Kategori - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container">
<div class="card">
<h1 class="title">Manajemen Kategori</h1>
<p>Tambahkan, edit, dan hapus kategori produk atau layanan Olvart.</p>
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
</div>
<div class="card">
<h2 class="title"><?= $editCategory ? 'Edit Kategori' : 'Tambah Kategori Baru' ?></h2>
<form method="POST" action="kategori_management.php<?= $editCategory ? '?action=edit&id=' . intval($editCategory['id']) : '' ?>">
<input type="hidden" name="action" value="<?= $editCategory ? 'update' : 'create' ?>">
<?php if ($editCategory): ?>
<input type="hidden" name="id" value="<?= intval($editCategory['id']) ?>">
<?php endif; ?>
<div class="form-grid">
<div>
<span>Nama Kategori</span>
<input type="text" name="name" value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>" required>
</div>
</div>
<div class="submit-area">
<button type="submit" class="btn btn-primary"><?= $editCategory ? 'Perbarui Kategori' : 'Buat Kategori' ?></button>
<?php if ($editCategory): ?>
<a href="kategori_management.php" class="btn btn-secondary">Batal</a>
<?php endif; ?>
</div>
</form>
</div>
<div class="card">
<h2 class="title">Daftar Kategori</h2>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Nama Kategori</th>
<th>Dibuat</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($categories as $category): ?>
<tr>
<td><?= intval($category['id']) ?></td>
<td><?= htmlspecialchars($category['name']) ?></td>
<td><?= htmlspecialchars($category['created_at']) ?></td>
<td>
<a href="kategori_management.php?action=edit&id=<?= intval($category['id']) ?>" class="btn btn-secondary">Edit</a>
<form method="POST" action="kategori_management.php" style="display:inline-block;margin-left:8px;" onsubmit="return confirm('Hapus kategori ini?');">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= intval($category['id']) ?>">
<button type="submit" class="btn btn-danger">Hapus</button>
</form>
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
