<?php
/*
 * File: hash_generator.php
 * Author: Gemini
 * Description: A temporary script to generate a password hash.
 */

// --- Password to Hash ---
$plain_password = "password";

// --- Generate Hash ---
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

// --- Output Hash ---
echo "Plain Password: " . $plain_password . "<br>";
echo "Hashed Password: " . $hashed_password;

?>