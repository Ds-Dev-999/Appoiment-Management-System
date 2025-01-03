<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedule Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Schedule an Appointment</h2>
        <form action="add_appointment.php" method="POST">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" required>

            <label>Email:</label>
            <input type="email" name="email">

            <label>Phone:</label>
            <input type="text" name="phone" required>

            <label>Appointment Date:</label>
            <input type="datetime-local" name="appointment_date" required>

            <label>Priority:</label>
            <select name="priority">
                <option value="High">High</option>
                <option value="Medium" selected>Medium</option>
                <option value="Low">Low</option>
            </select>

            <label>Notes:</label>
            <textarea name="notes"></textarea>

            <button type="submit">Schedule Appointment</button>
        </form>
    </div>
</body>
</html>
