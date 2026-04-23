<?php
require_once 'auth.php';
require_once 'layout.php';

$search = trim($_GET['q'] ?? '');
if ($search) {
    $jobs_stmt = $pdo->prepare("SELECT j.*, c.company_name FROM jobs j JOIN clients c ON j.client_id=c.id WHERE j.status='open' AND (j.title LIKE ? OR j.skills LIKE ?) ORDER BY j.created_at DESC");
    $jobs_stmt->execute(["%$search%", "%$search%"]);
} else {
    $jobs_stmt = $pdo->query("SELECT j.*, c.company_name AS client_company FROM jobs j JOIN clients c ON j.client_id=c.id WHERE j.status='open' ORDER BY j.created_at DESC");
}
$jobs = $jobs_stmt->fetchAll();

// Already applied
$applied_stmt = $pdo->prepare("SELECT job_id FROM applications WHERE freelancer_id=?");
$applied_stmt->execute([$fl_id]);
$applied_ids = array_column($applied_stmt->fetchAll(), 'job_id');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Apply for Jobs – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:4px}
.count{color:#8b8baa;font-size:14px;margin-bottom:20px}
.search-box{width:100%;background:#161b27;border:1px solid #1e1e2e;border-radius:10px;padding:11px 18px;color:#e6edf3;font-size:14px;outline:none;margin-bottom:20px;transition:.2s}
.search-box:focus{border-color:#f59e0b}
.job-card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:20px 24px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:flex-start;text-decoration:none;color:inherit;transition:.15s;cursor:pointer}
.job-card:hover{border-color:#333}
.job-title{font-size:16px;font-weight:700;margin-bottom:4px}
.job-meta{font-size:13px;color:#8b8baa;margin-bottom:8px}
.skill-tag{background:#1e1e2e;color:#8b8baa;padding:3px 10px;border-radius:4px;font-size:12px;display:inline-block;margin-right:5px;margin-top:2px}
.job-desc{font-size:13px;color:#666;margin-top:8px}
.mode-badge{padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;white-space:nowrap}
.mode-Remote{background:#1e1e2e;color:#7c3aed}
.mode-Hybrid{background:#1e2e1e;color:#00c97a}
.mode-On-site,.mode-Onsite{background:#2e1e1e;color:#f59e0b}
.empty{color:#8b8baa;text-align:center;padding:60px;font-size:15px}
</style>
</head>
<body>
<?php renderLayout('apply', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <h1>Apply for Jobs</h1>
    <p class="count"><?= count($jobs) ?> open positions</p>
    <form method="GET">
        <input class="search-box" type="text" name="q" placeholder="🔍 Search by title or skill..." value="<?= htmlspecialchars($search) ?>">
    </form>
    <?php if (empty($jobs)): ?>
        <p class="empty">No open jobs found.</p>
    <?php else: ?>
        <?php foreach ($jobs as $job): ?>
        <a href="job_apply.php?id=<?= $job['id'] ?>" class="job-card">
            <div>
                <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
                <div class="job-meta"><?= htmlspecialchars($job['company_name'] ?? $job['client_company']) ?> · <?= htmlspecialchars($job['work_mode']) ?> · ₹<?= number_format($job['salary']) ?>/mo</div>
                <?php foreach (array_filter(array_map('trim', explode(',', $job['skills']))) as $sk): ?>
                    <span class="skill-tag"><?= htmlspecialchars($sk) ?></span>
                <?php endforeach; ?>
                <div class="job-desc"><?= htmlspecialchars(substr($job['description'], 0, 100)) ?>...</div>
            </div>
            <span class="mode-badge mode-<?= str_replace('-','', $job['work_mode']) ?>"><?= $job['work_mode'] ?></span>
        </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
