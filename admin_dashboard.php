<?php
session_start();
require_once "../inc/db.php";

// Check admin login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

// Helper function to safely count records
function safeCount($mysqli, $table) {
    try {
        $result = $mysqli->query("SELECT COUNT(*) AS total FROM `$table`");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'];
        }
        return 0;
    } catch (Exception $e) {
        return 0; // if table doesn't exist, return 0
    }
}

// Safe stats fetching
$total_cars = safeCount($mysqli, 'cars');
$total_sales = safeCount($mysqli, 'sales');
$total_appointments = safeCount($mysqli, 'appointments');
$total_customers = safeCount($mysqli, 'customers');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Prevent password manager autocomplete -->
<meta name="autocomplete" content="off">
<title>Admin Dashboard | Car Dealership</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #000;
    color: #fff;
    margin: 0;
    padding: 0;
}
.header {
    background: linear-gradient(90deg, #111, #222);
    padding: 20px 40px;
    box-shadow: 0 0 20px #ffcc00;
    text-align: center;
}
.header h1 {
    margin: 0;
    color: #ffcc00;
    text-shadow: 0 0 10px #ffcc00;
}
.container {
    padding: 40px;
    max-width: 1200px;
    margin: 0 auto;
}
.stats {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
    margin-bottom: 40px;
}
.card {
    background: linear-gradient(145deg, #111, #1a1a1a);
    flex: 1;
    min-width: 220px;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 6px 20px rgba(255, 204, 0, 0.2);
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(255, 204, 0, 0.4);
}
.card h2 {
    color: #ffcc00;
    font-size: 40px;
    margin-bottom: 10px;
}
.card p {
    font-size: 16px;
    color: #ccc;
}
.actions {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
    margin-bottom: 40px;
}
.actions a {
    padding: 12px 25px;
    background: #ffcc00;
    color: #000;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: 0.3s;
}
.actions a:hover {
    background: #ffd633;
    box-shadow: 0 0 20px #ffcc00;
    transform: translateY(-2px);
}
.logout {
    background: #ff4444 !important;
    color: #fff !important;
}
.logout:hover {
    background: #ff6666 !important;
    box-shadow: 0 0 20px #ff4444;
}
.btn-home {
    display: inline-block;
    padding: 12px 25px;
    background: #1e3c72;
    color: #ffcc00;
    font-weight: 600;
    text-decoration: none;
    border-radius: 10px;
    transition: 0.3s;
}
.btn-home:hover {
    background: #111;
    color: #ffd633;
    box-shadow: 0 0 15px #ffcc00;
    transform: translateY(-2px);
}
@media(max-width:768px){
    .stats { flex-direction: column; }
    .actions { flex-direction: column; }
}
</style>
</head>
<body autocomplete="off">

<div class="header">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?> 👋</h1>
</div>

<div class="container">

    <!-- Back to Home -->
    <div style="text-align:center; margin-bottom:30px;">
        <a href="../index.php" class="btn-home">🏠 Back to Home</a>
    </div>

    <!-- Stats Cards -->
    <div class="stats">
        <div class="card"><h2><?= $total_cars ?></h2><p>Cars</p></div>
        <div class="card"><h2><?= $total_sales ?></h2><p>Sales</p></div>
        <div class="card"><h2><?= $total_appointments ?></h2><p>Appointments</p></div>
        <div class="card"><h2><?= $total_customers ?></h2><p>Customers</p></div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="manage_cars.php">Manage Cars</a>
        <a href="manage_sales.php">Manage Sales</a>
        <a href="manage_appointments.php">Manage Appointments</a>
        <a href="manage_customers.php">Manage Customers</a>
        <a href="admin_logout.php" class="logout">Logout</a>
    </div>

</div>
</body>
</html>
