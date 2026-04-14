<?php
require_once '../routes/auth.php';
requireLogin();
require_once '../config/database.php';

// Ambil data alat dari database
$query = "SELECT * FROM alat ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat Lukis - OlivCart</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- CSS OlivCart -->
    <link rel="stylesheet" href="../assets/resources/css/olvart.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark olvart-navbar">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">🎨 OlivCart</a>
            <div class="ms-auto">
                <a href="../routes/logout.php" class="btn btn-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="container text-center mt-5">
        <h2 class="fw-bold text-wine">Katalog Alat Lukis</h2>
        <p class="text-muted">Pilih peralatan terbaik untuk mewujudkan kreativitas Anda.</p>
    </div>

    <!-- Katalog -->
    <div class="container my-5">
        <div class="row g-4">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card katalog-card h-100">
                            <img src="../assets/img/<?= $row['gambar']; ?>" 
                                 class="card-img-top katalog-img" 
                                 alt="<?= $row['nama_alat']; ?>">

                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $row['nama_alat']; ?></h5>
                                <p class="text-muted small"><?= $row['deskripsi']; ?></p>
                                <p class="harga">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                                <p class="stok">Stok: <?= $row['stok']; ?></p>
                            </div>

                            <div class="card-footer bg-white border-0 text-center">
                                <a href="sewa.php?id=<?= $row['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-cart"></i> Sewa
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Belum ada data alat lukis.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 olvart-footer">
        <p>&copy; 2026 OlivCart. Semua Hak Dilindungi.</p>
    </footer>
</body>
</html>