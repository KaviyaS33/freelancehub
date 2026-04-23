<?php
require_once 'auth.php';
require_once 'layout.php';

$courses   = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$students  = $pdo->query("SELECT COUNT(*) FROM freelancers WHERE status='approved'")->fetchColumn();
$clients   = $pdo->query("SELECT COUNT(*) FROM clients WHERE status='approved'")->fetchColumn();
$pending   = $pdo->query("SELECT COUNT(*) FROM clients WHERE status='pending'")->fetchColumn();
$total_jobs= $pdo->query("SELECT COUNT(*) FROM jobs")->fetchColumn();
$total_paid= $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments")->fetchColumn();

$recent_jobs = $pdo->query("SELECT j.*, c.company_name FROM jobs j JOIN clients c ON j.client_id=c.id ORDER BY j.created_at DESC LIMIT 5")->fetchAll();
$pending_clients = $pdo->query("SELECT * FROM clients WHERE status='pending' LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard – FreelanceHub</title>
<style>
h1{font-size:30px;font-weight:700;margin-bottom:4px}
.sub{color:#8b8baa;font-size:14px;margin-bottom:28px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px}
.stat-card{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;padding:22px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.stat-card:nth-child(1)::before{background:#7c3aed}
.stat-card:nth-child(2)::before{background:#f59e0b}
.stat-card:nth-child(3)::before{background:#00c97a}
.stat-card:nth-child(4)::before{background:#7c3aed}
.stat-card:nth-child(5)::before{background:#3b82f6}
.stat-card:nth-child(6)::before{background:#f59e0b}
.stat-icon{font-size:26px;margin-bottom:10px}
.stat-value{font-size:28px;font-weight:700;color:#7c3aed;margin-bottom:4px}
.stat-card:nth-child(2) .stat-value{color:#f59e0b}
.stat-card:nth-child(3) .stat-value{color:#00c97a}
.stat-card:nth-child(4) .stat-value{color:#7c3aed}
.stat-card:nth-child(5) .stat-value{color:#3b82f6}
.stat-card:nth-child(6) .stat-value{color:#f59e0b}
.stat-label{font-size:13px;color:#8b8baa}
.bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:20px}
.panel{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;padding:22px}
.panel-title{font-size:15px;font-weight:700;margin-bottom:16px}
.job-row{padding:10px 0;border-bottom:1px solid #1e1a2e;display:flex;justify-content:space-between;align-items:center}
.job-row:last-child{border-bottom:none}
.job-title-text{font-size:14px;font-weight:600}
.job-company{font-size:12px;color:#8b8baa}
.mode-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.mode-Remote{background:#1e1a2e;color:#7c3aed}
.mode-Hybrid{background:#1e2e1e;color:#00c97a}
.mode-Onsite,.mode-On-site{background:#2e1e1e;color:#f59e0b}
.pending-row{padding:10px 0;border-bottom:1px solid #1e1a2e;display:flex;justify-content:space-between;align-items:center}
.pending-row:last-child{border-bottom:none}
.pending-name{font-size:14px;font-weight:600}
.pending-email{font-size:12px;color:#8b8baa}
.badge-pending{background:#2d1e00;color:#f59e0b;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.empty-text{color:#8b8baa;font-size:13px;text-align:center;padding:20px}
</style>
</head>
<body>
<?php adminLayout('dashboard', $admin_user, $notif_count); ?>
<div class="main">
    <h1>Admin Dashboard</h1>
    <p class="sub">Platform overview.</p>
    <div class="stats">
        <div class="stat-card"><div class="stat-icon">📚</div><div class="stat-value"><?= $courses ?></div><div class="stat-label">Courses</div></div>
        <div class="stat-card"><div class="stat-icon">🎓</div><div class="stat-value"><?= $students ?></div><div class="stat-label">Students</div></div>
        <div class="stat-card"><div class="stat-icon">🏢</div><div class="stat-value"><?= $clients ?></div><div class="stat-label">Clients</div></div>
        <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-value"><?= $pending ?></div><div class="stat-label">Pending Approvals</div></div>
        <div class="stat-card"><div class="stat-icon">💼</div><div class="stat-value"><?= $total_jobs ?></div><div class="stat-label">Total Jobs</div></div>
        <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-value">₹<?= number_format($total_paid) ?></div><div class="stat-label">Total Paid</div></div>
    </div>
    <div class="bottom-grid">
        <div class="panel">
            <div class="panel-title">Recent Jobs</div>
            <?php if (empty($recent_jobs)): ?><p class="empty-text">No jobs yet.</p>
            <?php else: foreach ($recent_jobs as $j): ?>
            <div class="job-row">
                <div>
                    <div class="job-title-text"><?= htmlspecialchars($j['title']) ?></div>
                    <div class="job-company"><?= htmlspecialchars($j['company_name']) ?></div>
                </div>
                <span class="mode-badge mode-<?= str_replace('-','',$j['work_mode']) ?>"><?= $j['work_mode'] ?></span>
            </div>
            <?php endforeach; endif; ?>
        </div>
        <div class="panel">
            <div class="panel-title">Pending Approvals</div>
            <?php if (empty($pending_clients)): ?><p class="empty-text">No pending approvals.</p>
            <?php else: foreach ($pending_clients as $c): ?>
            <div class="pending-row">
                <div>
                    <div class="pending-name"><?= htmlspecialchars($c['company_name'] ?: $c['username']) ?></div>
                    <div class="pending-email"><?= htmlspecialchars($c['username']) ?></div>
                </div>
                <span class="badge-pending">Pending</span>
            </div>
            <?php endforeach; endif; ?>
        </div>
    </div>
</div>
</body>
</html>
