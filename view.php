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
            <img src="Images/Tunasan Logo.png" alt="Logo">
        </div>
        
        <div class="dashboard-container">
            <div class="sidebar-item">
            <img src="Images/dashboardPIC.png" alt="dashh" class="side_image">
            <a href="AdminDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/manage.png" alt="mad" class="side_image">
            <a href="ManageUser.php" class="Tab" id="ManageUser">Manage User</a>
        </div>
        
        <div class="sidebar-item">
            <img src="Images/viewpatientrecordPIC.png" alt="payrec" class="side_image">
            <a href="ViewPatientRecords.php" class="Tab" id="ViewPatientRecords">View Patient Records</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/viewactivitylogsPIC.png" alt="actlogs" class="side_image">
            <a href="ViewActLogs.php" class="Tab" id="ViewActivityLogs">View Activity Logs</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/reportPIC.png" alt="rep" class="side_image">
            <a href="#" class="Tab" id="Posting">Posting</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/reportPIC.png" alt="rep" class="side_image">
            <a href="#" class="Tab" id="Reports">Reports</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/logoutPIC.png" alt="log" class="side_image">
            <a href="logout.php" class="Tab" id="Logout">Logout</a>
        </div>
    </div>

</div>
    <!-- NAVBAR CONTENTS END -->
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

<?php
require 'auth.php'; // Check if user is logged in
?>
    <script>
          // Modal elements
          const modal = document.getElementById('logoutModal');
        const closeModal = document.querySelector('.modal .close');
        const confirmLogout = document.getElementById('confirmLogout');
        const cancelLogout = document.getElementById('cancelLogout');
        const logoutLink = document.getElementById('Logout');

        // Show modal
        logoutLink.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent the default anchor action
            modal.style.display = 'block'; // Show the modal
        });

        // Hide modal
        closeModal.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        cancelLogout.addEventListener('click', function() {
            modal.style.display = 'none'; // Hide the modal
        });

        // Confirm logout
        confirmLogout.addEventListener('click', function() {
            fetch('logout.php', {
                method: 'GET', // Use GET or POST as needed
                credentials: 'same-origin' // Ensure cookies are sent with the request
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'Login.php'; // Redirect after successful logout
                } else {
                    console.error('Logout failed');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
