<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

require_once "../inc/db.php";

// Fetch all sales with customer and car info, including card info
$query = "
    SELECT 
        s.id AS sale_id,
        s.sale_price,
        s.sale_date,
        s.salesperson,
        s.notes,
        s.card_name,
        s.card_number,
        s.card_cvv,
        s.card_expire,
        s.created_at AS sale_created_at,
        c.fullname AS customer_name,
        car.make AS car_make,
        car.model AS car_model,
        car.year AS car_year
    FROM sales s
    LEFT JOIN customers c ON s.customer_id = c.id
    LEFT JOIN cars car ON s.car_id = car.id
    ORDER BY s.sale_date DESC
";

$sales = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Sales | Car Dealership</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#000; color:#fff; padding:20px; }
h1 { color:#ffcc00; text-align:center; margin-bottom:20px; text-shadow:0 0 10px #ffcc00; }
.back-dashboard { display:inline-block; margin:10px 0 20px 0; padding:10px 20px; background:#ffcc00; color:#000; border-radius:6px; text-decoration:none; font-weight:600; transition:0.3s; }
.back-dashboard:hover { background:#ffd633; transform:translateY(-2px); }
table { width:100%; border-collapse:collapse; margin-top:10px; border-radius:12px; overflow:hidden; box-shadow:0 0 20px rgba(255,204,0,0.3); }
th, td { padding:12px; text-align:center; border-bottom:1px solid #444; }
th { background: linear-gradient(90deg, #111, #222); color: #ffcc00; }
tr:hover { background: rgba(255,204,0,0.1); }
a { color:#ffcc00; text-decoration:none; font-weight:600; }
a:hover { color:#ffd633; }
</style>
</head>
<body>

<h1>Manage Sales</h1>

<a href="admin_dashboard.php" class="back-dashboard">← Back to Dashboard</a>

<table>
<thead>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Car</th>
<th>Sale Price</th>
<th>Sale Date</th>
<th>Salesperson</th>
<th>Notes</th>
<th>Card Name</th>
<th>Card Number</th>
<th>CVV</th>
<th>Expire Date</th>
<th>Created At</th>
</tr>
</thead>
<tbody>
<?php if($sales && $sales->num_rows > 0): ?>
    <?php while($row = $sales->fetch_assoc()): ?>
        <tr>
            <td><?= $row['sale_id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['car_make'].' '.$row['car_model'].' ('.$row['car_year'].')') ?></td>
            <td>$<?= number_format($row['sale_price'], 2) ?></td>
            <td><?= $row['sale_date'] ?></td>
            <td><?= htmlspecialchars($row['salesperson'] ?? '-') ?></td>
            <td><?= nl2br(htmlspecialchars($row['notes'] ?? '-')) ?></td>
            <td><?= htmlspecialchars($row['card_name']) ?></td>
            <td><?= '************' . substr($row['card_number'], -4) ?></td>
            <td>***</td>
            <td><?= htmlspecialchars($row['card_expire']) ?></td>
            <td><?= $row['sale_created_at'] ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="12">No sales records found.</td>
</tr>
<?php endif; ?>
</tbody>
</table>

</body>
</html>
