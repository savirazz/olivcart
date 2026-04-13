<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/tool_helper.php';
require_once __DIR__ . '/rental_helper.php';

requireAuth();
$user = currentUser();

if (!canApproveRentals()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$message = '';
$rentals = getRentals();
$status = $_GET['status'] ?? '';

if ($status && in_array($status, ['pending', 'approved', 'rejected', 'returned'])) {
    $rentals = array_filter($rentals, function($r) use ($status) {
        return $r['status'] === $status;
    });
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'approved') {
        $message = '✓ Peminjaman telah disetujui.';
    } elseif ($_GET['success'] === 'rejected') {
        $message = '✓ Peminjaman telah ditolak.';
    } elseif ($_GET['success'] === 'returned') {
        $message = '✓ Alat telah dikembalikan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postAction = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if ($postAction === 'approve' && $id !== null) {
        $result = updateRentalStatus($id, 'approved', null, $user['name']);
        if ($result['success']) {
            header('Location: kelola_peminjaman.php?success=approved');
            exit;
        }
        $error = $result['message'];
    }

    if ($postAction === 'reject' && $id !== null) {
        $reason = isset($_POST['rejection_reason']) ? trim($_POST['rejection_reason']) : '';
        if (!$reason) {
            $error = 'Alasan penolakan harus diisi.';
        } else {
            $result = updateRentalStatus($id, 'rejected', $reason, $user['name']);
            if ($result['success']) {
                header('Location: kelola_peminjaman.php?success=rejected');
                exit;
            }
            $error = $result['message'];
        }
    }

    if ($postAction === 'return' && $id !== null) {
        $result = updateRentalStatus($id, 'returned', null, $user['name']);
        if ($result['success']) {
            header('Location: kelola_peminjaman.php?success=returned');
            exit;
        }
        $error = $result['message'];
    }

    $rentals = getRentals();
    if ($status && in_array($status, ['pending', 'approved', 'rejected', 'returned'])) {
        $rentals = array_filter($rentals, function($r) use ($status) {
            return $r['status'] === $status;
        });
    }
}

// Sort rentals by created_at (newest first)
usort($rentals, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
?>
<!DOCTYPE html>
<html>
<head>
<title>Kelola Peminjaman - Olvart Admin</title>
<link rel="stylesheet" href="../assets/resources/css/olvart.css">
</head>
<body>
<div class="navbar">
<div class="logo">Olvart Admin</div>
<div class="nav-links">
<a href="dashboard.php">Dashboard</a>
<a href="user_management.php">User</a>
<a href="alat_management.php">Alat</a>
<a href="logout.php">Logout</a>
</div>
</div>

<div class="container">
<h1 class="title">Kelola Peminjaman</h1>
<p class="subtitle">Petugas: <strong><?= htmlspecialchars($user['name']) ?></strong></p>

<?php if ($message): ?>
<div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card">
<div class="filters">
<a href="kelola_peminjaman.php" class="filter-btn <?= !$status ? 'active' : '' ?>">Semua (<?= count(getRentals()) ?>)</a>
<button onclick="location.href='kelola_peminjaman.php?status=pending'" class="filter-btn <?= $status === 'pending' ? 'active' : '' ?>">Menunggu (<?= count(array_filter(getRentals(), function($r) { return $r['status'] === 'pending'; })) ?>)</button>
<button onclick="location.href='kelola_peminjaman.php?status=approved'" class="filter-btn <?= $status === 'approved' ? 'active' : '' ?>">Disetujui (<?= count(array_filter(getRentals(), function($r) { return $r['status'] === 'approved'; })) ?>)</button>
<button onclick="location.href='kelola_peminjaman.php?status=rejected'" class="filter-btn <?= $status === 'rejected' ? 'active' : '' ?>">Ditolak (<?= count(array_filter(getRentals(), function($r) { return $r['status'] === 'rejected'; })) ?>)</button>
<button onclick="location.href='kelola_peminjaman.php?status=returned'" class="filter-btn <?= $status === 'returned' ? 'active' : '' ?>">Dikembalikan (<?= count(array_filter(getRentals(), function($r) { return $r['status'] === 'returned'; })) ?>)</button>
</div>

<?php if (count($rentals) > 0): ?>
<div class="table-wrap">
<table>
<thead>
<tr>
<th>ID</th>
<th>User</th>
<th>Alat</th>
<th>Qty</th>
<th>Mulai</th>
<th>Selesai</th>
<th>Diajukan</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php foreach ($rentals as $rental): 
    $rentalUser = findUserById($rental['user_id']);
    $tool = findToolById($rental['tool_id']);
?>
<tr>
<td><?= intval($rental['id']) ?></td>
<td><?= htmlspecialchars($rentalUser['name'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($tool['name'] ?? 'N/A') ?></td>
<td><?= intval($rental['quantity']) ?></td>
<td><?= htmlspecialchars($rental['start_date']) ?></td>
<td><?= htmlspecialchars($rental['end_date']) ?></td>
<td><?= htmlspecialchars(date('d M Y', strtotime($rental['created_at']))) ?></td>
<td>
<span class="status-badge" style="background-color:<?= getStatusColor($rental['status']) ?>;">
<?= htmlspecialchars(getStatusLabel($rental['status'])) ?>
</span>
</td>
<td>
<?php if ($rental['status'] === 'pending'): ?>
<form method="POST" action="kelola_peminjaman.php" class="form-inline">
<input type="hidden" name="action" value="approve">
<input type="hidden" name="id" value="<?= intval($rental['id']) ?>">
<button type="submit" class="btn btn-approve">Setujui</button>
</form>
<button type="button" class="btn btn-reject" onclick="openRejectModal(<?= intval($rental['id']) ?>)">Tolak</button>
<?php elseif ($rental['status'] === 'approved'): ?>
<form method="POST" action="kelola_peminjaman.php" class="form-inline">
<input type="hidden" name="action" value="return">
<input type="hidden" name="id" value="<?= intval($rental['id']) ?>">
<button type="submit" class="btn btn-return">Dikembalikan</button>
</form>
<?php else: ?>
<span style="color:#999;">-</span>
<?php endif; ?>
</td>
</tr>
<?php 
    if ($rental['status'] === 'rejected' && !empty($rental['rejection_reason'])) {
?><tr>
<td colspan="9">
<div class="row-notes">
<div class="notes-label">❌ Alasan Penolakan:</div>
<div class="notes-text"><?= htmlspecialchars($rental['rejection_reason']); ?></div>
<?php if (!empty($rental['approved_by'])) { ?>
<div class="approver-info">Ditolak oleh: <?= htmlspecialchars($rental['approved_by']); ?> pada <?= htmlspecialchars(date('d M Y H:i', strtotime($rental['approved_at'] ?? $rental['updated_at']))); ?></div>
<?php } ?>
</div>
</td>
</tr>
<?php } ?>
<?php 
    if ($rental['status'] === 'approved' && !empty($rental['approved_by'])) {
?><tr>
<td colspan="9">
<div class="row-notes" style="border-left-color:#27ae60;">
<div class="approver-info">✓ Disetujui oleh: <?= htmlspecialchars($rental['approved_by']); ?> pada <?= htmlspecialchars(date('d M Y H:i', strtotime($rental['approved_at'] ?? $rental['updated_at']))); ?></div>
</div>
</td>
</tr>
<?php } ?>
<?php endforeach; ?>
</tbody>
</table>
</div>
<?php else: ?>
<div class="empty">Tidak ada peminjaman untuk ditampilkan.</div>
<?php endif; ?>
</div>
</div>

<!-- Modal untuk penolakan -->
<div id="rejectModal" class="modal">
<div class="modal-content">
<h2 class="modal-title">Tolak Peminjaman</h2>
<p style="color:#666;margin:15px 0;">Silakan berikan alasan penolakan peminjaman ini.</p>
<form id="rejectForm" method="POST" action="kelola_peminjaman.php">
<input type="hidden" name="action" value="reject">
<input type="hidden" name="id" id="rejectRentalId" value="">
<textarea name="rejection_reason" class="modal-textarea" placeholder="Contoh: Alat sedang dalam perbaikan, atau Jumlah permintaan melebihi ketersediaan stok..." required></textarea>
<div class="modal-buttons">
<button type="button" class="btn btn-cancel" onclick="closeRejectModal()">Batal</button>
<button type="submit" class="btn btn-reject-modal">Tolak Peminjaman</button>
</div>
</form>
</div>
</div>

<script>
function openRejectModal(rentalId) {
    document.getElementById('rejectRentalId').value = rentalId;
    document.getElementById('rejectModal').style.display = 'block';
    document.querySelector('.modal-textarea').value = '';
    document.querySelector('.modal-textarea').focus();
}

function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    var modal = document.getElementById('rejectModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Form validation
document.getElementById('rejectForm').onsubmit = function(e) {
    var reason = document.querySelector('.modal-textarea').value.trim();
    if (!reason) {
        e.preventDefault();
        alert('Alasan penolakan harus diisi!');
        return false;
    }
}
</script>

</body>
</html>
