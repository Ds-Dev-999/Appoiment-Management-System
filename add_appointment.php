<?php
session_start();
require 'db.php';
require 'send_email.php'; // Include your send_email.php file for the email function

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $appointment_date = $_POST['appointment_date'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $appointment_type = $_POST['appointment_type'];
    $customer_status = $_POST['customer_status'];
    $notes = $_POST['notes'];
    $agent_id = $_POST['agent_id'];

    // Insert appointment data into the database
    $stmt = $pdo->prepare("INSERT INTO appointments (customer_name, email, phone, appointment_date, status, priority, appointment_type, customer_status, notes, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$customer_name, $email, $phone, $appointment_date, $status, $priority, $appointment_type, $customer_status, $notes, $agent_id]);

    if ($appointment['appointment_date'] != $appointment_date && !empty($email)) { 
        // Send email notification
        $subject = "Your Appointment Scheduled ";
        $message = "Dear $customer_name,\n\nYour appointment has been scheduled to $appointment_date.\n\nThank you,\n Company";
    
        // Send email and log error if it fails
        if (!sendAppointmentEmail($email, $customer_name, $subject, $message)) {
            error_log("Failed to send appointment update email to $email");
        }
    }

    header("Location: admin_dashboard.php");
    exit();
}

// Fetch agents for dropdown
$agents = $pdo->query("SELECT * FROM agents")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Add Appointment</h2>
        <form method="POST">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" required>

            <label>Email:</label>
            <input type="email" name="email" required>

            <label>Phone:</label>
            <input type="text" name="phone" required>

            <label>Appointment Date:</label>
            <input type="datetime-local" name="appointment_date" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="Not started">Not started</option>
                <option value="In progress">In progress</option>
                <option value="Postponed">Postpone</option>
                <option value="Completed">Completed</option>
                <option value="Canceled">Canceled</option>
            </select>

            <label>Priority:</label>
            <select name="priority" required>
                <option value="High">High</option>
                <option value="Medium">Medium</option>
                <option value="Low">Low</option>
            </select>

            <label>Appointment Type:</label>
            <select name="appointment_type" required>
                <option value="Physical">Physical</option>
                <option value="Virtual">Virtual</option>
            </select>

            <label>Customer Status:</label>
            <select name="customer_status" required>
                <option value="Not Yet">Not Yet</option>
                <option value="Success">Success</option>
                <option value="Failed">Failed</option>
            </select>

            <label>Agent:</label>
            <select name="agent_id" required>
                <option value="">Select Agent</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?= $agent['agent_id'] ?>"><?= htmlspecialchars($agent['agent_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Notes:</label>
            <textarea name="notes"></textarea>

            <button type="submit">Add Appointment</button>
        </form>
    </div>
</body>
</html>
