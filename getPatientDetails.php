<?php
require 'databaseconnection.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['PatientID'])) {
    $patientID = $_GET['PatientID'];
    $sql = "SELECT * FROM tblpatientprofile WHERE PatientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $patientID);
    $stmt->execute();
    $result = $stmt->get_result();
    $patientDetails = $result->fetch_assoc();

    echo json_encode($patientDetails); // Return data as JSON
} else {
    echo "No patient ID provided";
}
?>
