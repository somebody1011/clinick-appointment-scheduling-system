<?php
/*
 * File: login.php
 * Author: Gemini
 * Description: This file handles the login process for all user roles.
 */

session_start();
include_once 'config/db.php';

// --- Login Logic ---

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_username = $_POST['email_username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $error = '';

    // --- User Authentication ---

    if ($role == 'patient') {
        $sql = "SELECT * FROM patients WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email_username);
    } elseif ($role == 'admin') {
        $sql = "SELECT * FROM admins WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email_username);
    } elseif ($role == 'doctor') {
        $sql = "SELECT * FROM doctors WHERE name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email_username);
    }

    if (isset($stmt)) {
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_role'] = $role;
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid email/username.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Login</div>
                    <div class="card-body">
                        <?php if (isset($error) && !empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                        <form method="post" action="">
                            <div class="form-group">
                                <label>Email/Username</label>
                                <input type="text" name="email_username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control">
                                    <option value="patient">Patient</option>
                                    <option value="admin">Admin</option>
                                    <option value="doctor">Doctor</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                            <p class="mt-3">Don't have an account? <a href="register.php">Register here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
