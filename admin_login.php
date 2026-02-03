<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prevent SQL Injection
    $user = mysqli_real_escape_string($conn, $user);
    $pass = mysqli_real_escape_string($conn, $pass);

    // Check Database for Admin
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass' AND role='admin'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Success: Set Session & Redirect
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = 'admin';
        
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Admin Credentials!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Job Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h1>Job Portal - Admin Access</h1>
</nav>

<div class="auth-bg">
    <div class="auth-container">
        <h2>Admin Login</h2>
        <p style="color:#64748b; margin-bottom: 20px;">Enter administrative credentials</p>

        <?php if($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f87171; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="admin_login.php" method="POST" autocomplete="off">
            <div class="input-group">
                <input type="text" name="username" placeholder="Admin Username" required>
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Admin Password" required>
            </div>

            <button class="btn primary-btn" type="submit">Login to Panel</button>
        </form>
        
        <p style="margin-top: 15px;">
            Not an admin? <a href="login.php">User Login</a>
        </p>
    </div>
</div>

</body>
</html>