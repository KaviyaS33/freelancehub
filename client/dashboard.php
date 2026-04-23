<?php
// client/dashboard.php

session_start();

if (empty($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
require_once 'layout.php';

$client_id = $_SESSION['client_id'];

// Safe session values
$client_username = $_SESSION['client_username'] ?? 'Client';
$client_company  = $_SESSION['client_company'] ?? '';
$display_name    = !empty($client_company) ? $client_company : $client_username;

// ==============================
// STATS
// ==============================

// Total Jobs
$stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE client_id=?");
$stmt->execute([$client_id]);
$total_jobs = (int)$stmt->fetchColumn();

// Total Applicants
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM applications a 
    JOIN jobs j ON a.job_id=j.id 
    WHERE j.client_id=?
");
$stmt->execute([$client_id]);
$total_applicants = (int)$stmt->fetchColumn();

// Active Jobs
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM jobs 
    WHERE client_id=? AND status='open'
");
$stmt->execute([$client_id]);
$active_jobs = (int)$stmt->fetchColumn();

// Total Paid
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount),0) 
    FROM payments 
    WHERE client_id=?
");
$stmt->execute([$client_id]);
$total_paid = (float)$stmt->fetchColumn();

// ==============================
// NOTIFICATIONS
// ==============================
$notif_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM notifications 
    WHERE user_id=? AND user_type='client' AND is_read=0
");
$notif_stmt->execute([$client_id]);
$notif_count = (int)$notif_stmt->fetchColumn();

// ==============================
// LOAD LAYOUT
// ==============================
clientLayout('dashboard', $display_name, $notif_count);
?>

<!-- PAGE CONTENT -->

<h1 style="font-size:24px; margin-bottom:20px;">
    Welcome, <?= htmlspecialchars($display_name) ?> 👋
</h1>

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:25px;">

    <div style="background:#1a1a1a;padding:20px;border-radius:12px;">
        <div style="font-size:22px;">💼</div>
        <h2><?= $total_jobs ?></h2>
        <p>Jobs Posted</p>
    </div>

    <div style="background:#1a1a1a;padding:20px;border-radius:12px;">
        <div style="font-size:22px;">👥</div>
        <h2><?= $total_applicants ?></h2>
        <p>Applicants</p>
    </div>

    <div style="background:#1a1a1a;padding:20px;border-radius:12px;">
        <div style="font-size:22px;">🟢</div>
        <h2><?= $active_jobs ?></h2>
        <p>Active Jobs</p>
    </div>

    <div style="background:#1a1a1a;padding:20px;border-radius:12px;">
        <div style="font-size:22px;">💰</div>
        <h2>₹<?= number_format($total_paid) ?></h2>
        <p>Total Paid</p>
    </div>

</div>

<div style="display:flex;gap:15px;flex-wrap:wrap;">

    <a href="post_job.php" style="padding:10px 20px;background:#22c55e;color:#fff;border-radius:8px;text-decoration:none;">
        📄 Post Job
    </a>

    <a href="posted_jobs.php" style="padding:10px 20px;border:1px solid #444;border-radius:8px;color:#fff;text-decoration:none;">
        🧱 View Jobs
    </a>

    <a href="manage_earnings.php" style="padding:10px 20px;border:1px solid #444;border-radius:8px;color:#fff;text-decoration:none;">
        💰 Manage Earnings
    </a>

</div>

</div> <!-- END main from layout -->