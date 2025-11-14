<?php
session_start();
if(!isset($_SESSION['admin_logged_in'])) header("Location: admin_login.php");
require_once "../inc/db.php";

// Handle approve/reject actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    $valid_actions = ['approve'=>'confirmed','reject'=>'cancelled'];
    if(array_key_exists($action, $valid_actions)) {
        $status = $valid_actions[$action];
        $mysqli->query("UPDATE appointments SET status='$status' WHERE id=$id");
    }
    header("Location: manage_appointments.php");
    exit;
}

// Fetch appointments with customer and car info
$appointments = $mysqli->query("
    SELECT a.*, c.fullname AS customer_name, c.email AS customer_email, car.make, car.model
    FROM appointments a
    LEFT JOIN customers c ON a.customer_id = c.id
    LEFT JOIN cars car ON a.car_id = car.id
    ORDER BY a.appointment_date DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Appointments | Car Dealership</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#000; color:#fff; padding:20px; }
h1 { color:#ffcc00; text-align:center; margin-bottom:20px; text-shadow:0 0 10px #ffcc00; }

/* Back to dashboard button */
.back-dashboard { display:inline-block; margin:10px 0 20px 0; padding:10px 20px; background:#ffcc00; color:#000; border-radius:6px; text-decoration:none; font-weight:600; transition:0.3s; }
.back-dashboard:hover { background:#ffd633; transform:translateY(-2px); }

table { width:100%; border-collapse:collapse; margin-top:10px; border-radius:12px; overflow:hidden; box-shadow:0 0 20px rgba(255,204,0,0.3); }
th, td { padding:12px; text-align:center; border-bottom:1px solid #444; }
th { background: linear-gradient(90deg, #111, #222); color: #ffcc00; }
tr:hover { background: rgba(255,204,0,0.1); }
a { color:#ffcc00; text-decoration:none; font-weight:600; }
a:hover { color:#ffd633; }
.action a { padding:5px 10px; background:#ffcc00; color:#000; border-radius:6px; margin:0 3px; display:inline-block; transition:0.3s; }
.action a:hover { background:#ffd633; transform:translateY(-2px); }
.status-pending { color:#ffcc00; font-weight:600; }
.status-confirmed { color:#4CAF50; font-weight:600; }
.status-completed { color:#2196F3; font-weight:600; }
.status-cancelled { color:#f44336; font-weight:600; }
</style>
</head>
<body>

<h1>Manage Appointments</h1>

<!-- BACK TO DASHBOARD BUTTON -->
<a href="admin_dashboard.php" class="back-dashboard"><i class="fas fa-tachometer-alt"></i> Back to Dashboard</a>

<table>
<thead>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Car</th>
<th>Date</th>
<th>Status</th>
<th>Notes</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php if($appointments && $appointments->num_rows > 0): ?>
    <?php while($a = $appointments->fetch_assoc()): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['customer_name'] ?? $a['customer_email']) ?></td>
            <td><?= htmlspecialchars($a['make'].' '.$a['model']) ?></td>
            <td><?= $a['appointment_date'] ?></td>
            <td class="status-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></td>
            <td><?= nl2br(htmlspecialchars($a['notes'])) ?></td>
            <td class="action">
                <?php if($a['status']=='pending'): ?>
                    <a href="?action=approve&id=<?= $a['id'] ?>">Approve</a>
                    <a href="?action=reject&id=<?= $a['id'] ?>">Reject</a>
                <?php else: ?>-
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7">No appointments found.</td></tr>
<?php endif; ?>
</tbody>
</table>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

</body>
</html>
