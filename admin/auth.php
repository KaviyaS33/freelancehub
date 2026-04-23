<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// If not logged in → redirect
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../db.php";

// Get session values
$admin_id   = $_SESSION['admin_id'];
$admin_user = $_SESSION['admin_user'];

// ==============================
// GET ADMIN DETAILS (optional)
// ==============================
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// ==============================
// NOTIFICATION COUNT (FIXED)
// ==============================
$notif_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM notifications 
    WHERE user_type = 'admin' 
    AND user_id = ? 
    AND is_read = 0
");
$notif_stmt->execute([$admin_id]);
$notif_count = (int)$notif_stmt->fetchColumn();

// ==============================
// OPTIONAL: FETCH LATEST NOTIFICATIONS
// ==============================
$notif_list_stmt = $pdo->prepare("
    SELECT * 
    FROM notifications 
    WHERE user_type = 'admin' 
    AND user_id = ?
    ORDER BY created_at DESC
    LIMIT 5
");
$notif_list_stmt->execute([$admin_id]);
$notifications = $notif_list_stmt->fetchAll(PDO::FETCH_ASSOC);

?>