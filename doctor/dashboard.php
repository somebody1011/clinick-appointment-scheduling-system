<?php
/*
 * File: dashboard.php
 * Author: Gemini
 * Description: This file displays the doctor's dashboard, showing their appointments for the day.
 */

session_start();
include_once '../config/db.php';

// --- Authentication and Authorization ---

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'doctor') {
    header("Location: ../login.php");
    exit();
}

// --- Fetch Doctor's Appointments for Today ---

$doctor_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$sql = "SELECT a.id, p.name AS patient_name, a.time_slot, a.reason, a.status 
        FROM appointments a 
        JOIN patients p ON a.patient_id = p.id 
        WHERE a.doctor_id = ? AND a.date = ? 
        ORDER BY a.time_slot";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $doctor_id, $today);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Clinic System - Doctor</a>
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
        <h2>Today's Appointments (<?php echo $today; ?>)</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Time Slot</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment) : ?>
                    <tr>
                        <td><?php echo $appointment['patient_name']; ?></td>
                        <td><?php echo $appointment['time_slot']; ?></td>
                        <td><?php echo $appointment['reason']; ?></td>
                        <td><?php echo $appointment['status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
