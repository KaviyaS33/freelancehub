<?php
require_once 'auth.php';
require_once 'layout.php';

// Completed courses
$comp_stmt = $pdo->prepare("
    SELECT c.* FROM course_progress cp
    JOIN courses c ON cp.course_id=c.id
    WHERE cp.freelancer_id=? AND cp.status='completed'
");
$comp_stmt->execute([$fl_id]);
$completed_courses = $comp_stmt->fetchAll();

// Jobs applied
$apps_stmt = $pdo->prepare("
    SELECT a.*, j.title AS job_title, j.work_mode, c.company_name
    FROM applications a
    JOIN jobs j ON a.job_id=j.id
    JOIN clients c ON j.client_id=c.id
    WHERE a.freelancer_id=?
    ORDER BY a.applied_at DESC
");
$apps_stmt->execute([$fl_id]);
$applications = $apps_stmt->fetchAll();

// Total earned
$total_earned = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE freelancer_id=?");
$total_earned->execute([$fl_id]); $total_earned = $total_earned->fetchColumn();

$skills = array_filter(array_map('trim', explode(',', $fl_profile['skills'] ?? '')));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Portfolio – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:24px;display:flex;align-items:center;gap:10px}
.card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:24px;margin-bottom:16px}
.section-title{font-size:15px;font-weight:700;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.profile-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.profile-item label{font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;display:block;margin-bottom:3px}
.profile-item value,.profile-item .val{font-size:14px;color:#e6edf3}
.skill-tag{background:#1e1e2e;color:#8b8baa;padding:4px 12px;border-radius:6px;font-size:12px;display:inline-block;margin-right:6px;margin-top:4px}
.courses-grid{display:flex;flex-wrap:wrap;gap:12px}
.course-chip{background:#0d3d2a;border:1px solid #00c97a22;border-radius:10px;padding:12px 14px;min-width:140px}
.course-chip .check{font-size:18px;margin-bottom:4px}
.course-chip .c-title{font-size:13px;font-weight:600}
.course-chip .c-cat{font-size:11px;color:#8b8baa}
.app-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #1e1e2e}
.app-row:last-child{border-bottom:none}
.app-title{font-size:14px;font-weight:600}
.app-company{font-size:12px;color:#8b8baa}
.mode-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.mode-Remote{background:#1e1e2e;color:#7c3aed}
.mode-Hybrid{background:#1e2e1e;color:#00c97a}
.mode-Onsite,.mode-On-site{background:#2e1e1e;color:#f59e0b}
.earned-row{display:flex;justify-content:space-between;align-items:center}
.earned-label{font-size:15px;font-weight:700;display:flex;align-items:center;gap:8px}
.earned-val{font-size:20px;font-weight:700;color:#f59e0b}
.empty{color:#8b8baa;font-size:13px}
</style>
</head>
<body>
<?php renderLayout('portfolio', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <h1>🎨 My Portfolio</h1>

    <!-- Profile -->
    <div class="card">
        <div class="section-title">👤 Profile</div>
        <div class="profile-grid">
            <div class="profile-item"><label>Name</label><span class="val"><?= htmlspecialchars($fl_profile['full_name'] ?? $fl_name) ?></span></div>
            <div class="profile-item"><label>Email</label><span class="val"><?= htmlspecialchars($fl_profile['email'] ?? '') ?></span></div>
            <div class="profile-item"><label>Phone</label><span class="val"><?= htmlspecialchars($fl_profile['phone'] ?? '-') ?></span></div>
            <div class="profile-item"><label>Education</label><span class="val"><?= htmlspecialchars($fl_profile['education'] ?? '-') ?></span></div>
            <div class="profile-item"><label>Experience</label><span class="val"><?= htmlspecialchars($fl_profile['experience'] ?? '-') ?></span></div>
            <div class="profile-item"><label>City</label><span class="val"><?= htmlspecialchars($fl_profile['city'] ?? '-') ?></span></div>
            <div class="profile-item"><label>Portfolio</label><span class="val"><?= htmlspecialchars($fl_profile['portfolio_url'] ?? '-') ?></span></div>
        </div>
        <?php if (!empty($skills)): ?>
        <div style="margin-top:14px">
            <label style="font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;display:block;margin-bottom:6px">Skills</label>
            <?php foreach ($skills as $s): ?>
                <span class="skill-tag"><?= htmlspecialchars($s) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Completed Courses -->
    <div class="card">
        <div class="section-title">🏆 Completed Courses (<?= count($completed_courses) ?>)</div>
        <?php if (empty($completed_courses)): ?>
            <p class="empty">No courses completed yet.</p>
        <?php else: ?>
        <div class="courses-grid">
            <?php foreach ($completed_courses as $c): ?>
            <div class="course-chip">
                <div class="check">✅</div>
                <div class="c-title"><?= htmlspecialchars($c['title']) ?></div>
                <div class="c-cat"><?= htmlspecialchars($c['category']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Jobs Applied -->
    <div class="card">
        <div class="section-title">🗂 Jobs Applied (<?= count($applications) ?>)</div>
        <?php if (empty($applications)): ?>
            <p class="empty">No applications yet.</p>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
            <div class="app-row">
                <div>
                    <div class="app-title"><?= htmlspecialchars($app['job_title']) ?></div>
                    <div class="app-company"><?= htmlspecialchars($app['company_name']) ?></div>
                </div>
                <span class="mode-badge mode-<?= str_replace('-','',$app['work_mode']) ?>"><?= $app['work_mode'] ?></span>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Total Earned -->
    <div class="card">
        <div class="earned-row">
            <div class="earned-label">💰 Total Earned</div>
            <div class="earned-val">₹<?= number_format($total_earned) ?></div>
        </div>
    </div>
</div>
</body>
</html>
