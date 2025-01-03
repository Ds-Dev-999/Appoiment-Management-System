<?php
session_start();
require 'db.php';
require 'send_email.php'; // Include PHPMailer functionality

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch appointment data
$appointment_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_id = ?");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if ($appointment) {
    // Format date for datetime-local input field
    $appointment['appointment_date'] = date("Y-m-d\TH:i", strtotime($appointment['appointment_date']));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $phone = $_POST['phone'];
    $appointment_date = $_POST['appointment_date'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $appointment_type = $_POST['appointment_type'];
    $customer_status = $_POST['customer_status'];
    $notes = $_POST['notes'];
    $agent_id = $_POST['agent_id'];

    // Get email from the database
    $email = $appointment['email'];

    // Check if appointment_date was updated and email exists in the database
    if ($appointment['appointment_date'] != $appointment_date && !empty($email)) {
        // Send email notification
        $subject = "Your Scheduled Appointment has Changed";
        $message = "Dear $customer_name,\n\nYour appointment has been rescheduled to $appointment_date.\n\nThank you,\n Company";

        // Send email and log error if it fails
        if (!sendAppointmentEmail($email, $customer_name, $subject, $message)) {
            error_log("Failed to send appointment update email to $email");
        }
    }

    // Update the appointment in the database
    $stmt = $pdo->prepare("UPDATE appointments SET customer_name = ?, phone = ?, appointment_date = ?, status = ?, priority = ?, appointment_type = ?, customer_status = ?, notes = ?, agent_id = ? WHERE appointment_id = ?");
    $stmt->execute([$customer_name, $phone, $appointment_date, $status, $priority, $appointment_type, $customer_status, $notes, $agent_id, $appointment_id]);

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
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Appointment</h2>
        <form method="POST">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" value="<?= htmlspecialchars($appointment['customer_name']) ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($appointment['email']) ?>" disabled>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($appointment['phone']) ?>" required>

            <label>Appointment Date:</label>
            <input type="datetime-local" name="appointment_date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>

            <label>Status:</label>
            <select name="status" required>
                <option value="Not started" <?= $appointment['status'] == 'Not started' ? 'selected' : '' ?>>Not started</option>
                <option value="In progress" <?= $appointment['status'] == 'In progress' ? 'selected' : '' ?>>In progress</option>
                <option value="Postponed" <?= $appointment['status'] == 'Postponed' ? 'selected' : '' ?>>Postponed</option>
                <option value="Completed" <?= $appointment['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option value="Canceled" <?= $appointment['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
            </select>

            <label>Priority:</label>
            <select name="priority" required>
                <option value="High" <?= $appointment['priority'] == 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= $appointment['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= $appointment['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
            </select>

            <label>Appointment Type:</label>
            <select name="appointment_type" required>
                <option value="Physical" <?= $appointment['appointment_type'] == 'Physical' ? 'selected' : '' ?>>Physical</option>
                <option value="Virtual" <?= $appointment['appointment_type'] == 'Virtual' ? 'selected' : '' ?>>Virtual</option>
            </select>

            <label>Customer Status:</label>
            <select name="customer_status" required>
                <option value="Not Yet" <?= $appointment['customer_status'] == 'Not Yet' ? 'selected' : '' ?>>Not Yet</option>
                <option value="Success" <?= $appointment['customer_status'] == 'Success' ? 'selected' : '' ?>>Success</option>
                <option value="Failed" <?= $appointment['customer_status'] == 'Failed' ? 'selected' : '' ?>>Failed</option>
            </select>

            <label>Agent:</label>
            <select name="agent_id" required>
                <option value="">Select Agent</option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?= $agent['agent_id'] ?>" <?= $agent['agent_id'] == $appointment['agent_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($agent['agent_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Notes:</label>
            <textarea name="notes"><?= htmlspecialchars($appointment['notes']) ?></textarea>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
