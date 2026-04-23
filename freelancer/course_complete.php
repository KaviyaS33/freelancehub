<?php
require_once 'auth.php';
require_once 'layout.php';

$course_id = (int)($_GET['id'] ?? 0);
$course = $pdo->prepare("SELECT * FROM courses WHERE id=?");
$course->execute([$course_id]);
$course = $course->fetch();
if (!$course) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Course Complete – FreelanceHub</title>
<style>
.back-btn{display:inline-block;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px;margin-bottom:20px}
.back-btn:hover{border-color:#555;color:#fff}
.success-banner{background:#0d3d2a;border:1px solid #00c97a;color:#00c97a;padding:14px 20px;border-radius:10px;margin-bottom:20px;font-size:15px;font-weight:600}
.card{background:#161b27;border:1px solid #1e1e2e;border-radius:14px;padding:28px;margin-bottom:14px}
.section-title{font-size:16px;font-weight:700;margin-bottom:14px;display:flex;align-items:center;gap:8px}
.project-task{background:#0d1117;border-radius:8px;padding:16px;font-size:14px;color:#aaa;line-height:1.7}
.btn-exit{display:inline-block;padding:10px 20px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px;margin-top:16px}
.btn-exit:hover{border-color:#555;color:#fff}
</style>
</head>
<body>
<?php renderLayout('dashboard', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <a href="dashboard.php" class="back-btn">← Back</a>
    <div class="success-banner">🎉 Course Completed! Here's your project:</div>
    <div class="card">
        <div class="section-title">📋 Project Task</div>
        <div class="project-task"><?= nl2br(htmlspecialchars($course['project_task'] ?? 'Complete the project as described in the course.')) ?></div>
        <a href="dashboard.php" class="btn-exit">Exit ×</a>
    </div>
</div>
</body>
</html>
