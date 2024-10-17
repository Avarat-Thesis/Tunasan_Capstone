<?php
// Function to log activity
function logActivity($conn, $UserID, $Firstname, $Lastname, $Role, $Activity) {
    // Prepare an SQL statement
    $stmt = $conn->prepare("INSERT INTO tblactlogs (UserID, Firstname, Lastname, `Role`, Activity) VALUES (?, ?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("issss", $UserID, $Firstname, $Lastname, $Role, $Activity);

    // Execute the query
    if ($stmt->execute()) {
        echo "Activity logged successfully.";
    } else {
        echo "Error logging activity: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

?>
