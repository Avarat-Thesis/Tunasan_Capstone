<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title> BHW | Patient Profiling </title>
        <link rel="stylesheet" href="PatientProfiling.css">
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

            <a href="BHWDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
            <a href="PatientProfiling.php" class="Tab" id="Profiling">Profiling</a>
            <a href="Schedules.php" class="Tab" id="">Schedules</a>
            <a href="AddPatientRecord.php" class="Tab" id="AddPatientRecord">Add Patient Record</a>
            <a href="#" class="Tab" id="ViewPatientRecords">View Patient Records</a>
            <a href="#" class="Tab" id="GenerateReports">Generate Reports</a>
            <a href="logout.php" class="Tab" id="Logout">Logout</a>
        </div>
        <!-- NAVBAR CONTENTS END -->
        <div class="right">
            <h2> <span style="color: #16348C;"> &#10074 </span> PATIENT PROFILING </h2> <br>

            <?php
    require 'databaseconnection.php';
    require 'auth.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Collect form data
        $Lastname = $_POST['Lastname'];
        $Firstname = $_POST['Firstname'];
        $Middlename = $_POST['Middlename'];
        $Birthday = $_POST['Birthday'];
        $Sex = $_POST['Sex'];
        $PhoneNo = $_POST['PhoneNo'] ?? null;
        $Email = $_POST['Email'] ?? null;
        $HouseNo = $_POST['HouseNo'];
        $Street = $_POST['Street'];
        $Subd = $_POST['Subd'];
        $Barangay = $_POST['Barangay'];
        $Municipality = $_POST['Municipality'];
        $Zip = $_POST['Zip'];
        $ID = $_POST['ID'] ?? null;
        $IDkind = $_POST['IDkind'] ?? null;
        $EmergCon = $_POST['EmergCon'];
        $Relationship = $_POST['Relationship'];
        $EmergConNo = $_POST['EmergConNo'];
    
        // Prepare SQL statement to insert data into the database
        $sql = "INSERT INTO tblpatientprofile (Lastname, Firstname, Middlename, Birthday, Sex, PhoneNo, Email, HouseNo, Street, Subd, Barangay, Municipality, Zip, ID, IDkind, EmergCon, Relationship, EmergConNo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        // Check if the SQL statement was prepared correctly
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("ssssssssssssssssss",
            $Lastname,
            $Firstname,
            $Middlename,
            $Birthday,
            $Sex,
            $PhoneNo,
            $Email,
            $HouseNo,
            $Street, $Subd, $Barangay, $Municipality, $Zip, $ID, $IDkind, $EmergCon, $Relationship, $EmergConNo);
    
            // Execute the statement
            if ($stmt->execute()) {
                echo "Record successfully saved!";

                // Now log the activity after successful profile creation
                // Assuming user session is already available from 'auth.php'
                $UserID = $_SESSION['UserID']; // Assuming you're storing the logged-in user's ID in session
                $Username = $_SESSION['Username']; // Get the logged-in user's name
                $Role = $_SESSION['Role']; // Get the role of the logged-in user
                $Activity = "Added patient profile for $Firstname $Middlename $Lastname";
                
                // Prepare SQL statement to log the activity
                $log_sql = "INSERT INTO tblactlogs (UserID, Username, Role, Activity, Timestamp)
                            VALUES (?, ?, ?, ?, NOW(6))";

                if ($log_stmt = $conn->prepare($log_sql)) {
                    // Bind parameters for activity log
                    $log_stmt->bind_param("isss", $UserID, $Username, $Role, $Activity);
                    
                    // Execute the log query
                    if ($log_stmt->execute()) {
                        echo "Activity logged successfully!";
                    } else {
                        echo "Error logging activity: " . $log_stmt->error;
                    }

                    // Close the statement
                    $log_stmt->close();
                } else {
                    echo "Error preparing activity log statement: " . $conn->error;
                }

            } else {
                echo "Error executing query: " . $stmt->error;
            }
    
            // Close the statement
            $stmt->close();
        } else {
            // If prepare fails, print the error message
            echo "Error preparing statement: " . $conn->error;
        }
    }
    
    $conn->close();
?>

            <form action="" method="POST">

            <div class="PatientProfile">
                <div class="profile-photo-container">
                    <img id="profileImage" src="Images/default-avatar.png" alt="Profile Photo">
                </div>

                <!-- Hidden file input -->
                <input type="file" id="profilePhoto" accept="image/*" style="display: none;" onchange="displayImage(this)">

                <!-- Label to trigger the hidden file input -->
                <label for="profilePhoto" class="upload-btn">Click to Upload Photo</label>
                <div class="InfoLine1">
                    <h4> Fullname </h4>
                    <input type="text" id="Lastname" name="Lastname" placeholder="Last Name" required>
                    <input type="text" id="Firstname" name="Firstname" placeholder="First Name" required>
                    <input type="text" id="Middlename" name="Middlename" placeholder="Middle Name" required>
                    <input type="text" id="Extension" name="Extension" placeholder="Extension">
                </div>

                <div class="InfoLine2">
                    <h4> Birthday </h4>
                    <input type="date" id="Birthday" name="Birthday" required>

                    <h4> Sex </h4>
                    <input type="radio" id="male" name="Sex" value="male">
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="Sex" value="female">
                    <label for="female">Female</label>
                </div>

                <div class="InfoLine3">
                    <h4> Contact Details </h4>
                    <input type="tel" id="PhoneNo" name="PhoneNo" placeholder="Phone Number">
                    <input type="email" id="Email" name="Email" placeholder="Email">
                </div>

                <div class="InfoLine4">
                    <div class="AddressLine1">
                        <h4> Present Address </h4>
                        <input type="text" id="HouseNo" name="HouseNo" placeholder="House Number" required>
                        <input type="text" id="Street" name="Street" placeholder="Street" required>
                        <input type="text" id="Subd" name="Subd" placeholder="Subdivision/Village" required>
                    </div>

                    <div class="AddressLine2">
                        <input type="text" id="Barangay" name="Barangay" placeholder="Barangay" required>
                        <input type="text" id="Municipality" name="Municipality" placeholder="Municipality" required>
                        <input type="text" id="Zip" name="Zip" placeholder="Zip Code" required>
                    </div>
                </div>

                <div class="InfoLine5">
                    <h4> Identification Card </h4>
                    <input type="text" id="ID" name="ID" placeholder="ID Number">
                    <select id="IDkind" name="IDkind">
                        <option value="ID Issuer" disabled selected hidden>ID Issuer</option>
                        <option value="Barangay ID">Barangay ID</option>
                        <option value="National ID">National ID</option>
                    </select>
                </div>

                <div class="InfoLine6">
                    <h4> Emergency Contact </h4>
                    <input type="text" id="EmergCon" name="EmergCon" placeholder="Emergency Contact Person" required>
                    <input type="text" id="Relationship" name="Relationship" placeholder="Relationship" required>
                    <input type="tel" id="EmergConNo" name="EmergConNo" placeholder="Emergency Contact Number" required>
                </div>

                <button type="submit">Submit</button>

            </div>

            </form>

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

            function displayImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('profileImage').src = e.target.result;
        }
        
        reader.readAsDataURL(input.files[0]); // Convert image file to base64 string
    }
}

// Click the hidden input when the label is clicked
document.querySelector('.upload-btn').addEventListener('click', function() {
    document.getElementById('profilePhoto').click();
});
        </script>
    </body>
</html>

