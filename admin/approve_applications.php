<?php
require_once 'auth.php';
require_once 'layout.php';

// Handle approve/reject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $app_id = (int)$_POST['app_id'];
    $action = $_POST['action'];

    if (in_array($action, ['accepted', 'rejected'])) {
        $pdo->prepare("UPDATE applications SET status=? WHERE id=?")
            ->execute([$action, $app_id]);

        // Application details fetch
        $app = $pdo->prepare("SELECT a.*, j.title, j.client_id, f.full_name 
                               FROM applications a 
                               JOIN jobs j ON j.id=a.job_id 
                               JOIN freelancers f ON f.id=a.freelancer_id 
                               WHERE a.id=?");
        $app->execute([$app_id]);
        $app = $app->fetch();

        if ($app) {
            if ($action === 'accepted') {
                // Freelancer-க்கு notify
                $pdo->prepare("INSERT INTO notifications (user_id,user_type,message) VALUES (?,'freelancer',?)")
    ->execute([$app['freelancer_id'], "🎉 Your application for '{$app['title']}' was accepted!"]);

$pdo->prepare("INSERT INTO notifications (user_id,user_type,message) VALUES (?,'client',?)")
    ->execute([$app['client_id'], "✅ {$app['full_name']} accepted for '{$app['title']}'"]);
            } else {
                // Freelancer-க்கு reject notify
                $$pdo->prepare("INSERT INTO notifications (user_id,user_type,message) VALUES (?,'freelancer',?)")
    ->execute([$app['freelancer_id'], "❌ Your application for '{$app['title']}' was rejected."]);
            }
        }
    }
    header('Location: approve_applications.php'); exit;
}

// Fetch all pending applications
$apps = $pdo->query("
    SELECT a.*, j.title AS job_title, j.salary,
           f.full_name, f.email AS f_email, f.skills,
           c.company_name
    FROM applications a
    JOIN jobs j ON j.id=a.job_id
    JOIN freelancers f ON f.id=a.freelancer_id
    JOIN clients c ON c.id=j.client_id
    ORDER BY a.applied_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Applications – FreelanceHub Admin</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:4px}
.count{color:#8b8baa;font-size:14px;margin-bottom:24px}
.card{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;padding:22px;margin-bottom:14px}
.card-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px}
.job-title{font-size:17px;font-weight:700}
.company{font-size:13px;color:#8b8baa;margin-top:2px}
.badge-pending{background:#2d1e00;color:#f59e0b;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.badge-accepted{background:#052e16;color:#00c97a;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.badge-rejected{background:#3b0000;color:#f87171;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}
.info-item label{font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;display:block;margin-bottom:3px}
.info-item span{font-size:14px;color:#e6edf3}
.action-btns{display:flex;gap:10px}
.btn-approve{padding:9px 20px;background:#00c97a;color:#000;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer}
.btn-approve:hover{background:#00e88a}
.btn-reject{padding:9px 20px;background:#e74c3c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer}
.btn-reject:hover{background:#c0392b}
.empty{color:#8b8baa;text-align:center;padding:60px;font-size:15px}
.filter-tabs{display:flex;gap:10px;margin-bottom:20px}
.tab{padding:7px 18px;border-radius:20px;border:1px solid #2a2a3a;color:#8b8baa;cursor:pointer;font-size:13px;text-decoration:none}
.tab.active{background:#7c3aed;color:#fff;border-color:#7c3aed}
</style>
</head>
<body>
<?php adminLayout('applications', $admin_user, $notif_count); ?>
<div class="main">
    <h1>📋 Job Applications</h1>
    <p class="count"><?= count($apps) ?> total applications</p>

    <?php if (empty($apps)): ?>
        <p class="empty">No applications yet!</p>
    <?php else: foreach ($apps as $a): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <div class="job-title"><?= htmlspecialchars($a['job_title']) ?></div>
                <div class="company"><?= htmlspecialchars($a['company_name']) ?> · ₹<?= number_format($a['salary']) ?></div>
            </div>
            <?php if ($a['status'] === 'pending'): ?>
                <span class="badge-pending">Pending</span>
            <?php elseif ($a['status'] === 'accepted'): ?>
                <span class="badge-accepted">✅ Accepted</span>
            <?php else: ?>
                <span class="badge-rejected">❌ Rejected</span>
            <?php endif; ?>
        </div>

        <div class="info-grid">
            <div class="info-item"><label>Freelancer</label><span><?= htmlspecialchars($a['full_name']) ?></span></div>
            <div class="info-item"><label>Email</label><span><?= htmlspecialchars($a['f_email']) ?></span></div>
            <div class="info-item"><label>Skills</label><span><?= htmlspecialchars($a['skills']) ?></span></div>
            <div class="info-item"><label>Applied</label><span><?= date('d M Y', strtotime($a['applied_at'])) ?></span></div>
            <div class="info-item"><label>Cover Letter</label><span><?= htmlspecialchars(substr($a['cover_letter'] ?? '-', 0, 60)) ?>...</span></div>
        </div>

        <?php if ($a['status'] === 'pending'): ?>
        <div class="action-btns">
            <form method="POST" style="display:inline">
                <input type="hidden" name="app_id" value="<?= $a['id'] ?>">
                <input type="hidden" name="action" value="accepted">
                <button type="submit" class="btn-approve">✅ Accept</button>
            </form>
            <form method="POST" style="display:inline">
                <input type="hidden" name="app_id" value="<?= $a['id'] ?>">
                <input type="hidden" name="action" value="rejected">
                <button type="submit" class="btn-reject">✖ Reject</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; endif; ?>
</div>
</body>
</html>