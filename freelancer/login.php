<?php
session_start();
require_once '../db.php';

if (!empty($_SESSION['freelancer_id'])) {
    header('Location: dashboard.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM freelancers WHERE username=?");
    $stmt->execute([$username]);
    $fl = $stmt->fetch();
    if (!$fl) {
        $error = 'Invalid username or password.';
    } elseif ($fl['status'] === 'pending') {
        $error = 'Your account is pending admin approval.';
    } elseif ($fl['status'] === 'rejected') {
        $error = 'Your account has been rejected.';
    } elseif (!password_verify($password, $fl['password'])) {
        $error = 'Invalid username or password.';
    } else {
        $_SESSION['freelancer_id']   = $fl['id'];
        $_SESSION['freelancer_name'] = $fl['full_name'] ?: $fl['username'];
        $_SESSION['freelancer_user'] = $fl['username'];
        header('Location: dashboard.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Freelancer Login – FreelanceHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0d0d14;color:#e6edf3;font-family:'Segoe UI',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:radial-gradient(ellipse at 30% 80%,#1a1230 0%,#0d0d14 70%)}
.card{background:#161b27;border:1px solid #2a2a3a;border-radius:18px;padding:44px 40px;width:100%;max-width:480px}
.back-btn{display:inline-block;margin-bottom:24px;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px}
.back-btn:hover{border-color:#555;color:#fff}
.logo{text-align:center;font-size:46px;margin-bottom:10px}
h2{text-align:center;font-size:26px;font-weight:700;margin-bottom:4px}
.subtitle{text-align:center;color:#666;font-size:14px;margin-bottom:28px}
.error{background:#3d1515;border:1px solid #7a2020;color:#ff6b6b;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
.form-group{margin-bottom:18px;position:relative}
label{display:block;font-size:12px;font-weight:600;letter-spacing:.08em;color:#888;margin-bottom:7px;text-transform:uppercase}
input{width:100%;background:#0d1117;border:1px solid #2a2a3a;border-radius:8px;padding:13px 44px 13px 14px;color:#fff;font-size:15px;outline:none;transition:.2s}
input:focus{border-color:#f59e0b}
.eye-btn{position:absolute;right:14px;top:38px;background:none;border:none;color:#666;cursor:pointer;font-size:16px}
.eye-btn:hover{color:#aaa}
.btn{width:100%;padding:14px;background:#f59e0b;color:#000;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;margin-top:4px}
.btn:hover{background:#fbbf24}
.signup-link{text-align:center;margin-top:16px;font-size:14px;color:#666}
.signup-link a{color:#f59e0b;text-decoration:none;font-weight:600}
</style>
</head>
<body>
<div class="card">
<a href="/freelancehub/index.php">← Back</a>
    <div class="logo">🧑‍💻</div>
    <h2>Freelancer Login</h2>
    <p class="subtitle">FreelanceHub Platform</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" id="pwd" placeholder="Enter password" required>
            <button type="button" class="eye-btn" onclick="togglePwd()">👁</button>
        </div>
        <button type="submit" class="btn">Sign In →</button>
    </form>
    <p class="signup-link">Don't have an account? <a href="register.php">Sign Up</a></p>
</div>
<script>
function togglePwd(){
    var p=document.getElementById('pwd');
    p.type=p.type==='password'?'text':'password';
}
</script>
</body>
</html>
