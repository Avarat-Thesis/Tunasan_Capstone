<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | View Patient Records</title>
    <link rel="stylesheet" href="ManageUser.css">
    <link rel="icon" href="Images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="Images/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="Images/apple-touch-icon.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-192x192.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-512x512.png">
    <link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
    <link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
</head>
<body>
    <!-- NAVBAR CONTENTS START -->
    <div class="Navbar">
        <div class="NavImg">
            <img src="Images/Tunasan Logo.png">
        </div>

        <a href="AdminDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
        <a href="ManageUser.php" class="Tab" id="ManageUser">Manage User</a>
        <a href="ViewPatientRecords.php" class="Tab" id="ViewPatientRecords">View Patient Records</a>
        <a href="ViewActLogs.php" class="Tab" id="ViewActivityLogs">View Activity Logs</a>
        <a href="#" class="Tab" id="Posting">Posting</a>
        <a href="#" class="Tab" id="Reports">Reports</a>
        <a href="logout.php" class="Tab" id="Logout">Logout</a>
        <!-- Add other navigation tabs as needed -->
    </div>
    <!-- NAVBAR CONTENTS END -->

    <!-- TABLE CONTENTS START -->
    <div class="TableContainer">
        <table class="PatientRecordsTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Doctor</th>
                </tr>
            </thead>
            <tbody>
                <?php
                session_start(); // Start the session
                require 'databaseconnection.php';

                // SQL query to fetch patient records
                $sql = "SELECT date, last_name, first_name, doctor FROM patient_records";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["last_name"] . "</td>";
                        echo "<td>" . $row["first_name"] . "</td>";
                        echo "<td>" . $row["doctor"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No records found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <!-- TABLE CONTENTS END -->
    
</body>
</html>
