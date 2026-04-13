<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
?>
<header class="site-header">
    <div class="header-content">
        <div class="logo">
            <a href="index.php">Olvart</a>
        </div>
        <?php if ($user): ?>
            <nav class="main-nav">
                <?php if ($user['role'] === 'user'): ?>
                    <!-- Menu untuk pengguna -->
                    <a href="katalog.php">Katalog</a>
                    <a href="keranjang.php">Keranjang</a>
                    <a href="peminjaman.php">Peminjaman Saya</a>
                <?php elseif ($user['role'] === 'admin' || $user['role'] === 'petugas'): ?>
                    <!-- Menu untuk admin dan petugas -->
                    <a href="denda.php">Denda</a>
                    <a href="alat_management.php">Alat</a>
                    <a href="kategori_management.php">Kategori</a>
                    <a href="user_management.php">Manajemen User</a>
                    <a href="kelola_peminjaman.php">Kelola Peminjaman</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Cari alat..." required>
                    <button type="submit">Cari</button>
                </form>
                <a href="profil.php" class="btn-profile">Profil</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        <?php else: ?>
            <nav class="main-nav">
                <a href="katalog.php">Katalog</a>
            </nav>
            <div class="header-actions">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
        <?php endif; ?>
    </div>
</header>