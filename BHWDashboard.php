<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> BHW | Dashboard</title>
    <link rel="stylesheet" href="Dashboard.css">
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
        <div class="NavImg"><img src="Images/Tunasan Logo.png" alt="Logo"></div>
        <div class="dashboard-container">
            <a href="BHWDashboard.php" class="Tab"><div class="sidebar-item"><img src="Images/dashboardPIC.png" alt="dash" class="side_image"> Dashboard </div></a>
            <a href="PatientProfiling.php" class="Tab"><div class="sidebar-item"><img src="Images/profilePIC.png" alt="prof" class="side_image"> Profiling </div></a>
            <a href="Schedules.php" class="Tab"><div class="sidebar-item"><img src="Images/schedulesPIC.png" alt="folder" class="side_image"> Schedules </div></a>
            <a href="Generate Reports.php" class="Tab"><div class="sidebar-item"><img src="Images/generatereportPIC.png" alt="sched" class="side_image"> Generate Reports</div></a>
            <a href="logout.php" class="Tab" id="Logout"><div class="sidebar-item"><img src="Images/logoutPIC.png" alt="log" class="side_image"> Logout</div></a>
        </div>
    </div>
    <!-- NAVBAR CONTENTS END -->
    
    <div class="Workspace">
        <div class="Header">
            <span class="Greeting">Good Day BHW!</span>
            <span class="DateTime" id="dateTime"></span>
        </div>
        <div class="CenteredContent">
            <img src="Images/Health Center Logo BW.png" class="WorkspaceBG">
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
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            };
            document.getElementById('dateTime').innerText = now.toLocaleDateString('en-US', options);
        }

        setInterval(updateDateTime, 1000); // Update every second
        updateDateTime(); // Initial call to display time immediately

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
