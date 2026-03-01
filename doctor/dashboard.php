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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
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
        </div>
    </nav>

    <div class="dashboard-header">
        <div class="container">
            <h2 class="dashboard-title">Appointments</h2>
            <p class="dashboard-subtitle">View appointments for a specific date</p>
        </div>
    </div>

    <div class="container mt-4 mb-5">
        <div class="filter-bar mb-4">
            <form method="get" class="d-flex gap-2">
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($selected_date); ?>" onchange="this.form.submit()">
                <a href="dashboard.php" class="btn btn-outline-secondary">Today</a>
            </form>
            <span class="text-muted">Showing appointments for <strong><?php echo htmlspecialchars($selected_date); ?></strong></span>
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
                                    <th>Time Slot</th>
                                    <th>Reason</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($appointments as $appointment) : ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($appointment['patient_name']); ?></strong></td>
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
