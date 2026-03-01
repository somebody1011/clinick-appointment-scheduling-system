<?php
/*
* File: db.php
* Author: Gemini
* Description: This file contains the database configuration and connection logic.
*/

// --- Database Configuration ---

// Database host (use environment variables for Docker, fallback for local development)
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');

// Database username
define('DB_USERNAME', getenv('DB_USER') ?: 'root');

// Database password
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');

// Database name
define('DB_NAME', getenv('DB_NAME') ?: 'clinic_db');

// --- Database Connection ---

// Create a new mysqli object to connect to the database
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check if the connection was successful
if ($conn->connect_error) {
    // If the connection fails, terminate the script and display an error message
    die("Connection failed: " . $conn->connect_error);
}

// Set the character set to utf8mb4 for proper encoding
$conn->set_charset("utf8mb4");

?>
