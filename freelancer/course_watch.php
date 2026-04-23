<?php
require_once 'auth.php';
require_once 'layout.php';

$course_id = (int)($_GET['id'] ?? 0);
$course = $pdo->prepare("SELECT * FROM courses WHERE id=?");
$course->execute([$course_id]);
$course = $course->fetch();
if (!$course) { header('Location: dashboard.php'); exit; }

$prog = $pdo->prepare("SELECT * FROM course_progress WHERE freelancer_id=? AND course_id=?");
$prog->execute([$fl_id, $course_id]);
$progress = $prog->fetch();

$completed = ($progress && $progress['status'] === 'completed');

// Handle mark as complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete'])) {
    if ($progress) {
        $pdo->prepare("UPDATE course_progress SET status='completed', completed_at=NOW() WHERE freelancer_id=? AND course_id=?")
            ->execute([$fl_id, $course_id]);
    } else {
        $pdo->prepare("INSERT INTO course_progress (freelancer_id, course_id, status, started_at, completed_at) VALUES (?,?,'completed',NOW(),NOW())")
            ->execute([$fl_id, $course_id]);
    }
    header("Location: course_complete.php?id=$course_id"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($course['title']) ?> – Watch</title>
<style>
.back-btn{display:inline-block;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px;margin-bottom:20px}
.back-btn:hover{border-color:#555;color:#fff}
.card{background:#161b27;border:1px solid #1e1e2e;border-radius:14px;padding:28px;margin-bottom:16px}
h2{font-size:20px;font-weight:700;margin-bottom:20px}
.video-box{background:#0d1117;border-radius:10px;padding:40px;text-align:center;margin-bottom:16px}
.play-icon{font-size:52px;margin-bottom:14px}
.video-title{font-size:17px;font-weight:700;margin-bottom:4px}
.video-meta{font-size:13px;color:#8b8baa;margin-bottom:18px}
.btn-youtube{display:inline-block;padding:12px 28px;background:#ef4444;color:#fff;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;display:flex;align-items:center;gap:8px;justify-content:center;max-width:220px;margin:0 auto}
.btn-youtube:hover{background:#dc2626}
.hint{font-size:12px;color:#666;margin-top:10px}
.complete-box{background:#161b27;border:1px solid #1e1e2e;border-radius:14px;padding:24px}
.check-label{display:flex;align-items:center;gap:10px;font-size:14px;color:#8b8baa;margin-bottom:16px}
.btn-complete{width:100%;padding:14px;background:#00c97a;color:#000;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px}
.btn-complete:hover{background:#00e88a}
.done-text{color:#00c97a;font-weight:600;font-size:14px;padding:14px;text-align:center}
</style>
</head>
<body>
<?php renderLayout('dashboard', $fl_name, $fl_user, $notif_count); ?>
<div class="main">
    <a href="course_detail.php?id=<?= $course_id ?>" class="back-btn">← Back</a>
    <div class="card">
        <h2><?= htmlspecialchars($course['title']) ?></h2>
        <div class="video-box">
            <div class="play-icon">▶️</div>
            <div class="video-title"><?= htmlspecialchars($course['title']) ?></div>
            <div class="video-meta"><?= htmlspecialchars($course['category']) ?> · <?= $course['duration_hrs'] ?> hrs · <?= $course['level'] ?></div>
            <a href="<?= htmlspecialchars($course['youtube_url']) ?>" target="_blank" class="btn-youtube">🎬 Watch on YouTube</a>
            <div class="hint">Click the button above → Watch the full video → Come back and mark as complete</div>
        </div>
    </div>
    <div class="complete-box">
        <?php if ($completed): ?>
            <div class="done-text">✅ You have already completed this course!</div>
        <?php else: ?>
            <div class="check-label">☑ Watched the full video? Mark it as complete to unlock your project task.</div>
            <form method="POST">
                <button type="submit" name="mark_complete" class="btn-complete">☑ I've Watched — Mark as Complete</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
