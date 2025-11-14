<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}
require_once "../inc/db.php";

// --- Helpers ---
function safePost(string $key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

// --- Handle Add or Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $make = safePost('make');
    $model = safePost('model');
    $year = (int)safePost('year', 0);
    $price = (float)safePost('price', 0.0);
    $category = safePost('category');
    $description = safePost('description');
    $in_stock = max(0, (int)safePost('in_stock', 0)); // now a number input

    // Handle image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../public/images/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $tmp = $_FILES['image']['tmp_name'];
        $origName = basename($_FILES['image']['name']);
        $origName = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $origName);

        $dest = $uploadDir . $origName;
        if (file_exists($dest)) {
            $dest = $uploadDir . time() . '_' . $origName;
            $origName = basename($dest);
        }
        if (move_uploaded_file($tmp, $dest)) {
            $imagePath = 'images/' . $origName;
        }
    }

    // --- Update existing car ---
    if (!empty($_POST['edit_id'])) {
        $id = (int)$_POST['edit_id'];
        if ($imagePath) {
            $sql = "UPDATE cars SET make=?, model=?, year=?, price=?, category=?, image=?, description=?, in_stock=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssddsssii", $make, $model, $year, $price, $category, $imagePath, $description, $in_stock, $id);
        } else {
            $sql = "UPDATE cars SET make=?, model=?, year=?, price=?, category=?, description=?, in_stock=? WHERE id=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssddssii", $make, $model, $year, $price, $category, $description, $in_stock, $id);
        }
        $_SESSION['feedback'] = $stmt->execute() ? "✅ Car updated successfully." : "❌ Error updating car: " . $stmt->error;
        $stmt->close();
    }
    // --- Add new car ---
    else {
        $sql = "INSERT INTO cars (make, model, year, price, category, image, description, in_stock)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssdssssi", $make, $model, $year, $price, $category, $imagePath, $description, $in_stock);
        $_SESSION['feedback'] = $stmt->execute() ? "✅ Car added successfully." : "❌ Error adding car: " . $stmt->error;
        $stmt->close();
    }

    header("Location: manage_cars.php");
    exit;
}

// --- Handle Delete ---
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $row = $mysqli->query("SELECT image FROM cars WHERE id = $del_id")->fetch_assoc();
    if ($row && !empty($row['image'])) {
        $file = __DIR__ . '/../public/' . $row['image'];
        if (file_exists($file)) @unlink($file);
    }
    $stmt = $mysqli->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['feedback'] = "✅ Car deleted successfully.";
    header("Location: manage_cars.php");
    exit;
}

// --- Fetch for edit ---
$editing = false;
$editCar = null;
if (isset($_GET['edit_id'])) {
    $editing = true;
    $id = (int)$_GET['edit_id'];
    $stmt = $mysqli->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editCar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// --- Fetch all cars ---
$result = $mysqli->query("SELECT * FROM cars ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Cars - Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--gold:#ffcc00;--muted:#aaa;}
body{margin:0;background:#000;color:#fff;font-family:Poppins,system-ui,Arial}
.container{max-width:1100px;margin:30px auto;padding:24px}
.header{display:flex;justify-content:space-between;align-items:center}
.h1{color:var(--gold);font-size:22px}
.feedback{margin-top:10px;color:#9f9;padding:8px 12px;background:#072;display:inline-block;border-radius:6px}
.form-card{background:linear-gradient(180deg,#0b0b0b,#0f0f0f);padding:18px;border-radius:12px;border:1px solid #222;box-shadow:0 8px 30px rgba(0,0,0,.6);margin-top:18px}
.row{display:flex;gap:12px;flex-wrap:wrap}
.input, textarea, select{width:100%;padding:10px;border-radius:8px;border:1px solid #222;background:#0b0b0b;color:#fff}
.col{flex:1;min-width:200px}
textarea{min-height:100px;resize:vertical}
.actions{display:flex;gap:12px;align-items:center;margin-top:12px}
.btn{padding:10px 16px;border-radius:10px;border:none;cursor:pointer;font-weight:600}
.btn-primary{background:var(--gold);color:#000}
.btn-danger{background:#ff4444;color:#fff}
.table{width:100%;border-collapse:collapse;margin-top:20px;border-radius:12px;overflow:hidden;box-shadow:0 0 15px rgba(255,204,0,0.2)}
.table th, .table td{padding:12px;border:1px solid #222;text-align:left}
.table th{background:#0f2340;color:var(--gold)}
.table tr:hover{background:rgba(255,204,0,0.05)}
.thumb{width:120px;height:70px;object-fit:cover;border-radius:6px}
.link{color:var(--gold);text-decoration:none}
.small{font-size:13px;color:var(--muted)}
.badge{padding:5px 10px;border-radius:12px;font-size:13px;font-weight:600;text-transform:uppercase;display:inline-block}
.in-stock{background:linear-gradient(135deg,#28a745,#4cd964);color:#fff;box-shadow:0 2px 6px rgba(40,167,69,0.5);}
.out-stock{background:linear-gradient(135deg,#dc3545,#ff6b6b);color:#fff;box-shadow:0 2px 6px rgba(220,53,69,0.5);}
</style>
</head>
<body>
<div class="container">
<div class="header">
    <div>
        <div class="h1">Manage Cars</div>
        <div class="small">Add, edit, or remove car inventory</div>
    </div>
    <div><a href="admin_dashboard.php" class="link">← Back to Dashboard</a></div>
</div>

<?php if (!empty($_SESSION['feedback'])): ?>
<div class="feedback"><?= htmlspecialchars($_SESSION['feedback']) ?></div>
<?php unset($_SESSION['feedback']); endif; ?>

<!-- Add / Edit Form -->
<div class="form-card">
<form method="post" enctype="multipart/form-data" action="manage_cars.php<?= $editing ? '?edit_id=' . (int)$editCar['id'] : '' ?>">
    <div class="row">
        <div class="col">
            <label class="small">Make</label>
            <input class="input" name="make" required value="<?= htmlspecialchars($editing ? $editCar['make'] : '') ?>">
        </div>
        <div class="col">
            <label class="small">Model</label>
            <input class="input" name="model" required value="<?= htmlspecialchars($editing ? $editCar['model'] : '') ?>">
        </div>
        <div class="col">
            <label class="small">Year</label>
            <input class="input" name="year" type="number" min="1900" max="<?= date('Y') ?>" required value="<?= htmlspecialchars($editing ? $editCar['year'] : date('Y')) ?>">
        </div>
    </div>

    <div class="row" style="margin-top:12px">
        <div class="col">
            <label class="small">Price ($)</label>
            <input class="input" name="price" type="number" step="0.01" required value="<?= htmlspecialchars($editing ? $editCar['price'] : '') ?>">
        </div>
        <div class="col">
            <label class="small">Category</label>
            <input class="input" name="category" value="<?= htmlspecialchars($editing ? $editCar['category'] : '') ?>">
        </div>
        <div class="col">
            <label class="small">In Stock</label>
            <input class="input" name="in_stock" type="number" min="0" value="<?= htmlspecialchars($editing ? $editCar['in_stock'] : 1) ?>">
        </div>
    </div>

    <div style="margin-top:12px">
        <label class="small">Description</label>
        <textarea name="description"><?= htmlspecialchars($editing ? $editCar['description'] : '') ?></textarea>
    </div>

    <div style="margin-top:12px">
        <label class="small">Image (optional)</label>
        <input type="file" name="image" accept="image/*">
        <?php if ($editing && !empty($editCar['image'])): ?>
            <div style="margin-top:8px"><img class="thumb" src="../public/<?= htmlspecialchars($editCar['image']) ?>" alt=""></div>
        <?php endif; ?>
    </div>

    <div class="actions">
        <?php if ($editing): ?>
            <input type="hidden" name="edit_id" value="<?= (int)$editCar['id'] ?>">
            <button type="submit" class="btn btn-primary">Update Car</button>
            <a class="btn btn-danger" href="manage_cars.php">Cancel</a>
        <?php else: ?>
            <button type="submit" class="btn btn-primary">Add Car</button>
        <?php endif; ?>
    </div>
</form>
</div>

<!-- Cars Table -->
<table class="table">
<thead>
<tr>
<th>ID</th><th>Image</th><th>Make / Model</th><th>Year</th><th>Price</th><th>Category</th><th>In Stock</th><th>Status</th><th>Created</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
<td><?= (int)$row['id'] ?></td>
<td>
<?php if (!empty($row['image'])): ?>
<img class="thumb" src="../public/<?= htmlspecialchars($row['image']) ?>" alt="Car Image">
<?php else: ?><span class="small">No image</span><?php endif; ?>
</td>
<td><?= htmlspecialchars($row['make'].' '.$row['model']) ?></td>
<td><?= htmlspecialchars($row['year']) ?></td>
<td>$<?= number_format($row['price'],2) ?></td>
<td><?= htmlspecialchars($row['category'] ?: '-') ?></td>
<td><?= (int)$row['in_stock'] ?></td>
<td>
<?php if ($row['in_stock'] > 0): ?>
<span class="badge in-stock">In Stock</span>
<?php else: ?>
<span class="badge out-stock">Out of Stock</span>
<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['created_at']) ?></td>
<td>
<a class="link" href="manage_cars.php?edit_id=<?= (int)$row['id'] ?>">Edit</a> |
<a class="link" href="manage_cars.php?delete_id=<?= (int)$row['id'] ?>" onclick="return confirm('Delete this car?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</body>
</html>
