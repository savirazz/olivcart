<!DOCTYPE html>
<html>
<head>
<title>Dashboard Olvart</title>

<style>

body{
margin:0;
font-family:Arial;
background:#e7e4cf;
}

/* NAVBAR */

.navbar{
background:#d9d6bd;
display:flex;
justify-content:space-between;
align-items:center;
padding:15px 50px;
}

.logo{
font-weight:bold;
color:#7a0000;
font-size:20px;
}

.right-nav{
display:flex;
align-items:center;
gap:20px;
}

.logout{
background:#7a0000;
color:white;
padding:10px 20px;
border-radius:20px;
text-decoration:none;
}

/* HERO */

.hero{
margin:30px auto;
width:90%;
height:250px;
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
font-size:40px;
width:40%;
}

/* TITLE */

.section{
width:90%;
margin:auto;
}

.title{
color:#7a0000;
font-size:30px;
margin-top:30px;
}

/* CARD GRID */

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
height:180px;
object-fit:cover;
}

.card-body{
padding:20px;
}

.card h3{
color:#7a0000;
}

.btn{
background:#efe6c5;
padding:10px;
border-radius:10px;
text-align:center;
margin-top:15px;
cursor:pointer;
}

/* TABLE */

.table-box{
background:white;
border-radius:15px;
padding:20px;
margin-top:30px;
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

footer{
margin-top:40px;
padding:20px;
text-align:center;
background:#ddd;
}

</style>
</head>

<body>

<!-- NAVBAR -->

<div class="navbar">

<div class="logo">Olvart</div>

<div class="right-nav">
<span>Seniman Muda</span>
<a href="#" class="logout">Keluar</a>
</div>

</div>


<!-- HERO -->

<div class="hero">
<h1>Wujudkan Karya Senimu Bersama Olvart</h1>
<p>Peminjaman alat lukis premium dengan paket lengkap.</p>
</div>


<div class="section">

<h2 class="title">Pilih Paket Lukismu</h2>

<div class="grid">

<?php

$paket = ["J","A","E","Y","U","K"];

foreach($paket as $p){

echo "

<div class='card'>

<img src='https://picsum.photos/400/300?random=$p'>

<div class='card-body'>

<h3>Paket $p</h3>

<ul>
<li>Set Kuas Profesional</li>
<li>Palet Lukis</li>
<li>Kanvas Premium</li>
<li>Set Cat (Gratis)</li>
</ul>

<div class='btn'>Pinjam Sekarang</div>

</div>
</div>

";

}

?>

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
