<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $theme = $_POST['theme'];
    file_put_contents('current_theme.txt', $theme); // Save the theme choice
    header("Location: admin_dashboard.php");
    exit();
}

$current_theme = file_get_contents('current_theme.txt');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Theme Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Theme Manager</h2>
        <form method="POST">
            <label>Select Theme:</label>
            <select name="theme">
                <option value="black_red" <?= $current_theme == 'black_red' ? 'selected' : '' ?>>Black & Red</option>
                <option value="white_blue" <?= $current_theme == 'white_blue' ? 'selected' : '' ?>>White & Blue</option>
            </select>
            <button type="submit">Save Theme</button>
        </form>
    </div>
</body>
</html>
