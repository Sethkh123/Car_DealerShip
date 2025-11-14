<?php
session_start();
require_once "../inc/db.php";

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Hardcoded admin credentials (use hashed passwords in production)
    $admin_user = 'admin';
    $admin_pass = '123';

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $msg = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Car Dealership</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #000;
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    padding: 0;
}
.login-box {
    background: linear-gradient(180deg, #0b0b0b, #1a1a1a);
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(255,204,0,0.4);
    width: 360px;
    text-align: center;
    border: 1px solid #222;
}
.login-box h2 {
    color: #ffcc00;
    margin-bottom: 20px;
    text-shadow: 0 0 8px #ffcc00;
}
.login-box input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: 1px solid #222;
    background: #0b0b0b;
    color: #fff;
    font-size: 16px;
}
.login-box input:focus {
    outline: none;
    border-color: #ffcc00;
    box-shadow: 0 0 8px #ffcc00;
}
.login-box button {
    padding: 12px 20px;
    background: #ffcc00;
    border: none;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    border-radius: 8px;
    transition: 0.3s;
}
.login-box button:hover {
    background: #ffd633;
    box-shadow: 0 0 15px #ffcc00;
}
.msg {
    color: #ff4444;
    margin-bottom: 10px;
    font-weight: 500;
}
.back-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background: #1e3c72;
    color: #ffcc00;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.back-btn:hover {
    background: #111;
    color: #ffd633;
    box-shadow: 0 0 10px #ffcc00;
}
</style>
</head>
<body autocomplete="off">

<div class="login-box">
    <h2>Admin Login</h2>
    <?php if($msg): ?>
        <div class="msg"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="text" name="username" placeholder="Username" required autocomplete="off">
        <input type="password" name="password" placeholder="Password" required autocomplete="new-password">
        <button type="submit">Login</button>
    </form>
    <!-- Back button -->
    <a href="../index.php" class="back-btn">← Back</a>
</div>

</body>
</html>
