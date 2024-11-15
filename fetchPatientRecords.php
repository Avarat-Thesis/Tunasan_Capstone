<?php
require 'databaseconnection.php';

if (isset($_GET['patientID'])) {
    $patientID = $_GET['patientID'];

    // Fetch patient details
    $sql = "SELECT * FROM tblpatientprofile WHERE PatientID = '$patientID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();

        // Fetch patient history if you have a separate table for it
        $historySql = "SELECT * FROM tblpatienthistory WHERE PatientID = '$patientID'";
        $historyResult = $conn->query($historySql);
        $history = [];

        if ($historyResult->num_rows > 0) {
            while ($row = $historyResult->fetch_assoc()) {
                $history[] = $row;
            }
        }

        $patient['history'] = $history;
        echo json_encode($patient);
    } else {
        echo json_encode(null);
    }
}
?>
