<?php
require_once 'auth.php';
require_once 'layout.php';

// Handle delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM courses WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: view_courses.php'); exit;
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Courses – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:4px}
.count{color:#8b8baa;font-size:14px;margin-bottom:24px}
.courses-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.course-card{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;padding:20px;transition:.15s}
.course-card:hover{border-color:#2e1e4a}
.course-icon{font-size:26px;margin-bottom:10px}
.course-title{font-size:15px;font-weight:700;margin-bottom:4px}
.course-meta{font-size:12px;color:#8b8baa;margin-bottom:6px}
.course-desc{font-size:12px;color:#666;line-height:1.6;margin-bottom:14px}
.actions{display:flex;gap:8px}
.btn-view{padding:7px 14px;background:#1e1a2e;color:#8b8baa;border:1px solid #2a1e4a;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px}
.btn-view:hover{color:#fff}
.btn-edit{padding:7px 14px;background:#7c3aed;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px}
.btn-edit:hover{background:#8b5cf6}
.btn-delete{padding:7px 12px;background:#e74c3c;color:#fff;border:none;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px;cursor:pointer}
.btn-delete:hover{background:#c0392b}
.empty{color:#8b8baa;text-align:center;padding:60px;font-size:15px}
</style>
</head>
<body>
<?php adminLayout('courses', $admin_user, $notif_count); ?>
<div class="main">
    <h1>All Courses</h1>
    <p class="count"><?= count($courses) ?> courses</p>
    <?php if (empty($courses)): ?>
        <p class="empty">No courses yet. <a href="add_course.php" style="color:#7c3aed">Add one</a>.</p>
    <?php else: ?>
    <div class="courses-grid">
        <?php foreach ($courses as $c): ?>
        <div class="course-card">
            <div class="course-icon">📚</div>
            <div class="course-title"><?= htmlspecialchars($c['title']) ?></div>
            <div class="course-meta"><?= htmlspecialchars($c['category']) ?> · <?= $c['level'] ?> · <?= $c['duration_hrs'] ?> hrs</div>
            <div class="course-desc"><?= htmlspecialchars(substr($c['description'], 0, 80)) ?>...</div>
            <div class="actions">
                <a href="course_view.php?id=<?= $c['id'] ?>" class="btn-view">👁 View</a>
                <a href="edit_course.php?id=<?= $c['id'] ?>" class="btn-edit">✏️ Edit</a>
                <a href="view_courses.php?delete=<?= $c['id'] ?>" class="btn-delete" onclick="return confirm('Delete this course?')">🗑</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
</body>
</html>
