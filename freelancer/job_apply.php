<?php
require_once 'auth.php';
require_once 'layout.php';

$job_id = (int)($_GET['id'] ?? 0);

// Fetch job
$job = $pdo->prepare("
    SELECT j.*, c.company_name AS client_company 
    FROM jobs j 
    JOIN clients c ON j.client_id=c.id 
    WHERE j.id=? AND j.status='open'
");
$job->execute([$job_id]);
$job = $job->fetch();

if (!$job) { 
    header('Location: apply_jobs.php'); 
    exit; 
}

// Check already applied
$already = $pdo->prepare("SELECT id FROM applications WHERE freelancer_id=? AND job_id=?");
$already->execute([$fl_id, $job_id]);
$already = $already->fetch();

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$already) {

    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $education = trim($_POST['education']);
    $portfolio = trim($_POST['portfolio']);
    $resume    = trim($_POST['resume']);
    $skills    = trim($_POST['skills']);

    if (!$full_name || !$email) {
        $error = 'Full Name and Email are required.';
    } else {

        // Insert application
        $stmt = $pdo->prepare("
            INSERT INTO applications 
            (job_id, freelancer_id, full_name, email, phone, education, portfolio, resume_filename, skills, status, applied_at)
            VALUES (?,?,?,?,?,?,?,?,?,'pending',NOW())
        ");

        $stmt->execute([
            $job_id,
            $fl_id,
            $full_name,
            $email,
            $phone,
            $education,
            $portfolio,
            $resume,
            $skills
        ]);

        // 🔥 FIXED NOTIFICATION INSERT
        $pdo->prepare("
            INSERT INTO notifications (message, created_at)
            VALUES (?, NOW())
        ")->execute([
            "New application from $full_name for '{$job['title']}'"
        ]);

        $success = 'Application submitted successfully!';
        $already = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Apply – <?= htmlspecialchars($job['title']) ?></title>

<style>
.back-btn{display:inline-block;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px;margin-bottom:20px}
.back-btn:hover{border-color:#555;color:#fff}
.job-info{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:22px 24px;margin-bottom:20px}
.job-title{font-size:22px;font-weight:700;margin-bottom:6px}
.job-meta{font-size:14px;color:#8b8baa;margin-bottom:8px}
.skill-tag{background:#1e1e2e;color:#8b8baa;padding:3px 10px;border-radius:4px;font-size:12px;display:inline-block;margin-right:5px}
.job-desc{font-size:14px;color:#aaa;margin-top:10px;line-height:1.7}
.apply-card{background:#161b27;border:1px solid #1e1e2e;border-radius:12px;padding:24px}
.apply-title{font-size:18px;font-weight:700;margin-bottom:20px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{margin-bottom:16px}
label{display:block;font-size:12px;font-weight:600;letter-spacing:.08em;color:#8b8baa;margin-bottom:6px;text-transform:uppercase}
label span{color:#e74c3c}
input{width:100%;background:#0d1117;border:1px solid #1e1e2e;border-radius:8px;padding:12px 14px;color:#e6edf3;font-size:14px;outline:none;transition:.2s}
input:focus{border-color:#f59e0b}
.btn-submit{padding:12px 24px;background:#f59e0b;color:#000;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer}
.btn-submit:hover{background:#fbbf24}
.success{background:#0d3d2a;border:1px solid #00c97a;color:#00c97a;padding:12px 16px;border-radius:8px;margin-bottom:16px}
.error{background:#3d1515;border:1px solid #7a2020;color:#ff6b6b;padding:12px 16px;border-radius:8px;margin-bottom:16px}
.already-applied{background:#1e1a2e;border:1px solid #7c3aed;color:#7c3aed;padding:12px 16px;border-radius:8px}
</style>
</head>

<body>

<?php renderLayout('apply', $fl_name, $fl_user, $notif_count); ?>

<div class="main">

<a href="apply_jobs.php" class="back-btn">← Back</a>

<div class="job-info">
    <div class="job-title"><?= htmlspecialchars($job['title']) ?></div>
    <div class="job-meta">
        <?= htmlspecialchars($job['client_company']) ?> · 
        <?= $job['work_mode'] ?> · 
        ₹<?= number_format($job['salary']) ?>/mo
    </div>

    <?php foreach (array_filter(array_map('trim', explode(',', $job['skills']))) as $sk): ?>
        <span class="skill-tag"><?= htmlspecialchars($sk) ?></span>
    <?php endforeach; ?>

    <div class="job-desc"><?= nl2br(htmlspecialchars($job['description'])) ?></div>
</div>

<div class="apply-card">
    <div class="apply-title">Apply for this Position</div>

    <?php if ($success): ?>
        <div class="success">✅ <?= $success ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($already && !$success): ?>
        <div class="already-applied">✅ You have already applied.</div>

    <?php elseif (!$already): ?>

    <form method="POST">

        <div class="form-row">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone">
            </div>
            <div class="form-group">
                <label>Education</label>
                <input type="text" name="education">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Portfolio</label>
                <input type="text" name="portfolio">
            </div>
            <div class="form-group">
                <label>Resume File Name</label>
                <input type="text" name="resume">
            </div>
        </div>

        <div class="form-group">
            <label>Skills</label>
            <input type="text" name="skills">
        </div>

        <button type="submit" class="btn-submit">Submit Application</button>

    </form>

    <?php endif; ?>

</div>
</div>

</body>
</html>
