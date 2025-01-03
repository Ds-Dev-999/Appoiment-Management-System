<?php
if ($admin) {
    $_SESSION['admin_id'] = $admin['admin_id'];
    header("Location: admin_dashboard.php");
    exit();
}
exit();
