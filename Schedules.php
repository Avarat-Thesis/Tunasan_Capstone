<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> BHW | Dashboard</title>
    <link rel="stylesheet" href="Schedules.css">
    <link rel="icon" href="Images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="Images/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="Images/apple-touch-icon.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-192x192.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-512x512.png">
    <link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
    <link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.css" rel="stylesheet" />

</head>
<body>
    <!-- NAVBAR CONTENTS START -->
    <div class="Navbar">
        <div class="NavImg">
            <img src="Images/Tunasan Logo.png" alt="Logo">
        </div>

        <a href="BHWDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
        <a href="PatientProfiling.php" class="Tab" id="Profiling">Profiling</a>
        <a href="Schedules.php" class="Tab" id="">Schedules</a>
        <a href="AddPatientRecord.php" class="Tab" id="AddPatientRecord">Add Patient Record</a>
        <a href="#" class="Tab" id="ViewPatientRecords">View Patient Records</a>
        <a href="#" class="Tab" id="GenerateReports">Generate Reports</a>
        <a href="logout.php" class="Tab" id="Logout">Logout</a>
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
            <span class="close">&times;</span>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    </div>

    <div class="Workspace">
        <!-- Calendar container -->
        <div id="calendar"></div>
    </div>

<?php
require 'databaseconnection.php';
require 'Auth.php'; // Check if user is logged in

$data = json_decode(file_get_contents('php://input'), true);

$Title = isset($data['Title']) ? $data['Title'] : null;
$EventDate = isset($data['EventDate']) ? $data['EventDate'] : null;
$Description = isset($data['Description']) ? $data['Description'] : null;
$DateCreated = isset($data['DateCreated']) ? $data['DateCreated'] : null;

// Insert the event into the database
if ($pdo && $Title && $EventDate && $Description && $DateCreated) {
    // Insert the event into the database
    $query = "INSERT INTO tblschedule (Title, EventDate, `Description`, DateCreated) 
              VALUES (:Title, :EventDate, :Description, :DateCreated)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':Title', $Title);
    $stmt->bindParam(':EventDate', $EventDate);
    $stmt->bindParam(':Description', $Description);
    $stmt->bindParam(':DateCreated', $DateCreated);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data or database connection']);
}

// Fetch events from the database
$events = [];
$query = "SELECT Title, EventDate FROM tblschedule";
foreach ($pdo->query($query) as $row) {
    $events[] = [
        'Title' => $row['Title'],
        'start' => $row['EventDate']
    ];
}
?>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.4/main.min.js"></script>
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
                    window.location.href = 'UnifiedLogin.php'; // Redirect after successful logout
                } else {
                    console.error('Logout failed');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth', // Month view by default
                selectable: true,            // Allow selecting dates
                dateClick: function(info) {
                    // Triggered when a date is clicked
                    var date = info.dateStr;
                    var eventTitle = prompt("Enter Event Title for " + date + ":");
                    if (eventTitle) {
                        // Add event to calendar
                        calendar.addEvent({
                            title: eventTitle,
                            start: date,
                            allDay: true
                        });
                        // You can also make an AJAX call here to save the event in the database
                        // sendEventToServer(eventTitle, date);
                    }
                },
                events: [
                    // You can preload events here from the server
                    // Example:
                    // { title: 'Sample Event', start: '2024-09-20' }
                ]
            });
            calendar.render();
        });
        
        // Example function to send event to server
        function sendEventToServer(title, date) {
            fetch('addEvent.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title: title,
                    date: date
                })
            }).then(response => {
                if (response.ok) {
                    alert('Event saved successfully');
                } else {
                    alert('Error saving event');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            dateClick: function(info) {
                var date = info.dateStr;
                var eventTitle = prompt("Enter Event Title for " + date + ":");
                if (eventTitle) {
                    calendar.addEvent({
                        title: eventTitle,
                        start: date,
                        allDay: true
                    });
                    // Optionally save the event to the server
                    sendEventToServer(eventTitle, date);
                }
            },
            events: <?php echo json_encode($events); ?>
        });
        calendar.render();
    });
    </script>
</body>
</html>
