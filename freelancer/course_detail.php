<?php
require_once 'auth.php';
require_once 'layout.php';

$course_id = (int)($_GET['id'] ?? 0);
$course = $pdo->prepare("SELECT * FROM courses WHERE id=?");
$course->execute([$course_id]);
$course = $course->fetch();
if (!$course) { header('Location: dashboard.php'); exit; }

// Mark or create progress as ongoing
$prog = $pdo->prepare("SELECT * FROM course_progress WHERE freelancer_id=? AND course_id=?");
$prog->execute([$fl_id, $course_id]);
if (!$prog->fetch()) {
    $pdo->prepare("INSERT INTO course_progress (freelancer_id, course_id, status, started_at) VALUES (?,?,'ongoing',NOW())")
        ->execute([$fl_id, $course_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($course['title']) ?> – FreelanceHub</title>
<style>
h1{font-size:24px;font-weight:700;margin-bottom:6px}
.back-btn{display:inline-block;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px;margin-bottom:20px}
.back-btn:hover{border-color:#555;color:#fff}
.card{background:#161b27;border:1px solid #1e1e2e;border-radius:14px;padding:28px;margin-bottom:16px}
.level-badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600;margin-bottom:12px}
.level-Beginner{background:#1a2a1a;color:#00c97a}
.level-Intermediate{background:#1e1a2e;color:#7c3aed}
.level-Advanced{background:#2e1a1a;color:#ef4444}
.meta{color:#8b8baa;font-size:14px;margin-bottom:12px}
.desc{color:#aaa;font-size:14px;line-height:1.7}
.btn-go{display:inline-block;padding:12px 24px;background:#f59e0b;color:#000;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;margin-top:16px}
.btn-go:hover{background:#fbbf24}
</style>
</head>
<body>
<?php renderLayout('dashboard', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <a href="dashboard.php" class="back-btn">← Back</a>
    <div class="card">
        <span class="level-badge level-<?= $course['level'] ?>"><?= $course['level'] ?></span>
        <h1><?= htmlspecialchars($course['title']) ?></h1>
        <div class="meta">📁 <?= htmlspecialchars($course['category']) ?> · ⏱ <?= $course['duration_hrs'] ?> hrs</div>
        <div class="desc"><?= htmlspecialchars($course['description']) ?></div>
        <a href="course_watch.php?id=<?= $course['id'] ?>" class="btn-go">▶ Go to Course</a>
    </div>
</div>
</body>
</html>
