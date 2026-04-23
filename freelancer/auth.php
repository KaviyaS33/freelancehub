<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['freelancer_id'])) {
    header('Location: login.php'); exit;
}
require_once '../db.php';
$fl_id   = $_SESSION['freelancer_id'];
$fl_name = $_SESSION['freelancer_name'];
$fl_user = $_SESSION['freelancer_user'];

// Fetch full profile
$fl_stmt = $pdo->prepare("SELECT * FROM freelancers WHERE id=?");
$fl_stmt->execute([$fl_id]);
$fl_profile = $fl_stmt->fetch();

// Notification count
$notif_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE id=? AND is_read=0");
$notif_count_stmt->execute([$fl_id]);
$notif_count = $notif_count_stmt->fetchColumn();
?>
