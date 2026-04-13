<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
requireAuth();
$user = currentUser();
if ($user['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$message = '';
$tools = getTools();
$editTool = null;
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (isset($_GET['success'])) {
    $successType = $_GET['success'];
    if ($successType === 'created') {
        $message = 'Alat berhasil dibuat.';
    } elseif ($successType === 'updated') {
        $message = 'Alat berhasil diperbarui.';
    } elseif ($successType === 'deleted') {
        $message = 'Alat berhasil dihapus.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $targetId = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $image = '';

    if ($postAction === 'create') {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = uploadToolImage($_FILES['image']) ?? '';
        }
        $result = createTool($name, $description, $stock, $price, $image);
        if ($result['success']) {
            header('Location: alat_management.php?success=created');
            exit;
        }
        $error = $result['message'];
    }

    if ($postAction === 'update' && $targetId !== null) {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = uploadToolImage($_FILES['image']) ?? '';
        }
        $result = updateTool($targetId, $name, $description, $stock, $price, $image);
        if ($result['success']) {
            header('Location: alat_management.php?success=updated');
            exit;
        }
        $error = $result['message'];
    }

    if ($postAction === 'delete' && $targetId !== null) {
        if (deleteTool($targetId)) {
            header('Location: alat_management.php?success=deleted');
            exit;
        }
        $error = 'Alat tidak ditemukan.';
    }

    $tools = getTools();
}

if ($action === 'edit' && $id !== null) {
    $editTool = findToolById($id);
    if (!$editTool) {
        $error = 'Alat tidak ditemukan.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Manajemen Alat - Olvart</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container">
<div class="card">
<h1 class="title">Manajemen Alat</h1>
<p>Tambah, edit, dan hapus alat pada aplikasi Olvart.</p>
<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
</div>
<div class="card">
<h2 class="title"><?= $editTool ? 'Edit Alat' : 'Tambah Alat Baru' ?></h2>
<form method="POST" action="alat_management.php<?= $editTool ? '?action=edit&id=' . intval($editTool['id']) : '' ?>" enctype="multipart/form-data">
<input type="hidden" name="action" value="<?= $editTool ? 'update' : 'create' ?>">
<?php if ($editTool): ?>
<input type="hidden" name="id" value="<?= intval($editTool['id']) ?>">
<?php endif; ?>
<div class="form-grid">
<div>
<span>Nama Alat</span>
<input type="text" name="name" value="<?= htmlspecialchars($editTool['name'] ?? '') ?>" required>
</div>
<div>
<span>Stok</span>
<input type="number" name="stock" min="0" value="<?= htmlspecialchars($editTool['stock'] ?? '0') ?>" required>
</div>
<div>
<span>Harga</span>
<input type="number" name="price" min="0" step="0.01" value="<?= htmlspecialchars($editTool['price'] ?? '0.00') ?>" required>
</div>
<div>
<span>Gambar</span>
<input type="file" name="image" accept="image/jpeg,image/png,image/gif" >
<?php if ($editTool && !empty($editTool['image'])): ?>
<p style="font-size:12px;color:#666;">Gambar saat ini: <?= htmlspecialchars($editTool['image']) ?></p>
<?php endif; ?>
</div>
<div style="grid-column:1/-1;">
<span>Deskripsi</span>
<textarea name="description" required><?= htmlspecialchars($editTool['description'] ?? '') ?></textarea>
</div>
</div>
<div class="submit-area">
<button type="submit" class="btn btn-primary"><?= $editTool ? 'Perbarui Alat' : 'Buat Alat' ?></button>
<?php if ($editTool): ?>
<a href="alat_management.php" class="btn btn-secondary">Batal</a>
<?php endif; ?>
</div>
</form>
</div>
<div class="card">
<h2 class="title">Daftar Alat</h2>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>Nama</th>
<th>Stok</th>
<th>Harga</th>
<th>Dibuat</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($tools as $tool): ?>
<tr>
<td><?= intval($tool['id']) ?></td>
<td><?= htmlspecialchars($tool['name']) ?></td>
<td><?= intval($tool['stock']) ?></td>
<td>Rp <?= number_format($tool['price'], 2, ',', '.') ?></td>
<td><?= htmlspecialchars($tool['created_at']) ?></td>
<td>
<a href="alat_management.php?action=edit&id=<?= intval($tool['id']) ?>" class="btn btn-secondary">Edit</a>
<form method="POST" action="alat_management.php" style="display:inline-block;margin-left:8px;" onsubmit="return confirm('Hapus alat ini?');">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= intval($tool['id']) ?>">
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
