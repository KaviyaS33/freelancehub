<?php
require_once 'auth.php';
require_once 'layout.php';

// Stats
$completed = $pdo->prepare("SELECT COUNT(*) FROM course_progress WHERE freelancer_id=? AND status='completed'");
$completed->execute([$fl_id]); $completed = $completed->fetchColumn();

$ongoing = $pdo->prepare("SELECT COUNT(*) FROM course_progress WHERE freelancer_id=? AND status='ongoing'");
$ongoing->execute([$fl_id]); $ongoing = $ongoing->fetchColumn();

$skills_count = count(array_filter(array_map('trim', explode(',', $fl_profile['skills'] ?? ''))));

$total_earned = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE freelancer_id=?");
$total_earned->execute([$fl_id]); $total_earned = $total_earned->fetchColumn();

// All courses
$search = trim($_GET['q'] ?? '');
if ($search) {
    $courses_stmt = $pdo->prepare("SELECT * FROM courses WHERE title LIKE ? OR category LIKE ? ORDER BY id");
    $courses_stmt->execute(["%$search%", "%$search%"]);
} else {
    $courses_stmt = $pdo->query("SELECT * FROM courses ORDER BY id");
}
$courses = $courses_stmt->fetchAll();

// Progress map
$prog_stmt = $pdo->prepare("SELECT course_id, status FROM course_progress WHERE freelancer_id=?");
$prog_stmt->execute([$fl_id]);
$progress_map = [];
foreach ($prog_stmt->fetchAll() as $p) $progress_map[$p['course_id']] = $p['status'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard – FreelanceHub</title>
<style>
<?php ob_start(); ?>
h1{font-size:28px;font-weight:700;margin-bottom:4px}
.welcome-sub{color:#8b8baa;font-size:14px;margin-bottom:28px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:32px}
.stat-card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:20px;position:relative;overflow:hidden}
.stat-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.stat-card:nth-child(1)::before{background:#00c97a}
.stat-card:nth-child(2)::before{background:#f59e0b}
.stat-card:nth-child(3)::before{background:#7c3aed}
.stat-card:nth-child(4)::before{background:#f59e0b}
.stat-value{font-size:26px;font-weight:700;color:#f59e0b;margin-bottom:4px}
.stat-label{font-size:12px;color:#8b8baa}
.section-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.section-title{font-size:18px;font-weight:700}
.search-box{background:#161b27;border:1px solid #1e1e2e;border-radius:8px;padding:9px 16px;color:#e6edf3;font-size:14px;outline:none;width:220px;transition:.2s}
.search-box:focus{border-color:#f59e0b}
.courses-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.course-card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:20px;transition:.15s}
.course-card:hover{border-color:#333}
.course-icon{font-size:28px;margin-bottom:10px}
.level-badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;margin-bottom:8px}
.level-Beginner{background:#1a2a1a;color:#00c97a}
.level-Intermediate{background:#1e1a2e;color:#7c3aed}
.level-Advanced{background:#2e1a1a;color:#ef4444}
.course-title{font-size:15px;font-weight:700;margin-bottom:4px}
.course-meta{font-size:12px;color:#8b8baa;margin-bottom:14px}
.btn-course{width:100%;padding:10px;background:#f59e0b;color:#000;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;display:block;text-align:center}
.btn-course:hover{background:#fbbf24}
.btn-course.ongoing{background:#1e1a2e;color:#7c3aed;border:1px solid #7c3aed}
.btn-course.completed{background:#0d3d2a;color:#00c97a;border:1px solid #00c97a}
<?php $css = ob_get_clean(); echo $css; ?>
</style>
</head>
<body>
<?php renderLayout('dashboard', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <h1>Welcome, <?= htmlspecialchars($fl_name) ?>! 👋</h1>
    <p class="welcome-sub">Keep learning and growing.</p>
    <div class="stats">
        <div class="stat-card"><div class="stat-value"><?= $completed ?></div><div class="stat-label">Completed</div></div>
        <div class="stat-card"><div class="stat-value"><?= $ongoing ?></div><div class="stat-label">Ongoing</div></div>
        <div class="stat-card"><div class="stat-value"><?= $skills_count ?></div><div class="stat-label">Skills</div></div>
        <div class="stat-card"><div class="stat-value">₹<?= number_format($total_earned) ?></div><div class="stat-label">Total Earned</div></div>
    </div>
    <div class="section-header">
        <div class="section-title">All Courses</div>
        <form method="GET">
            <input class="search-box" type="text" name="q" placeholder="🔍 Search courses..." value="<?= htmlspecialchars($search) ?>">
        </form>
    </div>
    <div class="courses-grid">
        <?php foreach ($courses as $course): ?>
        <?php $status = $progress_map[$course['id']] ?? null; ?>
        <div class="course-card">
            <div class="course-icon">📚</div>
            <span class="level-badge level-<?= $course['level'] ?>"><?= $course['level'] ?></span>
            <div class="course-title"><?= htmlspecialchars($course['title']) ?></div>
            <div class="course-meta"><?= htmlspecialchars($course['category']) ?> · ⏱ <?= $course['duration_hrs'] ?> hrs</div>
            <?php if ($status === 'completed'): ?>
                <a href="course_detail.php?id=<?= $course['id'] ?>" class="btn-course completed">✅ Completed</a>
            <?php elseif ($status === 'ongoing'): ?>
                <a href="course_watch.php?id=<?= $course['id'] ?>" class="btn-course ongoing">▶ Continue</a>
            <?php else: ?>
                <a href="course_detail.php?id=<?= $course['id'] ?>" class="btn-course">Start Course</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
