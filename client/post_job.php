<?php
// client/post_job.php

session_start();

if (empty($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
require_once 'layout.php';

$client_id   = $_SESSION['client_id'];
$client_name = $_SESSION['client_username'] ?? 'Client';

$error = '';
$success = '';

// GET COMPANY NAME
$stmt = $pdo->prepare("SELECT company_name FROM clients WHERE id=?");
$stmt->execute([$client_id]);
$company_name = $stmt->fetchColumn() ?: '';

// FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $company     = trim($_POST['company_name'] ?? $company_name);
    $start_date  = $_POST['start_date'] ?? null;
    $end_date    = $_POST['end_date'] ?? null;
    $salary      = $_POST['salary'] ?? null;
    $work_mode   = $_POST['work_mode'] ?? 'Remote';
    $skills      = trim($_POST['skills'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$title || !$description) {
        $error = "Job title and description are required.";
    } else {

        $stmt = $pdo->prepare("INSERT INTO jobs
        (client_id, title, company_name, start_date, end_date, salary, work_mode, skills, description, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open')");

        $stmt->execute([
            $client_id,
            $title,
            $company,
            $start_date ?: null,
            $end_date ?: null,
            $salary ?: null,
            $work_mode,
            $skills,
            $description
        ]);

        $success = "Job posted successfully!";
    }
}

// NOTIFICATIONS
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM notifications 
    WHERE user_id=? AND user_type='client' AND is_read=0
");
$stmt->execute([$client_id]);
$notif_count = (int)$stmt->fetchColumn();

// LOAD LAYOUT
clientLayout('post_job', $client_name, $notif_count);
?>

<!-- PAGE CONTENT -->

<h1 style="font-size:26px; font-weight:700; margin-bottom:25px;">
    📄 Post a Job
</h1>

<?php if ($error): ?>
<div style="background:#3b0000; color:#f87171; padding:10px; border-radius:10px; margin-bottom:15px;">
    <?= $error ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div style="background:#052e16; color:#86efac; padding:10px; border-radius:10px; margin-bottom:15px;">
    <?= $success ?>
</div>
<?php endif; ?>

<div style="
    background:#1a1a1a;
    padding:25px;
    border-radius:16px;
    max-width:800px;
    border:1px solid #2a2a2a;
">

<form method="POST">

<div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">

<!-- TITLE -->
<div>
<label style="font-size:12px; color:#9ca3af;">JOB TITLE *</label>
<input type="text" name="title" required
style="width:100%; padding:10px; background:#262626; border:1px solid #2a2a2a; border-radius:10px; color:#fff;"
value="<?= $_POST['title'] ?? '' ?>">
</div>

<!-- COMPANY -->
<div>
<label style="font-size:12px; color:#9ca3af;">COMPANY</label>
<input type="text" name="company_name"
style="width:100%; padding:10px; background:#262626; border:1px solid #2a2a2a; border-radius:10px; color:#fff;"
value="<?= $_POST['company_name'] ?? $company_name ?>">
</div>

<!-- START DATE -->
<div>
<label style="font-size:12px; color:#9ca3af;">START DATE</label>
<input type="date" name="start_date"
style="width:100%; padding:10px; background:#262626; border-radius:10px; color:#fff;">
</div>

<!-- END DATE -->
<div>
<label style="font-size:12px; color:#9ca3af;">END DATE</label>
<input type="date" name="end_date"
style="width:100%; padding:10px; background:#262626; border-radius:10px; color:#fff;">
</div>

<!-- SALARY -->
<div>
<label style="font-size:12px; color:#9ca3af;">SALARY</label>
<input type="number" name="salary"
style="width:100%; padding:10px; background:#262626; border-radius:10px; color:#fff;"
value="<?= $_POST['salary'] ?? '' ?>">
</div>

<!-- WORK MODE -->
<div>
<label style="font-size:12px; color:#9ca3af;">WORK MODE</label>
<select name="work_mode"
style="width:100%; padding:10px; background:#262626; border-radius:10px; color:#fff;">
<option>Remote</option>
<option>Hybrid</option>
<option>On-site</option>
</select>
</div>

<!-- SKILLS -->
<div style="grid-column:1/-1;">
<label style="font-size:12px; color:#9ca3af;">SKILLS</label>
<input type="text" name="skills"
style="width:100%; padding:10px; background:#262626; border-radius:10px; color:#fff;"
placeholder="React, Node.js"
value="<?= $_POST['skills'] ?? '' ?>">
</div>

<!-- DESCRIPTION -->
<div style="grid-column:1/-1;">
<label style="font-size:12px; color:#9ca3af;">DESCRIPTION *</label>
<textarea name="description" required
style="width:100%; padding:10px; height:120px; background:#262626; border-radius:10px; color:#fff;">
<?= $_POST['description'] ?? '' ?>
</textarea>
</div>

</div>

<button type="submit" style="
    margin-top:15px;
    background:#22c55e;
    padding:12px 20px;
    border:none;
    border-radius:12px;
    color:#fff;
    font-weight:600;
    cursor:pointer;
">
🚀 Post Job & Notify Freelancers
</button>

</form>

</div>

</div> <!-- END MAIN -->