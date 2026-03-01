<?php
/*
 * File: book_appointment.php
 * Author: Gemini
 * Description: This file allows patients to book a new appointment.
 */

session_start();
include_once '../config/db.php';

// --- Authentication and Authorization ---

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'patient') {
    header("Location: ../login.php");
    exit();
}

// --- Fetch Doctors ---

$sql = "SELECT * FROM doctors";
$result = $conn->query($sql);
$doctors = $result->fetch_all(MYSQLI_ASSOC);

// --- Time Slots ---

$time_slots = ["08:00-08:30", "08:30-09:00", "09:00-09:30", "09:30-10:00"];

// --- Appointment Booking Logic ---

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time_slot = $_POST['time_slot'];
    $reason = $_POST['reason'];
    $patient_id = $_SESSION['user_id'];

    // --- Double Booking Prevention ---

    $sql = "SELECT * FROM appointments WHERE doctor_id = ? AND date = ? AND time_slot = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $doctor_id, $date, $time_slot);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "This time slot is already booked. Please choose another one.";
    } else {
        // Insert new appointment
        $sql = "INSERT INTO appointments (patient_id, doctor_id, date, time_slot, reason) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time_slot, $reason);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error: " . $stmt->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Clinic System</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item active">
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

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-md">
                    <div class="card-header">
                        <h3 class="mb-0">Schedule Your Appointment</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)) { ?>
                            <div class="alert alert-danger">
                                <strong>⚠️ Booking Error:</strong> <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php } ?>

                        <form method="post" action="">
                            <div class="form-group">
                                <label class="form-label">Select a Doctor</label>
                                <select name="doctor_id" class="form-control" required>
                                    <option value="">-- Choose a Doctor --</option>
                                    <?php foreach ($doctors as $doctor) : ?>
                                        <option value="<?php echo $doctor['id']; ?>">
                                            <?php echo htmlspecialchars($doctor['name']); ?> • <?php echo htmlspecialchars($doctor['specialization']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="form-hint">Select the specialist you'd like to visit</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Preferred Date</label>
                                <input type="date" name="date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                                <p class="form-hint">Select a date from today onwards</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Preferred Time Slot</label>
                                <select name="time_slot" class="form-control" required>
                                    <option value="">-- Choose a Time --</option>
                                    <?php foreach ($time_slots as $slot) : ?>
                                        <option value="<?php echo $slot; ?>">
                                            <?php echo htmlspecialchars($slot); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="form-hint">Available time slots (30 minutes each)</p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Reason for Visit</label>
                                <textarea name="reason" class="form-control" placeholder="Describe your symptoms or reason for the visit..." required rows="4"></textarea>
                                <p class="form-hint">This helps the doctor prepare for your visit</p>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Confirm Appointment</button>
                                <a href="dashboard.php" class="btn btn-outline-secondary btn-lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
