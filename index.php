<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Car Dealership</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* Dark subtle black-red gradient background */
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #0a0a0a;
    overflow: hidden;
    color: #fff;
}
body::before {
    content: "";
    position: fixed;
    top:0; left:0;
    width:100%;
    height:100%;
    background: linear-gradient(135deg, #200000, #0a0a0a, #300000, #0a0a0a);
    background-size: 400% 400%;
    animation: gradientMove 20s ease infinite;
    z-index: -1;
}
@keyframes gradientMove {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}

/* Central container */
.container {
    text-align: center;
    background: rgba(15,15,15,0.85);
    padding: 50px 40px;
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(50,0,0,0.5);
}

/* Text */
.container h1 {
    font-size: 36px;
    color: #ff4444;
    margin-bottom: 20px;
    text-shadow: 0 0 5px #400000;
}
.container p {
    font-size: 18px;
    color: #ddd;
    margin-bottom: 30px;
}

/* Subtle dark-red buttons */
.btn {
    display: inline-block;
    margin: 10px;
    padding: 14px 28px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    background: #440000;
    color: #fff;
    transition: 0.3s;
    box-shadow: 0 0 5px rgba(255,0,0,0.3);
}
.btn:hover {
    background: #660000;
    box-shadow: 0 0 10px rgba(255,0,0,0.4);
    transform: scale(1.05);
}

/* Optional subtle floating particles */
.particle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255,0,0,0.1);
    animation: floatParticle 20s linear infinite;
}
@keyframes floatParticle {
    0% { transform: translateY(0) translateX(0); }
    50% { transform: translateY(-400px) translateX(150px); }
    100% { transform: translateY(0) translateX(0); }
}

/* Responsive */
@media (max-width: 500px) {
    .container { padding: 30px 20px; }
    .container h1 { font-size: 28px; }
    .container p { font-size: 16px; }
    .btn { padding: 12px 24px; font-size: 16px; }
}
</style>
</head>
<body>

<!-- Optional subtle particles -->
<div class="particle" style="width:5px; height:5px; top:80%; left:10%; animation-duration:25s;"></div>
<div class="particle" style="width:6px; height:6px; top:60%; left:40%; animation-duration:20s;"></div>
<div class="particle" style="width:4px; height:4px; top:50%; left:70%; animation-duration:22s;"></div>

<div class="container">
    <h1>Welcome to Car Dealership</h1>
    <p>Select your portal:</p>
    <a href="admin/admin_login.php" class="btn">Admin Login</a>
    <a href="customer/index.php" class="btn">Customer</a>
</div>

</body>
</html>
