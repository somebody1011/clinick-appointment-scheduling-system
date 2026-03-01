<?php
/*
 * File: dashboard.php
 * Author: Gemini
 * Description: This file displays the patient's dashboard, including their appointment history.
 */

session_start();
include_once '../config/db.php';

// --- Authentication and Authorization ---

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'patient') {
    header("Location: ../login.php");
    exit();
}

// --- Fetch Patient's Appointments ---

$patient_id = $_SESSION['user_id'];
$sql = "SELECT a.id, d.name AS doctor_name, a.date, a.time_slot, a.reason, a.status 
        FROM appointments a 
        JOIN doctors d ON a.doctor_id = d.id 
        WHERE a.patient_id = ? 
        ORDER BY a.date DESC, a.time_slot DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Patient Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/app.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">Clinic System</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="book_appointment.php">Book Appointment</a>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>My Appointments</h2>
            <a href="book_appointment.php" class="btn btn-outline-light btn-sm text-primary">Book Appointment</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="mb-3 hint">Recent and past appointments</h5>
                <div class="table-responsive">
                    <table class="table">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Time Slot</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) : ?>
                    <tr>
                        <td><?php echo $appointment['doctor_name']; ?></td>
                        <td><?php echo $appointment['date']; ?></td>
                        <td><?php echo $appointment['time_slot']; ?></td>
                        <td><?php echo $appointment['reason']; ?></td>
                        <td><?php echo $appointment['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>