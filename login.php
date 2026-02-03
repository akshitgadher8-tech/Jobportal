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

    // Query Database
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Login Success
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: user_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Job Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar"><h1>Job Portal</h1></nav>
    <div class="auth-bg">
        <div class="auth-container">
            <h2>User Login</h2>
            
            <?php if($error): ?>
                <p style="color: red; background: #ffe6e6; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
                    <?php echo $error; ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST" autocomplete="off">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button class="btn primary-btn" type="submit">Login</button>
            </form>

            <div style="margin: 20px 0;">OR</div>
            <button class="btn secondary-btn" onclick="window.location.href='admin_login.php'">Admin Login</button>
            <p style="margin-top:15px;">New? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</body>
</html>