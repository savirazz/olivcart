<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<?php
require_once __DIR__ . '/auth.php';
if (currentUser()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvart - Selamat Datang</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- CSS Olvart -->
    <link rel="stylesheet" href="../assets/resources/css/olvart.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="landing-page">
<header class="landing-header">
  <div class="logo">Olvart</div>
  <nav class="landing-nav">
    <a class="btn btn-secondary" href="register.php">Daftar</a>
    <a class="btn btn-primary" href="login.php">Masuk</a>
  </nav>
</header>
<section class="hero"> 
  <div class="hero-copy"> 
    <span class="eyebrow">Sewa Alat Lukis</span> 
    <h1>Wujudkan Karya Tanpa Batas</h1> 
    <p>Temukan alat lukis lengkap untuk setiap ide kreatif Anda. Olvart membantu Anda menyewa peralatan berkualitas dengan mudah dan aman.</p> 
    <div class="buttons"> 
      <a class="btn btn-primary" href="login.php">Masuk</a> 
      <a class="btn btn-secondary" href="register.php">Mulai Daftar</a> 
    </div> 
  </div> 
</section>
<section class="features">
  <h2 class="section-title text-center">Keunggulan Olvart</h2>
  <p class="section-subtitle text-center">
    Solusi terbaik untuk kebutuhan sewa alat lukis Anda.
  </p>

  <div class="features-container">
    <div class="feature-card">
      <div class="feature-icon"><i class="fas fa-palette"></i></div>
      <h3>Akses Cepat</h3>
      <p>Login dan kelola peminjaman dengan dashboard personal yang mudah digunakan.</p>
    </div>

    <div class="feature-card">
      <div <div class="feature-icon"><i class="fas fa-paint-brush"></i></div>
      <h3>Peralatan Premium</h3>
      <p>Pilih kuas, kanvas, dan media lukis terbaik untuk setiap proyek Anda.</p>
    </div>

    <div class="feature-card">
      <div class="feature-icon"><i class="fas fa-star"></i></div>
      <h3>Komunitas Kreatif</h3>
      <p>Jadilah bagian dari pengguna yang saling mendukung karya seni satu sama lain.</p>
    </div>
  </div>
</section>
<footer>
  <p>&copy; 2026 Olvart. Semua Hak Dilindungi.</p>
</footer>
</body>
</html>

<style>
/* Landing Page */
.landing-page {
  font-family: 'Poppins', sans-serif;
}

/* Header */
.landing-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 50px;
  background: #ffffff;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.landing-header .logo {
  font-size: 24px;
  font-weight: 700;
  color: var(--wine);
}

/* Hero Section */
.hero {
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 100px 20px;
  background: linear-gradient(135deg, #fff6f3, #fbeaea);
}

.hero-copy {
  max-width: 700px;
}

.hero h1 {
  font-size: 3rem;
  font-weight: 700;
  color: var(--wine-dark);
}

.hero p {
  font-size: 1.1rem;
  color: var(--muted);
  margin: 20px 0;
}

.eyebrow {
  display: inline-block;
  background: #ffe5e5;
  color: var(--wine);
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  margin-bottom: 15px;
}

/* Buttons */
.buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
}

/* Features */
.features {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
  padding: 60px 20px;
  background: #fff;
}

.feature-card {
  background: #fff4f1;
  border-radius: 20px;
  padding: 25px;
  width: 300px;
  text-align: center;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  transition: 0.3s;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-card h3 {
  color: var(--wine);
  margin-bottom: 10px;
}

/* Footer */
footer {
  text-align: center;
  padding: 20px;
  background: var(--wine);
  color: white;
  font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
  .landing-header {
    flex-direction: column;
    gap: 10px;
  }

  .hero h1 {
    font-size: 2rem;
  }

  .features {
    flex-direction: column;
    align-items: center;
  }
}

/* Sticky Header */
.landing-header {
  position: sticky;
  top: 0;
  z-index: 1000;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 50px;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease-in-out;
}

/* Logo */
.landing-header .logo {
  font-size: 24px;
  font-weight: 700;
  color: var(--wine);
}

/* Navigasi */
.landing-nav {
  display: flex;
  gap: 12px;
}

/* Efek Hover Tombol */
.landing-nav .btn {
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 600;
}

.landing-header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

/* Tambahkan jarak agar konten tidak tertutup header */
body {
  padding-top: 90px;
}

/* ===== FEATURES SECTION ===== */
.features {
  padding: 80px 20px;
  background: linear-gradient(180deg, #ffffff, #fdf1ec);
  text-align: center;
}

.section-title {
  font-size: 2.2rem;
  font-weight: 700;
  color: var(--wine);
  margin-bottom: 10px;
}

.section-subtitle {
  font-size: 1rem;
  color: var(--muted);
  margin-bottom: 40px;
}

.features-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 25px;
}

.feature-card {
  background: #fff;
  border-radius: 20px;
  padding: 30px 25px;
  width: 300px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease-in-out;
  border: 1px solid #f1d9d4;
}

.feature-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  font-size: 45px;
  margin-bottom: 15px;
}

.feature-card h3 {
  color: var(--wine-dark);
  font-size: 1.3rem;
  margin-bottom: 10px;
}

.feature-card p {
  font-size: 0.95rem;
  color: var(--muted);
  line-height: 1.6;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 992px) {
  .feature-card {
    width: 45%;
  }
}

@media (max-width: 576px) {
  .feature-card {
    width: 100%;
  }

  .section-title {
    font-size: 1.8rem;
  }
}