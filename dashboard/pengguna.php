<?php
require_once '../routes/auth.php';
require_once '../config/database.php';

requireRole('pengguna');
$user = currentUser();

// Ambil data statistik dari database
$totalAlat = $conn->query("SELECT COUNT(*) FROM alat")->fetchColumn();
$totalTransaksi = $conn->prepare("SELECT COUNT(*) FROM transaksi WHERE user_id = ?");
$totalTransaksi->execute([$user['id']]);
$totalTransaksi = $totalTransaksi->fetchColumn();

$totalPembayaran = $conn->prepare("SELECT COUNT(*) FROM pembayaran 
    JOIN transaksi ON pembayaran.transaksi_id = transaksi.id 
    WHERE transaksi.user_id = ?");
$totalPembayaran->execute([$user['id']]);
$totalPembayaran = $totalPembayaran->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pengguna - OlivCart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #8B1E3F;
            --secondary: #F7E6E0;
            --bg: #FFF8F5;
            --text: #4A2C2A;
            --white: #ffffff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #8B1E3F, #5e1229);
            color: white;
            padding: 20px;
        }

        .sidebar h3 {
            text-align: center;
            font-weight: 700;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            margin-bottom: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Topbar */
        .topbar {
            background: var(--white);
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        /* Cards */
        .card-dashboard {
            background: var(--white);
            border: none;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .card-dashboard:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-dashboard i {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .badge-role {
            background: var(--primary);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h3>🎨 OlivCart</h3>
    <a href="#" class="active"><i class="bi bi-house-door"></i> Dashboard</a>
    <a href="../routes/katalog.php"><i class="bi bi-brush"></i> Katalog Alat</a>
    <a href="#"><i class="bi bi-clock-history"></i> Riwayat Penyewaan</a>
    <a href="#"><i class="bi bi-credit-card"></i> Pembayaran</a>
    <a href="../routes/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            Selamat Datang, <strong><?= htmlspecialchars($user['nama']); ?></strong> 👋
        </h5>
        <span class="badge-role">Pengguna</span>
    </div>

    <!-- Statistik -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card-dashboard">
                <i class="bi bi-brush"></i>
                <h4><?= $totalAlat; ?></h4>
                <p>Katalog Alat</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-dashboard">
                <i class="bi bi-box-seam"></i>
                <h4><?= $totalTransaksi; ?></h4>
                <p>Riwayat Sewa</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-dashboard">
                <i class="bi bi-credit-card"></i>
                <h4><?= $totalPembayaran; ?></h4>
                <p>Pembayaran</p>
            </div>
        </div>
    </div>

    <!-- Informasi -->
    <div class="card-dashboard mt-4 text-start">
        <h5>Selamat Datang di OlivCart 🎨</h5>
        <p>
            OlivCart adalah platform penyewaan alat lukis yang membantu Anda
            mewujudkan karya seni dengan mudah, cepat, dan aman. Jelajahi katalog,
            lakukan penyewaan, dan kelola transaksi Anda dengan nyaman.
        </p>
    </div>

</div>

</body>
</html>