<?php
function clientLayout($current, $client_name, $notif_count) {
?>
<style>
*{margin:0;padding:0;box-sizing:border-box}

body{
    background:#0d0d14;
    color:#e6edf3;
    font-family:'Segoe UI',sans-serif;
    display:flex;
    min-height:100vh;
    background:radial-gradient(ellipse at 20% 80%,#130d2e 0%,#0d0d14 65%);
}

/* SIDEBAR */
.sidebar{
    width:220px;
    background:#111118;
    border-right:1px solid #1e1a2e;
    display:flex;
    flex-direction:column;
    position:fixed;
    top:0;
    left:0;
    height:100vh;
}

.sidebar-brand{
    padding:20px;
    font-size:18px;
    font-weight:700;
    border-bottom:1px solid #1e1a2e;
}

.sidebar-nav{
    flex:1;
    padding:10px 0;
}

.nav-item{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 18px;
    color:#8b8baa;
    text-decoration:none;
    font-size:14px;
    transition:.2s;
}

.nav-item:hover{
    background:#1e1a2e;
    color:#fff;
}

.nav-item.active{
    background:#22c55e22;
    color:#22c55e;
    border-left:3px solid #22c55e;
}

/* LOGOUT */
.sidebar-footer{
    padding:15px;
    border-top:1px solid #1e1a2e;
}

.logout-link{
    color:#ef4444;
    text-decoration:none;
}

/* TOPBAR */
.topbar{
    position:fixed;
    top:0;
    left:220px;
    right:0;
    height:60px;
    background:#111118;
    border-bottom:1px solid #1e1a2e;
    display:flex;
    justify-content:flex-end;
    align-items:center;
    padding:0 20px;
    gap:15px;
}

/* NOTIFICATION */
.notif-btn{
    position:relative;
    font-size:18px;
    color:#fff;
    text-decoration:none;
}

.notif-badge{
    position:absolute;
    top:-5px;
    right:-5px;
    background:red;
    color:#fff;
    font-size:10px;
    width:16px;
    height:16px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* AVATAR */
.avatar{
    width:34px;
    height:34px;
    background:#22c55e;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:bold;
}

/* MAIN */
.main{
    margin-left:220px;
    margin-top:60px;
    padding:25px;
    width:100%;
}
</style>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-brand">🏢 Client</div>

    <div class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $current=='dashboard'?'active':'' ?>">🏠 Dashboard</a>
        <a href="post_job.php" class="nav-item <?= $current=='post_job'?'active':'' ?>">📄 Post Job</a>
        <a href="posted_jobs.php" class="nav-item <?= $current=='jobs'?'active':'' ?>">🧱 Posted Jobs</a>
        <a href="manage_earnings.php" class="nav-item <?= $current=='earnings'?'active':'' ?>">💰 Earnings</a>
    </div>

    <div class="sidebar-footer">
        <a href="logout.php" class="logout-link">🚪 Logout</a>
    </div>
</div>

<!-- TOPBAR -->
<div class="topbar">
    <a href="notifications.php" class="notif-btn">
        🔔
        <?php if ($notif_count > 0): ?>
            <span class="notif-badge"><?= $notif_count ?></span>
        <?php endif; ?>
    </a>

    <div class="avatar">
        <?= strtoupper(substr($client_name,0,1)) ?>
    </div>
</div>

<div class="main">
<?php
}
?>