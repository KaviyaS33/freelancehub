<?php
require_once 'auth.php';
require_once 'layout.php';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid    = (int)$_POST['client_id'];
    $action = $_POST['action'];
    if (in_array($action, ['approved', 'rejected'])) {
        $pdo->prepare("UPDATE clients SET status=? WHERE id=?")->execute([$action, $cid]);
        if ($action === 'approved') {
            $cl = $pdo->prepare("SELECT username FROM clients WHERE id=?");
            $cl->execute([$cid]); $cl = $cl->fetch();
            // Notify client via session-based approach (or just update status)
        }
    }
    header('Location: approve_clients.php'); exit;
}

$pending = $pdo->query("SELECT * FROM clients WHERE status='pending' ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Approve Clients – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:4px}
.count{color:#8b8baa;font-size:14px;margin-bottom:24px}
.client-card{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;padding:24px;margin-bottom:14px;position:relative}
.card-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px}
.client-company{font-size:18px;font-weight:700}
.client-username{font-size:13px;color:#8b8baa;margin-top:2px}
.badge-pending{background:#2d1e00;color:#f59e0b;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
.info-item label{font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;display:block;margin-bottom:3px}
.info-item span{font-size:14px;color:#e6edf3}
.action-btns{display:flex;gap:10px}
.btn-approve{padding:10px 22px;background:#00c97a;color:#000;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px}
.btn-approve:hover{background:#00e88a}
.btn-reject{padding:10px 22px;background:#e74c3c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:6px}
.btn-reject:hover{background:#c0392b}
.empty{color:#8b8baa;text-align:center;padding:60px;font-size:15px}
</style>
</head>
<body>
<?php adminLayout('approve', $admin_user, $notif_count); ?>
<div class="main">
    <h1>Approve Clients</h1>
    <p class="count"><?= count($pending) ?> pending</p>
    <?php if (empty($pending)): ?>
        <p class="empty">🎉 No pending approvals!</p>
    <?php else: foreach ($pending as $c): ?>
    <div class="client-card">
        <div class="card-header">
            <div>
                <div class="client-company"><?= htmlspecialchars($c['company_name'] ?: $c['username']) ?></div>
                <div class="client-username"><?= htmlspecialchars($c['username']) ?></div>
            </div>
            <span class="badge-pending">Pending Review</span>
        </div>
        <div class="info-grid">
            <div class="info-item"><label>Email</label><span><?= htmlspecialchars($c['email']) ?></span></div>
            <div class="info-item"><label>Phone</label><span><?= htmlspecialchars($c['phone'] ?? '-') ?></span></div>
            <div class="info-item"><label>GST</label><span><?= htmlspecialchars($c['gst_number'] ?? '-') ?></span></div>
            <div class="info-item"><label>CIN</label><span><?= htmlspecialchars($c['cin_number'] ?? '-') ?></span></div>
            <div class="info-item"><label>Website</label><span><?= htmlspecialchars($c['website'] ?? '-') ?></span></div>
            <div class="info-item"><label>Size</label><span><?= htmlspecialchars($c['company_size'] ?? '-') ?></span></div>
        </div>
        <div class="action-btns">
            <form method="POST" style="display:inline">
                <input type="hidden" name="client_id" value="<?= $c['id'] ?>">
                <input type="hidden" name="action" value="approved">
                <button type="submit" class="btn-approve">✅ Approve</button>
            </form>
            <form method="POST" style="display:inline">
                <input type="hidden" name="client_id" value="<?= $c['id'] ?>">
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="btn-reject">✖ Reject</button>
            </form>
        </div>
    </div>
    <?php endforeach; endif; ?>
</div>
</body>
</html>
