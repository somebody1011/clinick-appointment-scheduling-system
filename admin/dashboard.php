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
<html>
<head>
    <title>Admin Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
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
    </nav>
    <div class="container mt-5">
        <h2>Appointments for <?php echo $date_filter; ?></h2>
        <form class="form-inline mb-3">
            <label for="date" class="mr-2">Select Date:</label>
            <input type="date" id="date" name="date" class="form-control mr-2" value="<?php echo $date_filter; ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
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
                        <td><?php echo $appointment['patient_name']; ?></td>
                        <td><?php echo $appointment['doctor_name']; ?></td>
                        <td><?php echo $appointment['time_slot']; ?></td>
                        <td><?php echo $appointment['status']; ?></td>
                        <td>
                            <form method="post" action="update_status.php" style="display: inline-block;">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <select name="status" class="form-control-sm">
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
</body>
</html>
