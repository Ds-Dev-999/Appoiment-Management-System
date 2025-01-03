<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!$pdo) {
        die("Database connection failed.");
    }

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if (!$admin) {
        $error = "Admin not found with that username.";
    } else {
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>
            
            <label>Password:</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        
        <!-- Register Link Logic -->
        <?php
        $stmt = $pdo->query("SELECT COUNT(*) FROM admins");
        $admin_count = $stmt->fetchColumn();
        
        if ($admin_count == 0) {
            echo "<p style='margin-top: 10px;'>
                    No admins yet? 
                    <a href='register_admin.php' style='color: #e53935; text-decoration: none;'>Register here</a>
                  </p>";
        } else {
            echo "<p style='margin-top: 10px; color: red;'>Use the super admin to log in to the dashboard.</p>";
        }
        ?>
    </div>
</body>
</html>
