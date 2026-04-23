<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../db.php';

$error = '';
$step  = $_SESSION['reg_step'] ?? 1;

/* ================= STEP 1 ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step1'])) {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if (!$username || !$email || !$password || !$confirm) {
        $error = "Fill all required fields";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
    } else {

        $_SESSION['reg_data'] = [
            'username' => $username,
            'email'    => $email,
            'phone'    => $phone,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        $_SESSION['reg_step'] = 2;

        // 🔥 IMPORTANT: redirect to same page to load step 2
        header("Location: register.php");
        exit;
    }
}

/* ================= STEP 2 ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step2'])) {

    if (!isset($_SESSION['reg_data'])) {
        $error = "Session expired";
        $_SESSION['reg_step'] = 1;
        header("Location: register.php");
        exit;
    }

    $d = $_SESSION['reg_data'];

    $stmt = $pdo->prepare("
        INSERT INTO clients
        (username,email,phone,password,company_name,industry,gst_number,cin_number,website,founded_year,company_size,status,created_at)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,'pending',NOW())
    ");

    $ok = $stmt->execute([
        $d['username'],
        $d['email'],
        $d['phone'],
        $d['password'],
        $_POST['company_name'],
        $_POST['industry'],
        $_POST['gst_number'],
        $_POST['cin_number'],
        $_POST['website'],
        $_POST['founded_year'],
        $_POST['company_size']
    ]);

    if ($ok) {
        unset($_SESSION['reg_data'], $_SESSION['reg_step']);
        header("Location: success.php");
        exit;
    } else {
        $error = "Registration failed";
    }
}

/* BACK BUTTON */
if (isset($_GET['back'])) {
    $_SESSION['reg_step'] = 1;
    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Client Register</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Segoe UI;
}

body{
    background: radial-gradient(circle at top, #0f172a, #020617);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    color:#fff;
}

.card{
    width:420px;
    background:#0b0f19;
    border:1px solid #1f2937;
    border-radius:18px;
    padding:30px;
}

.back{
    color:#9ca3af;
    text-decoration:none;
    font-size:13px;
}

h2{
    margin-top:10px;
    margin-bottom:5px;
}

.step{
    font-size:13px;
    color:#9ca3af;
    margin-bottom:20px;
}

.row{
    display:flex;
    gap:12px;
}

.group{
    flex:1;
    margin-bottom:15px;
}

label{
    font-size:11px;
    color:#9ca3af;
}

input, select{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:10px;
    border:1px solid #1f2937;
    background:#020617;
    color:#fff;
}

.btn{
    width:100%;
    padding:14px;
    background:#10b981;
    border:none;
    border-radius:12px;
    color:#fff;
    font-weight:600;
    cursor:pointer;
}

.err{
    background:#3b0000;
    color:#f87171;
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
}
</style>

</head>
<body>

<div class="card">

<?php if ($step == 1): ?>

<a href="login.php" class="back">← Back</a>

<h2>Client Registration</h2>
<p class="step">Step 1 of 2 — Account Details</p>

<?php if($error): ?><div class="err"><?= $error ?></div><?php endif; ?>

<form method="POST">

<div class="row">
<div class="group">
<label>USERNAME *</label>
<input name="username" required value="<?= $_SESSION['reg_data']['username'] ?? '' ?>">
</div>

<div class="group">
<label>PHONE</label>
<input name="phone" value="<?= $_SESSION['reg_data']['phone'] ?? '' ?>">
</div>
</div>

<div class="group">
<label>EMAIL *</label>
<input name="email" required value="<?= $_SESSION['reg_data']['email'] ?? '' ?>">
</div>

<div class="row">
<div class="group">
<label>PASSWORD *</label>
<input type="password" name="password" required>
</div>

<div class="group">
<label>CONFIRM *</label>
<input type="password" name="confirm" required>
</div>
</div>

<button type="submit" name="step1" value="1" class="btn">Continue →</button>

</form>

<?php else: ?>

<a href="?back=1" class="back">← Back</a>

<h2>Client Registration</h2>
<p class="step">Step 2 of 2 — Company Verification</p>

<?php if($error): ?><div class="err"><?= $error ?></div><?php endif; ?>

<form method="POST">

<div class="row">
<div class="group">
<label>COMPANY NAME *</label>
<input name="company_name" required>
</div>

<div class="group">
<label>INDUSTRY</label>
<input name="industry">
</div>
</div>

<div class="row">
<div class="group">
<label>GST NUMBER *</label>
<input name="gst_number" required>
</div>

<div class="group">
<label>CIN NUMBER</label>
<input name="cin_number">
</div>
</div>

<div class="row">
<div class="group">
<label>WEBSITE</label>
<input name="website">
</div>

<div class="group">
<label>FOUNDED YEAR</label>
<input name="founded_year">
</div>
</div>

<div class="group">
<label>COMPANY SIZE</label>
<select name="company_size">
<option value="">Select...</option>
<option>1-10</option>
<option>11-50</option>
<option>51-200</option>
<option>200+</option>
</select>
</div>

<button type="submit" name="step2" value="1" class="btn">
Submit for Approval →
</button>

</form>

<?php endif; ?>

</div>

</body>
</html>