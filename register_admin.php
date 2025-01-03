<?php
session_start();
require 'db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if any admins exist in the system
$stmt = $pdo->query("SELECT COUNT(*) FROM admins");
$admin_count = $stmt->fetchColumn();

// Check if an admin is logged in
$loggedIn = isset($_SESSION['admin_logged_in']);

// Redirect to login if an admin exists but no one is logged in
if ($admin_count > 0 && !$loggedIn) {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Admin Already Exists</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'info',
                    title: 'Admin Already Exists',
                    text: 'An admin already exists. Please log in to add more admins.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'admin_login.php';
                    }
                });
            });
        </script>
    </head>
    <body></body>
    </html>";
    exit();
}

// Process registration form if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    if ($stmt->execute([$username, $password])) {
        header("Location: admin_login.php"); // Redirect to login after registration
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register Admin</h2>
        <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
        
        <?php if ($admin_count == 0 || $loggedIn): ?>
            <!-- Registration form is only displayed if no admin exists or if an admin is logged in -->
            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" required>
                
                <label>Password:</label>
                <input type="password" name="password" required>
                
                <button type="submit">Register</button>
            </form>
        <?php else: ?>
            <p>You must be logged in to add more admins.</p>
        <?php endif; ?>
    </div>
</body>
</html>
