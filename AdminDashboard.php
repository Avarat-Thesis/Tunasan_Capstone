<?php
require 'auth.php'; // Check if user is logged in
$username = $_SESSION['Username'] ?? 'Admin'; // Fetch username or default to 'Admin'
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Dashboard</title>
    <link rel="stylesheet" href="Dashboard.css">
    <link rel="icon" href="Images/favicon.ico">
</head>
<body>
    
                                                <?php
                                                require 'databaseconnection.php';
                                                require 'Auth.php'; // Check if user is logged in
                                                ?>

    <!-- NAVBAR -->
    <div class="Navbar">
        <div class="NavImg"><img src="Images/Tunasan Logo.png" alt="Logo"></div>
        <div class="dashboard-container">
            <a href="AdminDashboard.php" class="Tab"><div class="sidebar-item"><img src="Images/dashboardPIC.png" alt="dash" class="side_image"> Dashboard</div></a>
            <a href="ManageUser.php" class="Tab"><div class="sidebar-item"><img src="Images/profilePIC.png" alt="prof" class="side_image"> Manage User</div></a>
            <a href="ViewPatientRecords.php" class="Tab"><div class="sidebar-item"><img src="Images/viewpatientrecordPIC.png" alt="folder" class="side_image"> View Patient Records</div></a>
            <a href="ViewActLogs.php" class="Tab"><div class="sidebar-item"><img src="Images/viewactivitylogsPIC.png" alt="sched" class="side_image"> View Activity Logs</div></a>
            <a href="#" class="Tab" id="GenerateReports"><div class="sidebar-item"><img src="Images/generatereportPIC.png" alt="rep" class="side_image"> Generate Reports</div></a>
            <a href="logout.php" class="Tab" id="Logout"><div class="sidebar-item"><img src="Images/logoutPIC.png" alt="log" class="side_image"> Logout</div></a>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="Workspace">
        <div class="Header">
            <span class="Greeting">Good Day, Admin!</span>
            <span class="DateTime" id="dateTime"></span>
        </div>

        <!-- Stats Section -->
        <div class="StatsContainer">
            <div class="StatBox">
                <h2>531</h2>
                <p>Registered Patients</p>
            </div>
            <div class="StatBox">
                <h2>247</h2>
                <p>Completed Appointments</p>
            </div>
            <div class="StatBox">
                <h2>29</h2>
                <p>Referrals</p>
            </div>
            <div class="StatBox">
                <h2>96</h2>
                <p>Online Consultations</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="MainContent">
            <div class="StaffRota">
                <h3>Staff Rota</h3>
                <p>Schedule here...</p>
            </div>
            <div class="Tasks">
                <h3>Tasks</h3>
                <p>Task list here...</p>
            </div>
            <div class="PatientsTable">
                <h3>Patients</h3>
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>ID</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Last Visit</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Kate Barker</td>
                            <td>0813487</td>
                            <td>F</td>
                            <td>21 June 1953</td>
                            <td>12 Jan 2019</td>
                            <td>View | Email</td>
                        </tr>
                        <!-- Add more rows dynamically as needed -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

                                                <!-- Modal HTML -->
                                            <div id="logoutModal" class="modal">
                                                <div class="modal-content">
                                                <span onclick="closeLogoutModal()" class="close">&times;</span>
                                                <p>Are you sure you want to log out?</p>
                                                <button id="confirmLogout">Yes</button>
                                                <button id="cancelLogout">No</button>
                                                </div>
                                            </div>

                                            <?php
                                                require 'databaseconnection.php';
                                                require 'Auth.php'; // Check if user is logged in
                                            ?>
    <script>
        // Update date and time
        setInterval(() => {
            const now = new Date();
            document.getElementById('dateTime').innerText = now.toLocaleString('en-US', { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', 
                hour: '2-digit', minute: '2-digit', second: '2-digit' 
            });
        }, 1000);

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
