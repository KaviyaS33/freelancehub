<?php
// client/posted_jobs.php

session_start();

if (empty($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
require_once 'layout.php';

$client_id = $_SESSION['client_id'];
$client_name = $_SESSION['client_username'] ?? 'Client';

// ==============================
// TOGGLE JOB STATUS
// ==============================
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $job_id = (int)$_GET['toggle'];

    $cur = $pdo->prepare("SELECT status FROM jobs WHERE id=? AND client_id=?");
    $cur->execute([$job_id, $client_id]);
    $row = $cur->fetch();

    if ($row) {
        $new = $row['status'] === 'open' ? 'closed' : 'open';
        $upd = $pdo->prepare("UPDATE jobs SET status=? WHERE id=?");
        $upd->execute([$new, $job_id]);
    }

    header('Location: posted_jobs.php');
    exit;
}

// ==============================
// FETCH JOBS
// ==============================
$jobs = $pdo->prepare("
    SELECT j.*, COUNT(a.id) AS applicant_count
    FROM jobs j
    LEFT JOIN applications a ON a.job_id = j.id
    WHERE j.client_id = ?
    GROUP BY j.id
    ORDER BY j.created_at DESC
");
$jobs->execute([$client_id]);
$all_jobs = $jobs->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// NOTIFICATIONS
// ==============================
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM notifications 
    WHERE user_id=? AND user_type='client' AND is_read=0
");
$stmt->execute([$client_id]);
$notif_count = (int)$stmt->fetchColumn();

// ==============================
// LOAD LAYOUT
// ==============================
clientLayout('jobs', $client_name, $notif_count);
?>

<!-- PAGE CONTENT -->

<h1 style="font-size:24px; margin-bottom:20px;">📋 Posted Jobs</h1>

<?php if (empty($all_jobs)): ?>

<div style="background:#1a1a1a;padding:30px;border-radius:12px;text-align:center;">
    <h3>No jobs posted yet</h3>
    <a href="post_job.php" style="color:#22c55e;">Post your first job</a>
</div>

<?php else: ?>

<div style="display:flex;flex-direction:column;gap:15px;">

<?php foreach ($all_jobs as $job): ?>

<div style="background:#1a1a1a;padding:20px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;">

    <!-- LEFT -->
    <div>
        <h3><?= htmlspecialchars($job['title']) ?></h3>

        <p style="color:#aaa;font-size:14px;">
            <?= htmlspecialchars($job['company_name'] ?? 'Company') ?> ·
            <?= htmlspecialchars($job['work_mode'] ?? 'Remote') ?> ·
            ₹<?= number_format($job['salary']) ?>
        </p>

        <!-- SKILLS -->
        <div style="margin-top:8px;">
            <?php 
            $skills = explode(',', $job['skills'] ?? '');
            foreach ($skills as $sk): 
                if (trim($sk) !== ''):
            ?>
                <span style="background:#1e3a5f;color:#93c5fd;padding:3px 8px;border-radius:12px;font-size:12px;">
                    <?= htmlspecialchars(trim($sk)) ?>
                </span>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <!-- RIGHT -->
    <div style="text-align:right;">

        <div style="margin-bottom:8px;">
            <span style="background:#222;padding:5px 10px;border-radius:10px;font-size:12px;">
                👥 <?= $job['applicant_count'] ?> applicants
            </span>
        </div>

        <a href="posted_jobs.php?toggle=<?= $job['id'] ?>"
           style="
            padding:6px 12px;
            border-radius:10px;
            text-decoration:none;
            font-size:12px;
            color:#fff;
            background:<?= $job['status']=='open' ? '#16a34a' : '#d97706' ?>;
           ">
            <?= ucfirst($job['status']) ?>
        </a>

    </div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</div> <!-- END layout main -->