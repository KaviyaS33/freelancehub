<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get values safely
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Debug (remove later if not needed)
    // echo $username . " - " . $password;

    // Simple static login check
    if ($username === 'admin' && $password === 'admin123') {

        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_user'] = 'admin';

        header("Location: dashboard.php");
        exit;

    } else {
        $error = "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login – FreelanceHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0d0d14;color:#e6edf3;font-family:'Segoe UI',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:radial-gradient(ellipse at 20% 80%,#130d2e 0%,#0d0d14 65%)}
.card{background:#161b27;border:1px solid #2a1e4a;border-radius:18px;padding:44px 40px;width:100%;max-width:480px}
.back-btn{display:inline-block;margin-bottom:24px;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px}
.back-btn:hover{border-color:#555;color:#fff}
.logo{text-align:center;font-size:46px;margin-bottom:10px}
h2{text-align:center;font-size:26px;font-weight:700;margin-bottom:4px}
.subtitle{text-align:center;color:#666;font-size:14px;margin-bottom:28px}
.error{background:#3d1515;border:1px solid #7a2020;color:#ff6b6b;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
.form-group{margin-bottom:18px}
label{display:block;font-size:12px;font-weight:600;letter-spacing:.08em;color:#888;margin-bottom:7px;text-transform:uppercase}
input{width:100%;background:#0d1117;border:1px solid #2a1e4a;border-radius:8px;padding:13px 14px;color:#fff;font-size:15px;outline:none;transition:.2s}
input:focus{border-color:#7c3aed}
.btn{width:100%;padding:14px;background:#7c3aed;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;margin-top:4px}
.btn:hover{background:#8b5cf6}
</style>
</head>
<body>
<div class="card">
    <a href="../index.php" class="back-btn">← Back</a>
    <div class="logo">🛡️</div>
    <h2>Admin Login</h2>
    <p class="subtitle">FreelanceHub Platform</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn">Sign In →</button>
    </form>
</div>
</body>
</html>
