<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FreelanceHub</title>

<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<script>
  window.addEventListener('pageshow', function(e) {
    if (e.persisted) {
      window.location.href = window.location.href;
    }
  });
</script>

<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
    background:#0a0a0f;
    color:#fff;
    font-family:'Segoe UI',sans-serif;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:radial-gradient(ellipse at 20% 50%, #1a1a2e 0%, #0a0a0f 60%);
}

.container{
    text-align:center;
    padding:40px 20px;
    width:100%;
    max-width:1200px;
}

h1{
    font-size:3.5rem;
    font-weight:800;
    margin-bottom:16px;
}

h1 span{
    color:#6c63ff;
}

.subtitle{
    color:#888;
    font-size:1.1rem;
    margin-bottom:60px;
}

.portals{
    display:flex;
    gap:24px;
    justify-content:center;
    flex-wrap:wrap;
}

.portal-card{
    display:block;
    text-decoration:none;
    color:#fff;
    background:#13131a;
    border:1px solid #2a2a3a;
    border-radius:16px;
    padding:40px 32px;
    width:300px;
    transition:0.3s;
}

.portal-card:hover{
    transform:translateY(-5px);
}

.admin:hover{
    border-color:#6c63ff;
    box-shadow:0 0 25px #6c63ff33;
}

.client:hover{
    border-color:#10b981;
    box-shadow:0 0 25px #10b98133;
}

.freelancer:hover{
    border-color:#f59e0b;
    box-shadow:0 0 25px #f59e0b33;
}

.portal-icon{
    font-size:3rem;
    margin-bottom:20px;
}

.portal-card h3{
    font-size:1.4rem;
    font-weight:700;
    margin-bottom:8px;
}

.portal-card p{
    color:#666;
    font-size:0.9rem;
    margin-bottom:24px;
}

.btn{
    display:block;
    padding:12px;
    border-radius:8px;
    font-weight:600;
    font-size:0.9rem;
}

.btn-admin{background:#6c63ff;}
.btn-client{background:#10b981;}
.btn-freelancer{background:#f59e0b;color:#000;}
</style>
</head>

<body>

<div class="container">

<h1>Freelance<span>Hub</span></h1>
<p class="subtitle">A complete platform connecting talented freelancers with businesses.</p>

<div class="portals">

<!-- ADMIN -->
<a href="/admin/login.php?t=<?= time() ?>" class="portal-card admin">
    <div class="portal-icon">🛡️</div>
    <h3>Admin Portal</h3>
    <p>Manage users, courses & approvals</p>
    <div class="btn btn-admin">Enter Portal →</div>
</a>

<!-- CLIENT -->
<a href="/client/login.php?t=<?= time() ?>" class="portal-card client">
    <div class="portal-icon">🏢</div>
    <h3>Client Portal</h3>
    <p>Post jobs & hire freelancers</p>
    <div class="btn btn-client">Enter Portal →</div>
</a>

<!-- FREELANCER -->
<a href="/freelancer/login.php?t=<?= time() ?>" class="portal-card freelancer">
    <div class="portal-icon">👨‍💻</div>
    <h3>Freelancer Portal</h3>
    <p>Learn, grow & get hired</p>
    <div class="btn btn-freelancer">Enter Portal →</div>
</a>

</div>

</div>

</body>
</html>