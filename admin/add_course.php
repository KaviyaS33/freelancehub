<?php
require_once 'auth.php';
require_once 'layout.php';

$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title']);
    $category     = trim($_POST['category']);
    $level        = $_POST['level'];
    $youtube_url  = trim($_POST['youtube_url']);
    $duration_hrs = (int)$_POST['duration_hrs'];
    $description  = trim($_POST['description']);
    $project_task = trim($_POST['project_task']);

    if (!$title || !$youtube_url) {
        $error = 'Course Name and YouTube URL are required.';
    } else {
        $pdo->prepare("INSERT INTO courses (title, category, level, youtube_url, duration_hrs, description, project_task, created_at)
            VALUES (?,?,?,?,?,?,?,NOW())")
            ->execute([$title, $category, $level, $youtube_url, $duration_hrs, $description, $project_task]);
        $success = 'Course added successfully!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Course – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:24px}
.card{background:#161b27;border:1px solid #1e1a2e;border-radius:14px;padding:28px;max-width:620px}
.form-group{margin-bottom:18px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
label{display:block;font-size:12px;font-weight:600;letter-spacing:.08em;color:#8b8baa;margin-bottom:7px;text-transform:uppercase}
label span{color:#e74c3c}
input,select,textarea{width:100%;background:#0d1117;border:1px solid #1e1a2e;border-radius:8px;padding:12px 14px;color:#e6edf3;font-size:14px;outline:none;transition:.2s;font-family:inherit}
input:focus,select:focus,textarea:focus{border-color:#7c3aed}
input::placeholder,textarea::placeholder{color:#444}
select option{background:#161b27}
textarea{resize:vertical;min-height:100px}
.btn{padding:13px 26px;background:#7c3aed;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px}
.btn:hover{background:#8b5cf6}
.success{background:#0d3d2a;border:1px solid #00c97a;color:#00c97a;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
.error{background:#3d1515;border:1px solid #7a2020;color:#ff6b6b;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
</style>
</head>
<body>
<?php adminLayout('add_course', $admin_user, $notif_count); ?>
<div class="main">
    <h1>Add Course</h1>
    <?php if ($success): ?><div class="success">✅ <?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label>Course Name <span>*</span></label>
                <input type="text" name="title" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" placeholder="e.g. Web Dev">
                </div>
                <div class="form-group">
                    <label>Level</label>
                    <select name="level">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>YouTube Embed URL <span>*</span></label>
                <input type="url" name="youtube_url" placeholder="https://www.youtube.com/embed/..." required>
            </div>
            <div class="form-group">
                <label>Duration</label>
                <input type="number" name="duration_hrs" placeholder="e.g. 12 hrs" min="0">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Course description..."></textarea>
            </div>
            <div class="form-group">
                <label>Project Task</label>
                <textarea name="project_task" placeholder="Describe the project task after completing the course..."></textarea>
            </div>
            <button type="submit" class="btn">📚 Add Course</button>
        </form>
    </div>
</div>
</body>
</html>
