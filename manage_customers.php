<?php
session_start();
require_once "../inc/db.php";

// Check admin login
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch customers from database
$customers = $mysqli->query("SELECT * FROM customers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Customers | Car Dealership</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #000;
    color: #fff;
    margin: 0;
    padding: 20px;
}
h1 {
    color: #ffcc00;
    text-align: center;
    margin-bottom: 30px;
    text-shadow: 0 0 10px #ffcc00;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 0 20px rgba(255,204,0,0.3);
    border-radius: 12px;
    overflow: hidden;
}
th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #444;
}
th {
    background: linear-gradient(90deg, #111, #222);
    color: #ffcc00;
    text-transform: uppercase;
}
tr:hover {
    background: rgba(255, 204, 0, 0.1);
}
a {
    color: #ffcc00;
    text-decoration: none;
    transition: 0.3s;
}
a:hover {
    color: #ffd633;
}
.button {
    padding: 8px 16px;
    background: #ffcc00;
    color: #000;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
}
.button:hover {
    background: #ffd633;
}
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.logout {
    background: #ff4444 !important;
    color: #fff !important;
}
.logout:hover {
    background: #ff6666 !important;
    box-shadow: 0 0 15px #ff4444;
}
</style>
</head>
<body>

<div class="header">
    <h1>Manage Customers</h1>
    <a href="admin_dashboard.php" class="button">Dashboard</a>
    <a href="admin_logout.php" class="button logout">Logout</a>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Created At</th>
        </tr>
    </thead>
    <tbody>
        <?php if($customers && $customers->num_rows > 0): ?>
            <?php while($c = $customers->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['fullname']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['phone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($c['address'] ?? '-') ?></td>
                    <td><?= $c['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No customers found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
