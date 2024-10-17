<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Admin | View Activity Logs</title>
    <link rel="stylesheet" href="ViewActLogs.css">
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

        <a href="AdminDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
        <a href="ManageUser.php" class="Tab" id="ManageUser">Manage User</a>
        <a href="ViewPatientRecords.php" class="Tab" id="ViewPatientRecords">View Patient Records</a>
        <a href="ViewActLogs.php" class="Tab" id="ViewActivityLogs">View Activity Logs</a>
        <a href="#" class="Tab" id="Posting">Posting</a>
        <a href="#" class="Tab" id="Reports">Reports</a>
        <a href="logout.php" class="Tab" id="Logout">Logout</a>
    </div>
    <!-- NAVBAR CONTENTS END -->

    <div class="Workspace">
    <h2>Activity Logs</h2>
    <form method="GET" action="">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date">
        
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date">
        
        <input type="submit" value="Filter">
    </form>
    <table>
        <thead>
            <tr>
                <th>Act ID</th>
                <th>User ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Activity</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require 'auth.php'; // Check if user is logged in
            require 'databaseconnection.php'; // Include your database connection file

            // Get start and end dates from the form, if set
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

            // Prepare the query
            $query = "SELECT ActID, UserID, Username, Role, Activity, Timestamp FROM tblactlogs";
            $conditions = [];

            // Add conditions based on selected dates
            if ($startDate) {
                $conditions[] = "Timestamp >= '$startDate 00:00:00'";
            }
            if ($endDate) {
                $conditions[] = "Timestamp <= '$endDate 23:59:59'";
            }

            if (count($conditions) > 0) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY ActID ASC"; // Order by timestamp in ascending order
            $result = $conn->query($query); // Assuming $conn is your database connection

            if ($result->num_rows > 0) {
                // Output data for each row
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['ActID']}</td>
                            <td>{$row['UserID']}</td>
                            <td>{$row['Username']}</td>
                            <td>{$row['Role']}</td>
                            <td>{$row['Activity']}</td>
                            <td>{$row['Timestamp']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No activity logs found for the selected date range.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

    <!-- Modal HTML -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    </div>

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
