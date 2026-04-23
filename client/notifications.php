<?php
// client/notifications.php
session_start();
if (empty($_SESSION['client_id'])) { header('Location: auth.php?action=login'); exit; }
require_once '../db.php';

$client_id = $_SESSION['client_id'];

// Mark all as read
$pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_type='client' AND user_id=?")->execute([$client_id]);

$notifs = $pdo->prepare("SELECT * FROM notifications WHERE user_type='client' AND user_id=? ORDER BY created_at DESC");
$notifs->execute([$client_id]);
$all = $notifs->fetchAll();

$page = 'notifications';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications — FreelanceHub Client</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="dashboard-layout">
<?php require 'partials/sidebar.php'; ?>

    <h1 class="page-title">🔔 Notifications</h1>

    <?php if (empty($all)): ?>
    <div class="placeholder-box">
        <div class="ph-icon">🔔</div>
        <div class="ph-title">No notifications</div>
        <div class="ph-sub">You're all caught up!</div>
    </div>
    <?php else: ?>
    <div class="jobs-list">
    <?php foreach ($all as $n): ?>
        <div class="job-card" style="padding:1rem 1.4rem;">
            <div>
                <div style="font-size:.92rem;margin-bottom:.3rem;"><?= htmlspecialchars($n['message']) ?></div>
                <div style="color:var(--text-muted);font-size:.78rem;"><?= $n['created_at'] ?></div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>
</div>
</div>
</body>
</html>
