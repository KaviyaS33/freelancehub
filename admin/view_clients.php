<?php
require_once 'auth.php';
require_once 'layout.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM clients WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: view_clients.php'); exit;
}

$clients = $pdo->query("
    SELECT c.*,
        (SELECT COUNT(*) FROM jobs WHERE client_id=c.id) AS jobs_count
    FROM clients c
    WHERE c.status='approved'
    ORDER BY c.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Clients – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:4px}
.count{color:#8b8baa;font-size:14px;margin-bottom:24px}
.table-wrap{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}
thead tr{background:#1e1a2e}
th{padding:13px 16px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#8b8baa;text-align:left}
td{padding:13px 16px;font-size:14px;border-bottom:1px solid #1e1a2e}
tr:last-child td{border-bottom:none}
tr:hover td{background:#1a1730}
.company-text{font-weight:600}
.badge-jobs{background:#1e1a2e;color:#7c3aed;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600}
.badge-approved{background:#0d3d2a;color:#00c97a;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.btn-del{background:#e74c3c;color:#fff;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none}
.btn-del:hover{background:#c0392b}
.empty{color:#8b8baa;text-align:center;padding:40px;font-size:14px}
</style>
</head>
<body>
<?php adminLayout('clients', $admin_user, $notif_count); ?>
<div class="main">
    <h1>View Clients</h1>
    <p class="count"><?= count($clients) ?> clients</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Industry</th>
                    <th>Jobs</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($clients)): ?>
                <tr><td colspan="7" class="empty">No approved clients yet.</td></tr>
            <?php else: foreach ($clients as $c): ?>
            <tr>
                <td><div class="company-text"><?= htmlspecialchars($c['company_name'] ?: $c['username']) ?></div></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['city'] ?? '-') ?></td>
                <td><?= htmlspecialchars($c['industry'] ?? '-') ?></td>
                <td><span class="badge-jobs"><?= $c['jobs_count'] ?></span></td>
                <td><span class="badge-approved">Approved</span></td>
                <td><a href="view_clients.php?delete=<?= $c['id'] ?>" class="btn-del" onclick="return confirm('Delete this client?')">🗑</a></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
