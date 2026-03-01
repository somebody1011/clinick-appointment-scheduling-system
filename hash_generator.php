<?php
/*
 * File: hash_generator.php
 * Author: Gemini
 * Description: A temporary script to generate a password hash.
 */

// --- Password to Hash ---
$admin_password = "password";
$doctor_password = "doctor123";

// --- Generate Hash ---
$hashed_admin_password = password_hash($admin_password, PASSWORD_DEFAULT);
$hashed_doctor_password = password_hash($doctor_password, PASSWORD_DEFAULT);

// --- Output Hash ---
echo "Plain Admin Password: " . $admin_password . "<br>";
echo "Hashed Admin Password: " . $hashed_admin_password . "<br>";

echo "Plain Doctor Password: " . $doctor_password . "<br>";
echo "Hashed Doctor Password: " . $hashed_doctor_password . "<br>";

?>