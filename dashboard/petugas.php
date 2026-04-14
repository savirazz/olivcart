<?php
require_once '../routes/auth.php';
requireRole('petugas');
$user = currentUser();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Petugas - OlivCart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; background: #fff8f5; }
        .sidebar {
            width: 250px; height: 100vh; position: fixed;
            background: linear-gradient(180deg, #8B1E3F, #5e1229);
            color: white; padding: 20px;
        }
        .sidebar h3 { text-align: center; margin-bottom: 30px; }
        .sidebar a {
            display: block; color: white; padding: 12px;
            border-radius: 10px; text-decoration: none;
            margin-bottom: 10px;
        }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .main-content { margin-left: 260px; padding: 30px; }
        .card-dashboard {
            background: white; padding: 20px;
            border-radius: 15px; box-shadow: 0 6px 20px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3>🎨 OlivCart</h3>
    <a href="#">🏠 Dashboard</a>
    <a href="#">📦 Transaksi</a>
    <a href="#">💳 Verifikasi Pembayaran</a>
    <a href="#">⚠️ Denda</a>
    <a href="../routes/logout.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between mb-4">
        <h5>Selamat Datang, <?= htmlspecialchars($user['nama']); ?> 👋</h5>
        <span class="badge bg-warning text-dark">Petugas</span>
    </div>

    <div class="row g-4">
        <div class="col-md-4"><div class="card-dashboard text-center"><h3>📦</h3><h5>Kelola Transaksi</h5></div></div>
        <div class="col-md-4"><div class="card-dashboard text-center"><h3>💳</h3><h5>Verifikasi Pembayaran</h5></div></div>
        <div class="col-md-4"><div class="card-dashboard text-center"><h3>⚠️</h3><h5>Kelola Denda</h5></div></div>
    </div>
</div>

</body>
</html>