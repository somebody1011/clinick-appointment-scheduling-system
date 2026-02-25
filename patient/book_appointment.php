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
<html>
<head>
    <title>Book Appointment - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Clinic System</a>
        <div class="collapse navbar-collapse">
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
    </nav>
    <div class="container mt-5">
        <h2>Book a New Appointment</h2>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="post" action="">
            <div class="form-group">
                <label>Select Doctor</label>
                <select name="doctor_id" class="form-control">
                    <?php foreach ($doctors as $doctor) : ?>
                        <option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['name']; ?> (<?php echo $doctor['specialization']; ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Time Slot</label>
                <select name="time_slot" class="form-control">
                    <?php foreach ($time_slots as $slot) : ?>
                        <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Reason for Visit</label>
                <textarea name="reason" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Book Appointment</button>
        </form>
    </div>
</body>
</html>
