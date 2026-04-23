<?php
require_once 'auth.php';
require_once 'layout.php';

$pdo->prepare("UPDATE notifications SET is_read=1 WHERE freelancer_id=?")->execute([$fl_id]);
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE freelancer_id=? ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$fl_id]);
$notifications = $stmt->fetchAll();
$notif_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications – FreelanceHub</title>
<style>
h1{font-size:24px;font-weight:700;margin-bottom:24px}
.notif-item{background:#161b27;border:1px solid #1e1e2e;border-radius:10px;padding:16px;margin-bottom:10px}
.notif-msg{font-size:14px;margin-bottom:4px}
.notif-time{font-size:12px;color:#8b8baa}
.empty{color:#8b8baa;text-align:center;padding:60px;font-size:15px}
</style>
</head>
<body>
<?php renderLayout('', $fl_name, $fl_user, 0); ?>
<div class="main">
    <h1>🔔 Notifications</h1>
    <?php if (empty($notifications)): ?>
        <p class="empty">No notifications yet.</p>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
        <div class="notif-item">
            <div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div>
            <div class="notif-time"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
