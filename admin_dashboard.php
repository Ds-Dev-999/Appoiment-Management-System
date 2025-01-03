<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$stmt = $pdo->query("
    SELECT a.*, ag.agent_name 
    FROM appointments a 
    LEFT JOIN agents ag ON a.agent_id = ag.agent_id
");
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport">
    <title>Admin Dashboard - Manage Appointments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="adminstyle.css"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid my-4">
    <header class="header mb-12">
            <h2>Scheduled Appointments</h2>
            <div>
                <a href="add_appointment.php" class="btn btn-primary">Add Appointment</a>
                <a href="register_admin.php" class="btn btn-info">Add Admin</a>

                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <!-- Appointment Status Charts -->
        <div class="d-flex flex-wrap justify-content-between chart-wrapper">
            <div class="chart-container">
                <h5>Appointment Status Distribution</h5>
                <canvas id="statusChart"></canvas>
            </div>
            <div class="chart-container">
                <h5>Customer Status Progress</h5>
                <canvas id="customerStatusChart"></canvas>
            </div>
            <div class="chart-container">
                <h5>Overall Progress</h5>
                <div class="progress">
                    <div class="progress-bar bg-warning" id="progressBar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    
        <!-- Filter Section -->
        <div class="filter-container">
            <label for="filterStatus">Filter by Status:</label>
            <select id="filterStatus" class="form-control w-25 d-inline-block">
                <option value="">All</option>
                <option value="In progress">In progress</option>
                <option value="Not started">Not started</option>
                <option value="Completed">Completed</option>
                <option value="Canceled">Canceled</option>
                <option value="Postponed">Postponed</option>
            </select>

            <label for="filterDate">Filter by Date:</label>
            <input type="date" id="filterDate" class="form-control w-25 d-inline-block">

            <label for="filterCustomer">Filter by Customer Name:</label>
            <input type="text" id="filterCustomer" class="form-control w-25 d-inline-block" placeholder="Enter customer name">
        </div>

        <table class="table table-striped table-dark table-responsive-md mt-4" id="appointmentTable">
            <thead class="thead-light">
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Appointment Date</th>
                    <th>Status</th>
                    <th>Customer Status</th>
                    <th>Priority</th>
                    <th>Appointment Type</th>
                    <th>Agent</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr data-status="<?= htmlspecialchars($appointment['status']) ?>" data-date="<?= htmlspecialchars($appointment['appointment_date']) ?>" data-customer="<?= strtolower(htmlspecialchars($appointment['customer_name'])) ?>">
                        <td><?= htmlspecialchars($appointment['customer_name']) ?></td>
                        <td><?= htmlspecialchars($appointment['email']) ?></td>
                        <td><?= htmlspecialchars($appointment['phone']) ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_date']) ?></td>
                        <td class="<?= $appointment['status'] === 'Completed' ? 'status-completed' : '' ?>">
                            <?= htmlspecialchars($appointment['status']) ?>
                        </td>
                        <td><?= htmlspecialchars($appointment['customer_status']) ?></td>
                        <td><?= htmlspecialchars($appointment['priority']) ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_type']) ?></td>
                        <td><?= htmlspecialchars($appointment['agent_name'] ?? 'Not Assigned') ?></td>
                        <td><?= htmlspecialchars($appointment['notes']) ?></td>
                        <td class="action-buttons">
                        <div class="action-buttons">
                            <a href="edit_appointment.php?id=<?= $appointment['appointment_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_appointment.php?id=<?= $appointment['appointment_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        const appointmentStatuses = <?= json_encode(array_count_values(array_column($appointments, 'status'))) ?>;
        const completedAppointments = <?= count(array_filter($appointments, fn($a) => $a['status'] === 'Completed')) ?>;
        const totalAppointments = <?= count($appointments) ?>;

        // Pie Chart for Appointment Status
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: Object.keys(appointmentStatuses),
                datasets: [{
                    data: Object.values(appointmentStatuses),
                    backgroundColor: ['#FFEB3B', '#4CAF50', '#F44336', '#2196F3', '#4CAF50'],
                }]
            },
            options: {
                responsive: true,
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // Line Chart for Customer Status Progress
        const ctxCustomerStatus = document.getElementById('customerStatusChart').getContext('2d');
        new Chart(ctxCustomerStatus, {
            type: 'line',
            data: {
                labels: Object.keys(appointmentStatuses),
                datasets: [{
                    label: 'Customer Status',
                    data: Object.values(appointmentStatuses),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutBounce'
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Progress Bar for Overall Progress
        const progressPercent = Math.round((completedAppointments / totalAppointments) * 100);
        document.getElementById('progressBar').style.width = progressPercent + '%';
        document.getElementById('progressBar').setAttribute('aria-valuenow', progressPercent);
        document.getElementById('progressBar').innerText = progressPercent + '%';

        // Filter functionality
        document.getElementById('filterStatus').addEventListener('change', filterAppointments);
        document.getElementById('filterDate').addEventListener('change', filterAppointments);
        document.getElementById('filterCustomer').addEventListener('keyup', filterAppointments);

        function filterAppointments() {
            const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
            const dateFilter = document.getElementById('filterDate').value;
            const customerFilter = document.getElementById('filterCustomer').value.toLowerCase();

            document.querySelectorAll('#appointmentTable tbody tr').forEach(row => {
                const rowStatus = row.getAttribute('data-status').toLowerCase();
                const rowDate = row.getAttribute('data-date');
                const rowCustomer = row.getAttribute('data-customer').toLowerCase();

                const matchesStatus = !statusFilter || rowStatus === statusFilter;
                const matchesDate = !dateFilter || rowDate === dateFilter;
                const matchesCustomer = !customerFilter || rowCustomer.includes(customerFilter);

                row.style.display = (matchesStatus && matchesDate && matchesCustomer) ? '' : 'none';
            });
        }
    </script>
</body>
</html>
