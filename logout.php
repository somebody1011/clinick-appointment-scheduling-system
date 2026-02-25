<?php
/*
 * File: logout.php
 * Author: Gemini
 * Description: This file handles the logout process and destroys the session.
 */

session_start();
session_destroy();
header("Location: login.php");
exit();
?>