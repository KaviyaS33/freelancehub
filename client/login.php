<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $error = "Enter username & password";
    } else {

        $stmt = $pdo->prepare("SELECT * FROM clients WHERE username=? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            if ($user['status'] !== 'approved') {
                $error = "Account not approved yet";
            } else {
                $_SESSION['client_id'] = $user['id'];
                $_SESSION['client_username'] = $user['username'];

                header("Location: dashboard.php");
                exit;
            }

        } else {
            $error = "Invalid login credentials";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Client Login</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI}

/* BACKGROUND */
body{
    background:linear-gradient(135deg,#020617,#0f172a);
    height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
}

/* CARD */
.login-card{
    width:380px;
    background:#0b1220;
    padding:30px;
    border-radius:18px;
    box-shadow:0 0 40px rgba(0,0,0,0.6);
}

/* HEADER */
.title{
    text-align:center;
    margin-bottom:20px;
}
.title h2{
    margin-top:10px;
}
.title p{
    color:#9ca3af;
    font-size:13px;
}

/* INPUT */
.group{
    margin-bottom:15px;
}
label{
    font-size:12px;
    color:#9ca3af;
}
input{
    width:100%;
    padding:12px;
    margin-top:6px;
    border-radius:10px;
    border:1px solid #1f2937;
    background:#020617;
    color:#fff;
}

/* BUTTON */
.btn{
    width:100%;
    padding:12px;
    background:#10b981;
    border:none;
    border-radius:12px;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
}

/* FOOT */
.footer{
    text-align:center;
    margin-top:15px;
    font-size:13px;
}
.footer a{
    color:#10b981;
    text-decoration:none;
}

/* ERROR */
.err{
    background:#3b0000;
    color:#f87171;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
}

/* BACK */
.back{
    font-size:13px;
    color:#9ca3af;
    text-decoration:none;
}
</style>

</head>

<body>

<div class="login-card">

<a href="/freelancehub/index.php">← Back</a>

<div class="title">
<div style="font-size:30px">🏢</div>
<h2>Client Login</h2>
<p>FreelanceHub Platform</p>
</div>

<?php if($error): ?>
<div class="err"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

<div class="group">
<label>USERNAME</label>
<input type="text" name="username" placeholder="Enter username">
</div>

<div class="group">
<label>PASSWORD</label>
<input type="password" name="password" placeholder="Enter password">
</div>

<button class="btn">Sign In →</button>

</form>

<div class="footer">
Don't have an account? <a href="register.php">Sign Up</a>
</div>

</div>

</body>
</html>