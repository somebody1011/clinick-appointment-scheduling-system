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

// --- Fetch Doctor's Appointments (selectable date) ---

$doctor_id = $_SESSION['user_id'];
// selected date via GET (format: YYYY-MM-DD), fallback to today
$selected_date = isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date']) ? $_GET['date'] : date('Y-m-d');
$sql = "SELECT a.id, p.name AS patient_name, a.time_slot, a.reason, a.status 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.id 
    WHERE a.doctor_id = ? AND a.date = ? 
    ORDER BY a.time_slot";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $doctor_id, $selected_date);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/doctor.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">Clinic System - Doctor</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Appointments</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-light" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Appointments for <span class="text-muted"><?php echo htmlspecialchars($selected_date); ?></span></h2>
            <form method="get" class="form-inline">
                <label for="date" class="mr-2 text-muted">Jump to date</label>
                <input type="date" id="date" name="date" class="form-control mr-2" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="this.form.submit()">
                <a href="dashboard.php" class="btn btn-outline-secondary">Today</a>
            </form>
        </div>

        <?php if (count($appointments) === 0) : ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <p class="mb-0">No appointments found for this date.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="thead-light">
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
                                        <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['time_slot']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['status']); ?></td>
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
