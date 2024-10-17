<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['Username']) || !isset($_SESSION['Role'])) {
    header("Location: Login.php"); // Redirect to login page if not logged in
    exit();
}
?>
