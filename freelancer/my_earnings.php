<?php
require_once 'auth.php';
require_once 'layout.php';

$total_earned = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE freelancer_id=?");
$total_earned->execute([$fl_id]); $total_earned = $total_earned->fetchColumn();

$payments_count = $pdo->prepare("SELECT COUNT(*) FROM payments WHERE freelancer_id=?");
$payments_count->execute([$fl_id]); $payments_count = $payments_count->fetchColumn();

$jobs_worked = $pdo->prepare("SELECT COUNT(DISTINCT job_id) FROM payments WHERE freelancer_id=?");
$jobs_worked->execute([$fl_id]); $jobs_worked = $jobs_worked->fetchColumn();

$txn_stmt = $pdo->prepare("
    SELECT p.*, j.title AS job_title, c.company_name
    FROM payments p
    JOIN jobs j ON p.job_id=j.id
    JOIN clients c ON p.client_id=c.id
    WHERE p.freelancer_id=?
    ORDER BY p.paid_at DESC
");
$txn_stmt->execute([$fl_id]);
$transactions = $txn_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Earnings – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:24px;display:flex;align-items:center;gap:10px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px}
.stat-card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:22px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.stat-card:nth-child(1)::before{background:#f59e0b}
.stat-card:nth-child(2)::before{background:#00c97a}
.stat-card:nth-child(3)::before{background:#7c3aed}
.stat-icon{font-size:22px;margin-bottom:8px}
.stat-value{font-size:26px;font-weight:700;color:#f59e0b;margin-bottom:4px}
.stat-card:nth-child(2) .stat-value{color:#00c97a}
.stat-card:nth-child(3) .stat-value{color:#7c3aed}
.stat-label{font-size:12px;color:#8b8baa}
.section-title{font-size:16px;font-weight:700;margin-bottom:16px}
.txn-card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:24px}
.txn-row{display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-bottom:1px solid #1e1e2e}
.txn-row:last-child{border-bottom:none}
.txn-info .job{font-size:14px;font-weight:600}
.txn-info .company{font-size:12px;color:#8b8baa;margin-top:2px}
.txn-info .note{font-size:12px;color:#666;margin-top:2px}
.txn-amount{color:#00c97a;font-weight:700;font-size:16px}
.txn-date{font-size:11px;color:#666;text-align:right;margin-top:2px}
.empty{color:#8b8baa;text-align:center;padding:40px;font-size:14px}
</style>
</head>
<body>
<?php renderLayout('earnings', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <h1>💰 My Earnings</h1>
    <div class="stats">
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-value">₹<?= number_format($total_earned) ?></div>
            <div class="stat-label">Total Earned</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-value"><?= $payments_count ?></div>
            <div class="stat-label">Payments Received</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🏢</div>
            <div class="stat-value"><?= $jobs_worked ?></div>
            <div class="stat-label">Jobs Worked</div>
        </div>
    </div>
    <div class="section-title">All Transactions</div>
    <div class="txn-card">
        <?php if (empty($transactions)): ?>
            <div class="empty">No earnings yet. Apply for jobs and get paid!</div>
        <?php else: ?>
            <?php foreach ($transactions as $txn): ?>
            <div class="txn-row">
                <div class="txn-info">
                    <div class="job"><?= htmlspecialchars($txn['job_title']) ?></div>
                    <div class="company"><?= htmlspecialchars($txn['company_name']) ?></div>
                    <?php if ($txn['note']): ?><div class="note"><?= htmlspecialchars($txn['note']) ?></div><?php endif; ?>
                </div>
                <div>
                    <div class="txn-amount">₹<?= number_format($txn['amount']) ?></div>
                    <div class="txn-date"><?= date('d M Y', strtotime($txn['paid_at'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
