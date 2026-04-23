<?php
require_once 'auth.php';
require_once 'layout.php';

$total_disbursed = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments")->fetchColumn();
$payments_count  = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();
$freelancers_count = $pdo->query("SELECT COUNT(*) FROM freelancers WHERE status='approved'")->fetchColumn();

$freelancers = $pdo->query("
    SELECT f.*,
        COALESCE(SUM(p.amount),0) AS total_earned,
        COUNT(p.id) AS pay_count
    FROM freelancers f
    LEFT JOIN payments p ON p.freelancer_id=f.id
    WHERE f.status='approved'
    GROUP BY f.id
    ORDER BY total_earned DESC
")->fetchAll();

$transactions = $pdo->query("
    SELECT p.*, f.full_name AS freelancer_name, f.username AS fl_user, j.title AS job_title, c.company_name
    FROM payments p
    JOIN freelancers f ON p.freelancer_id=f.id
    JOIN jobs j ON p.job_id=j.id
    JOIN clients c ON p.client_id=c.id
    ORDER BY p.paid_at DESC
    LIMIT 20
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Earnings Overview – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:24px;display:flex;align-items:center;gap:10px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px}
.stat-card{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;padding:22px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.stat-card:nth-child(1)::before{background:#f59e0b}
.stat-card:nth-child(2)::before{background:#00c97a}
.stat-card:nth-child(3)::before{background:#7c3aed}
.stat-value{font-size:26px;font-weight:700;color:#f59e0b;margin-bottom:4px}
.stat-card:nth-child(2) .stat-value{color:#00c97a}
.stat-card:nth-child(3) .stat-value{color:#7c3aed}
.stat-label{font-size:13px;color:#8b8baa}
.section-title{font-size:16px;font-weight:700;margin-bottom:16px}
.table-wrap{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;overflow:hidden;margin-bottom:24px}
table{width:100%;border-collapse:collapse}
thead tr{background:#1e1a2e}
th{padding:13px 16px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#8b8baa;text-align:left}
td{padding:13px 16px;font-size:14px;border-bottom:1px solid #1e1a2e}
tr:last-child td{border-bottom:none}
tr:hover td{background:#1a1730}
.name-text{font-weight:600}
.city-text{font-size:12px;color:#8b8baa}
.skill-tag{background:#1e1a2e;color:#8b8baa;padding:3px 8px;border-radius:4px;font-size:11px;display:inline-block;margin-right:4px}
.earned-text{color:#f59e0b;font-weight:700}
.pay-count{background:#1e1a2e;color:#7c3aed;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600}
.txn-amount{color:#00c97a;font-weight:700}
.empty{color:#8b8baa;text-align:center;padding:30px;font-size:14px}
</style>
</head>
<body>
<?php adminLayout('earnings', $admin_user, $notif_count); ?>
<div class="main">
    <h1>💰 Earnings Overview</h1>
    <div class="stats">
        <div class="stat-card"><div class="stat-value">₹<?= number_format($total_disbursed) ?></div><div class="stat-label">Total Disbursed</div></div>
        <div class="stat-card"><div class="stat-value"><?= $payments_count ?></div><div class="stat-label">Payments Made</div></div>
        <div class="stat-card"><div class="stat-value"><?= $freelancers_count ?></div><div class="stat-label">Freelancers</div></div>
    </div>

    <div class="section-title">Per-Freelancer Summary</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Freelancer</th><th>City</th><th>Skills</th><th>Total Earned</th><th>Payments</th></tr>
            </thead>
            <tbody>
            <?php if (empty($freelancers)): ?>
                <tr><td colspan="5" class="empty">No freelancers yet.</td></tr>
            <?php else: foreach ($freelancers as $f): ?>
            <tr>
                <td>
                    <div class="name-text"><?= htmlspecialchars($f['full_name'] ?: $f['username']) ?></div>
                </td>
                <td><?= htmlspecialchars($f['city'] ?? '-') ?></td>
                <td><?php foreach (array_slice(array_filter(array_map('trim', explode(',', $f['skills'] ?? ''))), 0, 3) as $sk): ?><span class="skill-tag"><?= htmlspecialchars($sk) ?></span><?php endforeach; ?></td>
                <td><span class="earned-text">₹<?= number_format($f['total_earned']) ?></span></td>
                <td><span class="pay-count"><?= $f['pay_count'] ?></span></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <div class="section-title">All Transactions</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Freelancer</th><th>Job</th><th>Company</th><th>Amount</th><th>Date</th></tr>
            </thead>
            <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="5" class="empty">No transactions yet.</td></tr>
            <?php else: foreach ($transactions as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t['freelancer_name'] ?: $t['fl_user']) ?></td>
                <td><?= htmlspecialchars($t['job_title']) ?></td>
                <td><?= htmlspecialchars($t['company_name']) ?></td>
                <td><span class="txn-amount">₹<?= number_format($t['amount']) ?></span></td>
                <td><?= date('d M Y', strtotime($t['paid_at'])) ?></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
