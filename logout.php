<?php
session_start();

// Set timezone
date_default_timezone_set('Asia/Manila'); // Set your preferred timezone

// Include your database connection
include 'databaseconnection.php'; // Adjust this path as needed

// Check if user is logged in
if (isset($_SESSION['UserID'])) {
    $UserID = $_SESSION['UserID'];
    $Username = $_SESSION['Username'];
    $Role = $_SESSION['Role'];
    
    // Log the logout activity
    $Activity = "User Logged out";
    $Timestamp = date("Y-m-d H:i:s"); // Current timestamp
    
    // Prepare SQL statement
    $stmt = $conn->prepare("INSERT INTO tblactlogs (UserID, Username, `Role`, Activity, `Timestamp`) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $UserID, $Username, $Role, $Activity, $Timestamp);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        // Log the activity successfully
        // Optionally, send a success response
        echo json_encode(['status' => 'success']);
    } else {
        // Log an error (you can handle it differently based on your error logging strategy)
        error_log("Logout logging failed: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Failed to log activity']);
    }

    // Destroy the session
    session_destroy();
} else {
    // Not logged in
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}
?>
