<?php
// DATA DUMMY (nanti bisa dari database)

$paket = [
["nama"=>"Paket J","img"=>"https://picsum.photos/400/300?1"],
["nama"=>"Paket A","img"=>"https://picsum.photos/400/300?2"],
["nama"=>"Paket E","img"=>"https://picsum.photos/400/300?3"],
["nama"=>"Paket Y","img"=>"https://picsum.photos/400/300?4"],
["nama"=>"Paket U","img"=>"https://picsum.photos/400/300?5"],
["nama"=>"Paket K","img"=>"https://picsum.photos/400/300?6"]
];
?>

<!DOCTYPE html>
<html>
<head>
<title>Olvart Dashboard</title>

<style>

body{
margin:0;
font-family:Arial;
background:#e8e5cf;
}

/* NAVBAR */

.navbar{
display:flex;
justify-content:space-between;
align-items:center;
padding:15px 40px;
background:#dcd8bf;
}

.logo{
font-weight:bold;
color:#7a0000;
font-size:22px;
}

.logout{
background:#7a0000;
color:white;
padding:8px 20px;
border-radius:20px;
text-decoration:none;
}

/* HERO */

.hero{
width:90%;
margin:30px auto;
height:260px;
border-radius:20px;
background:linear-gradient(rgba(122,0,0,0.8),rgba(0,0,0,0.4)),
url('https://images.unsplash.com/photo-1513364776144-60967b0f800f');
background-size:cover;
color:white;
display:flex;
flex-direction:column;
justify-content:center;
padding:40px;
}

.hero h1{
font-size:38px;
width:45%;
}

/* SECTION */

.section{
width:90%;
margin:auto;
}

.title{
color:#7a0000;
margin-top:20px;
}

/* GRID CARD */

.grid{
display:grid;
grid-template-columns:repeat(3,1fr);
gap:30px;
margin-top:20px;
}

.card{
background:white;
border-radius:15px;
overflow:hidden;
box-shadow:0 5px 10px rgba(0,0,0,0.1);
}

.card img{
width:100%;
height:200px;
object-fit:cover;
}

.card-body{
padding:20px;
}

.card h3{
color:#7a0000;
}

.btn{
margin-top:15px;
background:#efe5c7;
padding:10px;
border-radius:10px;
text-align:center;
cursor:pointer;
}

/* TABLE */

.table-box{
background:white;
margin-top:30px;
padding:20px;
border-radius:15px;
}

table{
width:100%;
border-collapse:collapse;
}

td,th{
padding:12px;
border-bottom:1px solid #ddd;
}

.status{
padding:5px 10px;
border-radius:10px;
font-size:12px;
}

.pinjam{
background:#cfe0ff;
}

.kembali{
background:#cdeccd;
}

/* FOOTER */

footer{
margin-top:40px;
text-align:center;
padding:20px;
background:#ddd;
}

</style>
</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

<div class="logo">Olvart</div>

<div>
Seniman Muda
<a href="#" class="logout">Keluar</a>
</div>

</div>


<!-- HERO -->

<div class="hero">

<h1>Wujudkan Karya Senimu Bersama Olvart</h1>

<p>Peminjaman alat lukis premium dengan paket lengkap.</p>

</div>


<!-- KATALOG -->

<div class="section">

<h2 class="title">Pilih Paket Lukismu</h2>

<div class="grid">

<?php foreach($paket as $p){ ?>

<div class="card">

<img src="<?= $p['img']; ?>">

<div class="card-body">

<h3><?= $p['nama']; ?></h3>

<ul>
<li>Set Kuas Profesional</li>
<li>Palet Lukis</li>
<li>Kanvas Premium</li>
<li>Set Cat (Gratis)</li>
</ul>

<div class="btn">Pinjam Sekarang</div>

</div>

</div>

<?php } ?>

</div>


<!-- STATUS -->

<h2 class="title">Status Peminjaman</h2>

<div class="table-box">

<table>

<tr>
<th>Paket</th>
<th>Tanggal</th>
<th>Status</th>
<th>Aksi</th>
</tr>

<tr>
<td>Paket E</td>
<td>2026-02-10</td>
<td><span class="status pinjam">Dipinjam</span></td>
<td style="color:red;">Kembalikan</td>
</tr>

<tr>
<td>Paket J</td>
<td>2026-01-25</td>
<td><span class="status kembali">Dikembalikan</span></td>
<td>-</td>
</tr>

</table>

</div>

</div>


<footer>
© 2026 Olvart - Painting Tool Rental System
</footer>

</body>
</html>
