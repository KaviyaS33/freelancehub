<?php
require_once 'auth.php';
require_once 'layout.php';

// Handle delete
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM freelancers WHERE id=?")->execute([(int)$_GET['delete']]);
    header('Location: view_students.php'); exit;
}

$students = $pdo->query("
    SELECT f.*,
        (SELECT COUNT(*) FROM course_progress WHERE freelancer_id=f.id AND status='completed') AS completed,
        (SELECT COUNT(*) FROM course_progress WHERE freelancer_id=f.id AND status='ongoing') AS ongoing,
        (SELECT COALESCE(SUM(amount),0) FROM payments WHERE freelancer_id=f.id) AS total_earned
    FROM freelancers f
    WHERE f.status='approved'
    ORDER BY f.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Students – FreelanceHub</title>
<style>
h1{font-size:26px;font-weight:700;margin-bottom:4px}
.count{color:#8b8baa;font-size:14px;margin-bottom:24px}
.table-wrap{background:#161b27;border:1px solid #1e1a2e;border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}
thead tr{background:#1e1a2e}
th{padding:13px 16px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#8b8baa;text-align:left}
td{padding:13px 16px;font-size:14px;border-bottom:1px solid #1e1a2e}
tr:last-child td{border-bottom:none}
tr:hover td{background:#1a1730}
.name-text{font-weight:600}
.city-text{font-size:12px;color:#8b8baa}
.skill-tag{background:#1e1a2e;color:#8b8baa;padding:3px 8px;border-radius:4px;font-size:11px;display:inline-block;margin-right:4px}
.badge{width:26px;height:26px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:700}
.badge-green{background:#0d3d2a;color:#00c97a}
.badge-purple{background:#1e1a2e;color:#7c3aed}
.earned-text{color:#f59e0b;font-weight:600}
.btn-del{background:#e74c3c;color:#fff;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:12px;text-decoration:none}
.btn-del:hover{background:#c0392b}
.empty{color:#8b8baa;text-align:center;padding:40px;font-size:14px}
</style>
</head>
<body>
<?php adminLayout('students', $admin_user, $notif_count); ?>
<div class="main">
    <h1>View Students</h1>
    <p class="count"><?= count($students) ?> registered freelancers</p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Skills</th>
                    <th>Completed</th>
                    <th>Ongoing</th>
                    <th>Total Earned</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
                <tr><td colspan="7" class="empty">No students yet.</td></tr>
            <?php else: foreach ($students as $s): ?>
            <tr>
                <td>
                    <div class="name-text"><?= htmlspecialchars($s['full_name'] ?: $s['username']) ?></div>
                    <div class="city-text"><?= htmlspecialchars($s['city'] ?? '') ?></div>
                </td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?php foreach (array_slice(array_filter(array_map('trim', explode(',', $s['skills'] ?? ''))), 0, 3) as $sk): ?><span class="skill-tag"><?= htmlspecialchars($sk) ?></span><?php endforeach; ?></td>
                <td><span class="badge badge-green"><?= $s['completed'] ?></span></td>
                <td><span class="badge badge-purple"><?= $s['ongoing'] ?></span></td>
                <td><span class="earned-text">₹<?= number_format($s['total_earned']) ?></span></td>
                <td><a href="view_students.php?delete=<?= $s['id'] ?>" class="btn-del" onclick="return confirm('Delete this student?')">🗑</a></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
