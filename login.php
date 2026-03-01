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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Clinic Appointment Scheduling System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-background"></div>
        <div class="auth-content">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-icon">🏥</div>
                    <h1>Clinic System</h1>
                    <p>Appointment Scheduling</p>
                </div>

                <?php if (isset($error) && !empty($error)) { ?>
                    <div class="alert alert-danger alert-icon">
                        <span class="alert-icon-symbol">⚠️</span>
                        <div>
                            <strong>Login Failed</strong>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                <?php } ?>

                <form method="post" action="" class="auth-form">
                    <div class="form-group">
                        <label class="form-label">Select Your Role</label>
                        <div class="role-selector">
                            <label class="role-option">
                                <input type="radio" name="role" value="patient" required>
                                <span class="role-icon">👤</span>
                                <span class="role-text">Patient</span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="doctor">
                                <span class="role-icon">👨‍⚕️</span>
                                <span class="role-text">Doctor</span>
                            </label>
                            <label class="role-option">
                                <input type="radio" name="role" value="admin">
                                <span class="role-icon">⚙️</span>
                                <span class="role-text">Admin</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email_username">Email or Username</label>
                        <div class="input-wrapper">
                            <input type="text" id="email_username" name="email_username" class="form-control form-control-lg" placeholder="your@email.com" required>
                            <span class="input-icon">📧</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="••••••••" required>
                            <span class="input-icon">🔒</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login btn-lg">Sign In</button>
                </form>

                <div class="auth-footer">
                    <p>Don't have an account? <a href="register.php" class="auth-link">Sign up here</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
