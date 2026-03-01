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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
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
        </div>
    </nav>

    <div class="dashboard-header">
        <div class="container">
            <h2 class="dashboard-title">My Appointments</h2>
            <p class="dashboard-subtitle">Manage and track all your scheduled appointments</p>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <?php if (count($appointments) === 0) : ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <h4 class="text-muted mb-2">No appointments scheduled</h4>
                    <p class="text-muted">You haven't booked any appointments yet.</p>
                    <a href="book_appointment.php" class="btn btn-primary mt-3">Book Your First Appointment</a>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body">
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
                                        <td><strong><?php echo htmlspecialchars($appointment['doctor_name']); ?></strong></td>
                                        <td><?php echo $appointment['date']; ?></td>
                                        <td><?php echo htmlspecialchars($appointment['time_slot']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($appointment['status']); ?> status-<?php echo strtolower($appointment['status']); ?>">
                                                <?php echo htmlspecialchars($appointment['status']); ?>
                                            </span>
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