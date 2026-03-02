<!DOCTYPE html>
<html>
<head>
<title>Login Olvart</title>

<style>

body{
    font-family: Arial;
    background:#e8e4d3;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    width:350px;
    background:white;
    border-radius:20px;
    overflow:hidden;
}

.header{
    background:#7a0000;
    color:white;
    text-align:center;
    padding:30px;
}

.form{
    padding:25px;
}

input{
    width:100%;
    padding:12px;
    margin-bottom:15px;
    border-radius:10px;
    border:1px solid #ccc;
}

button{
    width:100%;
    background:#7a0000;
    color:white;
    border:none;
    padding:15px;
    border-radius:12px;
}

.info{
    background:#f4ecd6;
    padding:15px;
    border-radius:12px;
}

</style>
</head>

<body>

<div class="card">

<div class="header">
<h2>Olvart</h2>
<p>Seni dalam Genggaman</p>
</div>

<div class="form">

<form method="POST" action="{{ route('login') }}">

<input type="email" name="email" placeholder="Email">
<input type="password" name="password" placeholder="Password">

<button>Masuk Sekarang</button>

</form>

<div class="info">
Informasi Login Demo
</div>

</div>
</div>

</body>
</html>
