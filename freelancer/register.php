<?php
session_start();
require_once '../db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $phone     = trim($_POST['phone']);
    $password  = $_POST['password'];
    $confirm   = $_POST['confirm'];
    $education = trim($_POST['education']);
    $experience= trim($_POST['experience']);
    $city      = trim($_POST['city']);
    $portfolio = trim($_POST['portfolio']);
    $skills    = trim($_POST['skills']);

    if (!$username || !$full_name || !$email || !$password || !$confirm) {
        $error = 'Please fill all required fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $chk = $pdo->prepare("SELECT id FROM freelancers WHERE username=? OR email=?");
        $chk->execute([$username, $email]);
        if ($chk->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $pdo->prepare("INSERT INTO freelancers (username, full_name, email, phone, password, education, experience, city, portfolio_url, skills, status, created_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,'approved',NOW())")
                ->execute([$username, $full_name, $email, $phone, password_hash($password, PASSWORD_DEFAULT),
                           $education, $experience, $city, $portfolio, $skills]);
            header('Location: welcome.php'); exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Freelancer Registration – FreelanceHub</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{background:#0d0d14;color:#e6edf3;font-family:'Segoe UI',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:40px 20px;background:radial-gradient(ellipse at 30% 80%,#1a1230 0%,#0d0d14 70%)}
.card{background:#161b27;border:1px solid #2a2a3a;border-radius:18px;padding:40px;width:100%;max-width:600px}
.back-btn{display:inline-block;margin-bottom:20px;padding:8px 16px;border:1px solid #333;border-radius:8px;color:#aaa;text-decoration:none;font-size:13px}
.back-btn:hover{border-color:#555;color:#fff}
h2{font-size:26px;font-weight:700;margin-bottom:4px}
.subtitle{color:#666;font-size:14px;margin-bottom:28px}
.error{background:#3d1515;border:1px solid #7a2020;color:#ff6b6b;padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:14px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{margin-bottom:16px}
label{display:block;font-size:12px;font-weight:600;letter-spacing:.08em;color:#888;margin-bottom:6px;text-transform:uppercase}
label span{color:#e74c3c}
input{width:100%;background:#0d1117;border:1px solid #2a2a3a;border-radius:8px;padding:12px 14px;color:#fff;font-size:14px;outline:none;transition:.2s}
input:focus{border-color:#f59e0b}
input::placeholder{color:#444}
.btn{width:100%;padding:14px;background:#f59e0b;color:#000;border:none;border-radius:10px;font-size:16px;font-weight:700;cursor:pointer;margin-top:8px}
.btn:hover{background:#fbbf24}
</style>
</head>
<body>
<div class="card">
    <a href="login.php" class="back-btn">← Back</a>
    <h2>Freelancer Registration</h2>
    <p class="subtitle">Create your account to start learning and earning.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Username <span>*</span></label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Full Name <span>*</span></label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email <span>*</span></label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Password <span>*</span></label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password <span>*</span></label>
                <input type="password" name="confirm" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Education</label>
                <input type="text" name="education" value="<?= htmlspecialchars($_POST['education'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Experience</label>
                <input type="text" name="experience" value="<?= htmlspecialchars($_POST['experience'] ?? '') ?>">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Portfolio URL</label>
                <input type="text" name="portfolio" value="<?= htmlspecialchars($_POST['portfolio'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Skills (Comma Separated)</label>
            <input type="text" name="skills" placeholder="React, Node.js, Python" value="<?= htmlspecialchars($_POST['skills'] ?? '') ?>">
        </div>
        <button type="submit" class="btn">Create Account →</button>
    </form>
</div>
</body>
</html>
