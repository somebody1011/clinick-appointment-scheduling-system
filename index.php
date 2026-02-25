<?php
/*
* File: index.php
* Author: Gemini
* Description: This is the main entry point of the application. It redirects users based on their login status and role.
*/

// Start the session
session_start();

// --- User Role-Based Redirection ---

// Check if a user is logged in
if (isset($_SESSION["user_id"])) {
    // Redirect based on the user's role
    if ($_SESSION["user_role"] == "patient") {
        header("Location: patient/dashboard.php");
        exit();
    } elseif ($_SESSION["user_role"] == "admin") {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION["user_role"] == "doctor") {
        header("Location: doctor/dashboard.php");
        exit();
    }
} else {
    // If no user is logged in, redirect to the login page
    header("Location: login.php");
    exit();
}
?>