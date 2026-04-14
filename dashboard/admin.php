<?php
require_once '../routes/auth.php';
requireRole('admin');
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - OlivCart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #8B1E3F;
            --secondary: #D9A5B3;
            --accent: #F7E6E0;
            --background: #FFF8F5;
            --white: #ffffff;
            --text: #4A2C2A;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background);
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary), #5e1229);
            color: white;
            position: fixed;
            padding: 20px;
        }

        .sidebar h3 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            margin: 8px 0;
            text-decoration: none;
            border-radius: 10px;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }

        /* Header */
        .topbar {
            background: var(--white);
            padding: 15px 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome {
            font-weight: 600;
            color: var(--primary);
        }

        /* Cards */
        .card-dashboard {
            background: var(--white);
            border: none;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
        }

        .card-dashboard h4 {
            color: var(--primary);
            font-weight: bold;
        }

        .icon-box {
            font-size: 40px;
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
    <a href="#">🏠 Dashboard</a>
    <a href="../routes/katalog.php">🖌️ Data Alat</a>
    <a href="#">📦 Transaksi</a>
    <a href="#">💳 Pembayaran</a>
    <a href="#">📊 Laporan</a>
    <a href="../routes/logout.php">🚪 Logout</a>
</div>

<!-- Main Content -->
<div class="main-content">
    
    <!-- Topbar -->
    <div class="topbar mb-4">
        <h5 class="welcome">Selamat Datang, <?= htmlspecialchars($user['nama']); ?> 👋</h5>
        <span class="badge bg-danger">Admin</span>
    </div>

    <!-- Statistik -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card-dashboard text-center">
                <div class="icon-box">🖌️</div>
                <h4>25</h4>
                <p>Total Alat</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-dashboard text-center">
                <div class="icon-box">📦</div>
                <h4>12</h4>
                <p>Transaksi</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-dashboard text-center">
                <div class="icon-box">👥</div>
                <h4>8</h4>
                <p>Pengguna</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-dashboard text-center">
                <div class="icon-box">💰</div>
                <h4>Rp 1.500K</h4>
                <p>Pendapatan</p>
            </div>
        </div>
    </div>

    <!-- Informasi -->
    <div class="card-dashboard mt-4">
        <h5>Selamat Datang di OlivCart 🎨</h5>
        <p>
            Sistem penyewaan alat lukis yang memudahkan Anda dalam mengelola
            transaksi, pembayaran, dan data pengguna dengan tampilan modern dan elegan.
        </p>
    </div>

</div>

</body>
</html>