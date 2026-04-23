<?php
// client/auth.php  — handles login, register step 1 & 2, and logout routing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../db.php';

$action = $_GET['action'] ?? 'login';
$error  = '';
$success = '';

// ── LOGOUT ───────────────────────────────────────────────────────────────────
if ($action === 'logout') {
    session_destroy();
    header('Location: auth.php?action=login');
    exit;
}

// ── LOGIN ─────────────────────────────────────────────────────────────────────
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE username = ? AND status = 'approved'");
    $stmt->execute([$username]);
    $client = $stmt->fetch();

    if ($client && password_verify($password, $client['password'])) {
        $_SESSION['client_id']       = $client['id'];
        $_SESSION['client_username'] = $client['username'];
        $_SESSION['client_company']  = $client['company_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid credentials or account not approved.';
    }
}

// ── REGISTER STEP 1 ───────────────────────────────────────────────────────────
if ($action === 'register_step1' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm'] ?? '';

    if (!$username || !$email || !$password) {
        $error = 'Username, email, and password are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check uniqueness
        $chk = $pdo->prepare("SELECT id FROM clients WHERE username=? OR email=?");
        $chk->execute([$username, $email]);
        if ($chk->fetch()) {
            $error = 'Username or email already taken.';
        } else {
            // Store in session for step 2
            $_SESSION['reg_step1'] = compact('username','phone','email','password');
            header('Location: register.php');
            exit;
        }
    }
}

// ── REGISTER STEP 2 ───────────────────────────────────────────────────────────
if ($action === 'register_step2' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['reg_step1'])) {
        header('Location: auth.php?action=register_step1');
        exit;
    }

    $s1           = $_SESSION['reg_step1'];
    $company_name = trim($_POST['company_name'] ?? '');
    $industry     = trim($_POST['industry'] ?? '');
    $gst_number   = trim($_POST['gst_number'] ?? '');
    $cin_number   = trim($_POST['cin_number'] ?? '');
    $website      = trim($_POST['website'] ?? '');
    $founded_year = trim($_POST['founded_year'] ?? '');
    $company_size = $_POST['company_size'] ?? '';

    if (!$company_name || !$gst_number) {
        $error = 'Company name and GST number are required.';
    } else {
        $hash = password_hash($s1['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO clients
            (username,email,phone,password,company_name,industry,gst_number,cin_number,website,founded_year,company_size,status)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,'pending')");
        $stmt->execute([
            $s1['username'], $s1['email'], $s1['phone'], $hash,
            $company_name, $industry, $gst_number, $cin_number,
            $website, $founded_year ?: null, $company_size ?: null,
        ]);
        unset($_SESSION['reg_step1']);
        header('Location: auth.php?action=submitted');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>FreelanceHub — Client</title>
</head>
<body>

<?php if ($action === 'login'): ?>
<!-- ── LOGIN ──────────────────────────────────────────────────────────── -->
<div class="auth-wrapper">
  <div class="auth-card">
    <a href="../index.php" class="back-btn">← Back</a>
    <div class="auth-icon">🏢</div>
    <h1 class="auth-title">Client Login</h1>
    <p class="auth-subtitle">FreelanceHub Platform</p>

    <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>USERNAME</label>
        <input type="text" name="username" placeholder="Enter username" required>
      </div>
      <div class="form-group">
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="Enter password" required>
      </div>
      <button class="btn-primary" type="submit">Sign In →</button>
    </form>

    <p class="auth-link">Don't have an account? <a href="auth.php?action=register_step1">Sign Up</a></p>
  </div>
</div>

<?php elseif ($action === 'register_step1'): ?>
<!-- ── REGISTER STEP 1 ─────────────────────────────────────────────── -->
<div class="auth-wrapper">
  <div class="auth-card">
    <a href="auth.php?action=login" class="back-btn">← Back</a>
    <h1 class="auth-title">Client Registration</h1>
    <p class="step-label">Step 1 of 2 — Account Details</p>

    <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>USERNAME <span style="color:#ef4444">*</span></label>
          <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>PHONE</label>
          <input type="tel" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>EMAIL <span style="color:#ef4444">*</span></label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>PASSWORD <span style="color:#ef4444">*</span></label>
          <input type="password" name="password" required>
        </div>
        <div class="form-group">
          <label>CONFIRM <span style="color:#ef4444">*</span></label>
          <input type="password" name="confirm" required>
        </div>
      </div>
      <button class="btn-primary" type="submit">Continue →</button>
    </form>
  </div>
</div>

<?php elseif ($action === 'register_step2'): ?>
<!-- ── REGISTER STEP 2 ─────────────────────────────────────────────── -->
<div class="auth-wrapper">
  <div class="auth-card">
    <a href="auth.php?action=register_step1" class="back-btn">← Back</a>
    <h1 class="auth-title">Client Registration</h1>
    <p class="step-label">Step 2 of 2 — Company Verification</p>

    <?php if ($error): ?><div class="error-msg"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label>COMPANY NAME <span style="color:#ef4444">*</span></label>
          <input type="text" name="company_name" value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>INDUSTRY</label>
          <input type="text" name="industry" value="<?= htmlspecialchars($_POST['industry'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>GST NUMBER <span style="color:#ef4444">*</span></label>
          <input type="text" name="gst_number" value="<?= htmlspecialchars($_POST['gst_number'] ?? '') ?>" required>
        </div>
        <div class="form-group">
          <label>CIN NUMBER</label>
          <input type="text" name="cin_number" value="<?= htmlspecialchars($_POST['cin_number'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>WEBSITE</label>
          <input type="url" name="website" value="<?= htmlspecialchars($_POST['website'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>FOUNDED YEAR</label>
          <input type="number" name="founded_year" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($_POST['founded_year'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>COMPANY SIZE</label>
        <select name="company_size">
          <option value="">Select...</option>
          <?php foreach (['1-10','11-50','51-200','201-500','500+'] as $s): ?>
          <option value="<?= $s ?>"><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn-primary" type="submit">Submit for Approval →</button>
    </form>
  </div>
</div>

<?php elseif ($action === 'submitted'): ?>
<!-- ── SUBMITTED ───────────────────────────────────────────────────── -->
<div class="success-wrapper">
  <div class="success-card">
    <div class="sc-icon">🎉</div>
    <h2 class="sc-title">Application Submitted!</h2>
    <p class="sc-sub">Admin will review your details. Login after approval.</p>
    <a href="auth.php?action=login" class="btn-primary" style="display:block;text-decoration:none;text-align:center;">Go to Login →</a>
  </div>
</div>
<?php endif; ?>

</body>
</html>
