<?php
// Safe defaults
$nav_items = [
    'dashboard'       => ['icon' => '🏠', 'label' => 'Dashboard',       'href' => 'dashboard.php'],
    'post_job'        => ['icon' => '📄', 'label' => 'Post Job',        'href' => 'post_job.php'],
    'posted_jobs'     => ['icon' => '🧱', 'label' => 'Posted Jobs',     'href' => 'posted_jobs.php'],
    'manage_earnings' => ['icon' => '💰', 'label' => 'Manage Earnings', 'href' => 'manage_earnings.php'],
];

// Notifications count
$unread = 0;
if (isset($pdo) && isset($_SESSION['client_id'])) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM notifications 
        WHERE user_type='client' AND user_id=? AND is_read=0
    ");
    $stmt->execute([$_SESSION['client_id']]);
    $unread = (int)$stmt->fetchColumn();
}

// Avatar initial
$initial = strtoupper(substr($_SESSION['client_username'] ?? 'C', 0, 1));
?>

<div class="sidebar">
    <div class="sidebar-logo">
        <span>🏢</span> Client
    </div>

    <?php foreach ($nav_items as $key => $item): ?>
        <a href="<?= $item['href'] ?>"
           class="nav-link <?= ($page ?? '') === $key ? 'active' : '' ?>">
            <span class="nav-icon"><?= $item['icon'] ?></span>
            <?= $item['label'] ?>
        </a>
    <?php endforeach; ?>

    <div class="sidebar-spacer"></div>

    <a href="logout.php" class="logout-link">🚪 Logout</a>
</div>

<!-- TOPBAR -->
<div style="display:flex;flex-direction:column;flex:1;min-height:100vh;">

<div class="topbar">
    <button class="notif-btn" onclick="window.location='notifications.php'">
        🔔
        <?php if ($unread > 0): ?>
            <span class="notif-badge"><?= $unread ?></span>
        <?php endif; ?>
    </button>

    <div class="avatar"><?= $initial ?></div>
</div>

<div class="main-content">