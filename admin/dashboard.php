<?php
/*
 * File: dashboard.php
 * Author: Gemini
 * Description: This file displays the admin dashboard, allowing them to view and manage appointments.
 */

session_start();
include_once '../config/db.php';

// --- Authentication and Authorization ---

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// --- Fetch All Appointments ---

$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$sql = "SELECT a.id, p.name AS patient_name, d.name AS doctor_name, a.date, a.time_slot, a.status 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.id 
        JOIN doctors d ON a.doctor_id = d.id 
        WHERE a.date = ? 
        ORDER BY a.time_slot";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date_filter);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Clinic System - Admin</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="dashboard.php">Appointments</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="dashboard-header">
        <div class="container">
            <h2 class="dashboard-title">All Appointments</h2>
            <p class="dashboard-subtitle">Manage and monitor clinic appointments</p>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="filter-bar mb-4">
            <form method="get" class="d-flex gap-2">
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date_filter); ?>" onchange="this.form.submit()">
                <a href="dashboard.php" class="btn btn-outline-secondary">Today</a>
            </form>
            <span class="text-muted">Showing appointments for <strong><?php echo htmlspecialchars($date_filter); ?></strong></span>
        </div>

        <?php if (count($appointments) === 0) : ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <h4 class="text-muted mb-2">No appointments</h4>
                    <p class="text-muted">No appointments scheduled for this date.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Time Slot</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['time_slot']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($appointment['status']); ?> status-<?php echo strtolower($appointment['status']); ?>">
                                                <?php echo htmlspecialchars($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form method="post" action="update_status.php" style="display: inline-block;">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                                <select name="status" class="form-control-sm" style="width: 120px; display: inline-block; margin-right: 5px;">
                                                    <option value="Pending" <?php if ($appointment['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                    <option value="Arrived" <?php if ($appointment['status'] == 'Arrived') echo 'selected'; ?>>Arrived</option>
                                                    <option value="Completed" <?php if ($appointment['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
