<?php
// Call this function to render sidebar + topbar
// $current: dashboard | apply | earnings | portfolio
function renderLayout($current, $fl_name, $fl_user, $notif_count) {
?>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0d0d14;color:#e6edf3;font-family:'Segoe UI',sans-serif;display:flex;min-height:100vh;background:radial-gradient(ellipse at 80% 90%,#12102a 0%,#0d0d14 60%)}
.sidebar{width:200px;background:#111118;border-right:1px solid #1e1e2e;display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100}
.sidebar-brand{padding:20px 18px;font-size:16px;font-weight:700;border-bottom:1px solid #1e1e2e;display:flex;align-items:center;gap:8px;color:#e6edf3}
.sidebar-nav{flex:1;padding:12px 0}
.nav-item{display:flex;align-items:center;gap:10px;padding:11px 18px;color:#8b8baa;text-decoration:none;font-size:14px;font-weight:500;transition:.15s}
.nav-item:hover{color:#fff;background:#1e1e2e}
.nav-item.active{color:#fff;background:#f59e0b22;border-left:3px solid #f59e0b;padding-left:15px}
.sidebar-footer{padding:16px 18px;border-top:1px solid #1e1e2e}
.logout{color:#e74c3c;text-decoration:none;font-size:14px;display:flex;align-items:center;gap:6px}
.main{margin-left:200px;flex:1;padding:32px 36px;margin-top:60px}
.topbar{position:fixed;top:0;right:0;left:200px;height:60px;background:#111118;border-bottom:1px solid #1e1e2e;display:flex;align-items:center;justify-content:flex-end;padding:0 24px;gap:16px;z-index:99}
.notif-btn{position:relative;background:none;border:none;color:#8b8baa;font-size:20px;cursor:pointer;text-decoration:none}
.notif-badge{position:absolute;top:-4px;right:-4px;background:#e74c3c;color:#fff;font-size:10px;font-weight:700;width:17px;height:17px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.avatar{width:34px;height:34px;background:#f59e0b;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#000;text-transform:uppercase}
</style>
<div class="sidebar">
    <div class="sidebar-brand">🧑 Hub</div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $current==='dashboard'?'active':'' ?>">🏠 Dashboard</a>
        <a href="apply_jobs.php" class="nav-item <?= $current==='apply'?'active':'' ?>">🗂 Apply for Jobs</a>
        <a href="my_earnings.php" class="nav-item <?= $current==='earnings'?'active':'' ?>">💰 My Earnings</a>
        <a href="my_portfolio.php" class="nav-item <?= $current==='portfolio'?'active':'' ?>">🎨 My Portfolio</a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="logout">🔴 Logout</a>
    </div>
</div>
<div class="topbar">
    <a href="notifications.php" class="notif-btn">
        🔔
        <?php if ($notif_count > 0): ?><span class="notif-badge"><?= $notif_count ?></span><?php endif; ?>
    </a>
    <div class="avatar"><?= strtoupper(substr($fl_user, 0, 1)) ?></div>
</div>
<?php
}
?>
