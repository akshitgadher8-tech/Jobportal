<?php
include 'db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    // 1. Check if passwords match
    if ($pass !== $confirm_pass) {
        $error = "Passwords do not match!";
    } else {
        // 2. Prevent SQL Injection
        $user = mysqli_real_escape_string($conn, $user);
        $pass = mysqli_real_escape_string($conn, $pass);

        // 3. Check if username already exists
        $check_sql = "SELECT * FROM users WHERE username='$user'";
        $result = $conn->query($check_sql);

        if ($result->num_rows > 0) {
            $error = "Username already taken! Please choose another.";
        } else {
            // 4. Insert new user into database
            $sql = "INSERT INTO users (username, password, role) VALUES ('$user', '$pass', 'user')";
            
            if ($conn->query($sql) === TRUE) {
                // Success! Redirect to login page with a success message
                echo "<script>alert('Account created successfully! Please Login.'); window.location.href='login.php';</script>";
                exit();
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | Job Portal</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar"><h1>Job Portal</h1></nav>
    <div class="auth-bg">
        <div class="auth-container">
            <h2>Create Account</h2>
            <p style="color:#64748b; margin-bottom: 20px;">Join us to find your dream job</p>
            
            <?php if($error): ?>
                <div style="background-color: #fee2e2; color: #991b1b; padding: 10px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f87171; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" autocomplete="off">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Choose Username" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                
                <button class="btn primary-btn" type="submit">Sign Up</button>
            </form>

            <p style="margin-top:15px;">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>