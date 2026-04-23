<?php
session_start();

if (empty($_SESSION['client_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../db.php';
require_once 'layout.php';

$client_id   = $_SESSION['client_id'];
$client_name = $_SESSION['client_username'] ?? 'Client';

$success = '';
$error   = '';

// ==============================
// PAYMENT
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {

    $job_id        = (int)$_POST['job_id'];
    $freelancer_id = (int)$_POST['freelancer_id'];
    $amount        = (float)$_POST['amount'];

    if ($amount <= 0) {
        $error = "Enter valid amount";
    } else {
        $pdo->prepare("INSERT INTO payments (job_id, client_id, freelancer_id, amount, note)
                       VALUES (?, ?, ?, ?, ?)")
            ->execute([$job_id, $client_id, $freelancer_id, $amount, $_POST['note'] ?? '']);

        // Freelancer-க்கு notification
        $pdo->prepare("INSERT INTO notifications (user_id, user_type, message) VALUES (?, 'freelancer', ?)")
            ->execute([$freelancer_id, "💰 ₹".number_format($amount)." payment received!"]);

        // Admin-க்கு notification
        $admins = $pdo->query("SELECT id FROM admins")->fetchAll();
        foreach ($admins as $adm) {
            $pdo->prepare("INSERT INTO notifications (user_id, user_type, message) VALUES (?, 'admin', ?)")
                ->execute([$adm['id'], "💳 Client paid ₹".number_format($amount)." to freelancer ID $freelancer_id"]);
        }

        $success = "₹" . number_format($amount) . " paid successfully!";
    }
}

// ==============================
// FETCH JOBS
// ==============================
$stmt = $pdo->prepare("
    SELECT j.*, 
           COALESCE(SUM(p.amount),0) as total_paid,
           COUNT(a.id) as applicants
    FROM jobs j
    LEFT JOIN payments p ON p.job_id=j.id
    LEFT JOIN applications a ON a.job_id=j.id
    WHERE j.client_id=?
    GROUP BY j.id
    ORDER BY j.created_at DESC
");
$stmt->execute([$client_id]);
$jobs = $stmt->fetchAll();

// ==============================
// SELECTED JOB
// ==============================
$selected_job_id = $_GET['job_id'] ?? null;
$applicants = [];
$selected_job = null;

if ($selected_job_id) {
    $sj = $pdo->prepare("SELECT * FROM jobs WHERE id=? AND client_id=?");
    $sj->execute([$selected_job_id, $client_id]);
    $selected_job = $sj->fetch();

    if ($selected_job) {
        $ap = $pdo->prepare("
            SELECT f.id, f.full_name, f.skills, f.email
            FROM applications a
            JOIN freelancers f ON f.id=a.freelancer_id
            WHERE a.job_id=? AND a.status='accepted'
        ");
        $ap->execute([$selected_job_id]);
        $applicants = $ap->fetchAll();
    }
}

// ==============================
// NOTIFICATIONS
// ==============================
$n = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id=? AND user_type='client' AND is_read=0");
$n->execute([$client_id]);
$notif_count = $n->fetchColumn();

// ==============================
// LOAD LAYOUT
// ==============================
clientLayout('earnings', $client_name, $notif_count);
?>

<!-- PAGE CONTENT -->

<h1 style="font-size:28px;font-weight:800;margin-bottom:5px;">💰 Manage Earnings</h1>
<p style="color:#9ca3af;margin-bottom:25px;">Pay freelancers for completed work</p>

<?php if ($success): ?>
<div style="background:#052e16;color:#86efac;padding:10px;border-radius:8px;margin-bottom:15px;">
    ✅ <?= $success ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div style="background:#3b0000;color:#f87171;padding:10px;border-radius:8px;margin-bottom:15px;">
    ❌ <?= $error ?>
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:25px;">

    <!-- LEFT SIDE -->
    <div>
        <h3 style="margin-bottom:15px;">1. Select a Job</h3>

        <?php foreach ($jobs as $job): ?>
        <a href="manage_earnings.php?job_id=<?= $job['id'] ?>" style="text-decoration:none;">
            <div style="
                background:#1a1a1a;
                padding:18px;
                border-radius:12px;
                margin-bottom:12px;
                border:1px solid <?= ($selected_job_id==$job['id']) ? '#22c55e' : '#2a2a2a' ?>;
                cursor:pointer;
            ">
                <h3 style="color:#fff;"><?= htmlspecialchars($job['title']) ?></h3>
                <p style="color:#aaa;font-size:13px;">
                    <?= htmlspecialchars($job['company_name']) ?> · ₹<?= number_format($job['salary']) ?>
                </p>
                <div style="margin-top:6px;">
                    <span style="background:#1e2a4a;color:#93c5fd;padding:4px 8px;border-radius:12px;font-size:12px;">
                        <?= $job['applicants'] ?> applicants
                    </span>
                </div>
                <p style="margin-top:8px;color:#f59e0b;font-size:13px;">
                    Paid: ₹<?= number_format($job['total_paid']) ?>
                </p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- RIGHT SIDE -->
    <div>

        <?php if (!$selected_job): ?>
        <div style="background:#1a1a1a;padding:50px;border-radius:12px;text-align:center;color:#9ca3af;">
            <div style="font-size:40px;">👉</div>
            <h3>Select a job from the left</h3>
            <p>Then choose a freelancer and send payment</p>
        </div>

        <?php elseif (empty($applicants)): ?>
        <div style="background:#1a1a1a;padding:50px;border-radius:12px;text-align:center;color:#9ca3af;">
            <div style="font-size:40px;">👤</div>
            <h3>No freelancers yet</h3>
            <p>Accept applicants first</p>
        </div>

        <?php else: ?>

        <h3 style="margin-bottom:15px;">2. Pay for: <?= htmlspecialchars($selected_job['title']) ?></h3>

        <div style="background:#1a1a2e;border:1px solid #2a2a4a;border-radius:12px;padding:24px;">
            <form method="POST">
                <input type="hidden" name="job_id" value="<?= $selected_job['id'] ?>">

                <!-- SELECT FREELANCER -->
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;margin-bottom:8px;">
                        SELECT FREELANCER *
                    </label>
                    <select name="freelancer_id" required style="
                        width:100%;
                        background:#0d1117;
                        border:1px solid #2a2a4a;
                        border-radius:8px;
                        padding:12px 14px;
                        color:#e6edf3;
                        font-size:14px;
                        outline:none;
                    ">
                        <?php foreach ($applicants as $fl): ?>
                        <option value="<?= $fl['id'] ?>">
                            <?= htmlspecialchars($fl['full_name']) ?> — <?= htmlspecialchars($fl['email'] ?? '') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- AMOUNT -->
                <div style="margin-bottom:18px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;margin-bottom:8px;">
                        AMOUNT (₹) *
                    </label>
                    <input type="number" name="amount" placeholder="e.g. 80000" required style="
                        width:100%;
                        background:#0d1117;
                        border:1px solid #2a2a4a;
                        border-radius:8px;
                        padding:12px 14px;
                        color:#e6edf3;
                        font-size:14px;
                        outline:none;
                    ">
                </div>

                <!-- PAYMENT NOTE -->
                <div style="margin-bottom:24px;">
                    <label style="display:block;font-size:11px;font-weight:600;letter-spacing:.08em;color:#8b8baa;text-transform:uppercase;margin-bottom:8px;">
                        PAYMENT NOTE (OPTIONAL)
                    </label>
                    <input type="text" name="note" placeholder="e.g. Month 1 salary, Project milestone" style="
                        width:100%;
                        background:#0d1117;
                        border:1px solid #2a2a4a;
                        border-radius:8px;
                        padding:12px 14px;
                        color:#e6edf3;
                        font-size:14px;
                        outline:none;
                    ">
                </div>

                <!-- SUBMIT -->
                <button name="pay" type="submit" style="
                    width:100%;
                    background:#22c55e;
                    border:none;
                    padding:14px;
                    border-radius:10px;
                    color:#fff;
                    font-size:15px;
                    font-weight:700;
                    cursor:pointer;
                ">
                    🚀 Send Payment & Notify All
                </button>

            </form>
        </div>

        <?php endif; ?>

    </div>

</div>

</div> <!-- END MAIN -->