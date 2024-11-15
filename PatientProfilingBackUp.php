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

        <form id="" method="post">
        <div class="container">
            <div class="left-panel">
                <div class="profile-picture" id="profile-picture">
                    <img id="selectedImage" src="Images/sample-profile.jpg" alt="Profile Picture">
                </div>
                <div class="patient-info">
                    <p>Patient No: 0001-24</p>
                    <input type="text" placeholder="Last Name">
                    <div class="name-row">
                        <input type="text" placeholder="First Name">
                        <input type="text" placeholder="Middle Name">
                        <input type="text" placeholder="Ext">
                    </div>
                    <div class="gender-row">
                        <label for="sex">Sex:</label>
                        <input type="radio" name="sex" value="male"> Male
                        <input type="radio" name="sex" value="female"> Female
                    </div>
                    <input type="date" placeholder="Birthday">
                </div>
            </div>
            
            <div class="right-panel">
                <div class="form-row">
                    <label>Contact Details:</label>
                    <input type="text" placeholder="Phone Number">
                    <input type="email" placeholder="Email">
                </div>
                <div class="form-row">
                    <label>Present Address:</label>
                    <input type="text" placeholder="House Number">
                    <input type="text" placeholder="Street">
                    <input type="text" placeholder="Subdivision/Village">
                    <input type="text" placeholder="Barangay">
                    <input type="text" placeholder="Municipality">
                    <input type="text" placeholder="Zip Code">
                </div>
                <div class="form-row">
                    <label>Identification Card:</label>
                    <input type="text" placeholder="ID Number">
                    <select>
                        <option>ID Issuer</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>Demographics:</label>
                    <input type="text" placeholder="Marital Status">
                    <input type="text" placeholder="Occupation">
                </div>
                <div class="form-row">
                    <label>Emergency Contact:</label>
                    <input type="text" placeholder="Contact Person">
                    <input type="text" placeholder="Relationship">
                    <input type="text" placeholder="Contact Number">
                </div>
            </div>
        </div>
        

        <div class="button-group">
            <button id="clearBtn">Clear</button>
            <button id="saveBtn">Save</button>
        </div>

        </form>
                        <?php
                        $sql = "SELECT * FROM tblpatientprofile"; // Adjust as necessary to fit the actual column for date order
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            $count = 1;
                            while ($row = $result->fetch_assoc()) {
                                $patientName = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName'];
                                echo "<tr>
                                        <td>{$count}</td>
                                        <td>{$row['PatientID']}</td>
                                        <td>{$patientName}</td>
                                        <td>
                                            <div class='dropdown'>
                                                <button onclick='toggleDropdown(\"{$row['PatientID']}\")'>Action</button>
                                                <div class='dropdown-content' id='dropdown-{$row['PatientID']}'>
                                                    <a href='#' onclick='viewPatientRecords(\"{$row['PatientID']}\")'>View Records</a>
                                                    <a href='#' onclick='editPatient(\"{$row['PatientID']}\")'>Edit</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>No patients found</td></tr>";
                        }
                        ?>

        <!-- Confirmation Modal HTML -->
        <div id="submitModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <p>Are you sure you want to submit this information?</p>
                <button id="confirmSubmit">Yes</button>
                <button id="cancelSubmit">No</button>
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

            // Modal elements for submit confirmation
            const submitModal = document.getElementById('submitModal');
            const closeSubmitModal = document.querySelector('#submitModal .close');
            const confirmSubmit = document.getElementById('confirmSubmit');
            const cancelSubmit = document.getElementById('cancelSubmit');
            const submitBtn = document.getElementById('submitBtn');

            submitBtn.addEventListener('click', function(event) {
                submitModal.style.display = 'block';
            });

            closeSubmitModal.addEventListener('click', function() {
                submitModal.style.display = 'none';
            });

            cancelSubmit.addEventListener('click', function() {
                submitModal.style.display = 'none';
            });

            confirmSubmit.addEventListener('click', function() {
                document.querySelector('form').submit(); // Submit the form if confirmed
            });
                                        // Populate the table with the paginated data.
                                paginatedPatients.forEach((p, i) => {
                                    tableBody.innerHTML += `
                                        <tr>
                                            <td>${startIndex + i + 1}</td>
                                            <td>${p.code}</td>
                                            <td>${p.name}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button onclick="toggleDropdown(${p.id})">Action</button>
                                                    <div class="dropdown-content" id="dropdown-${p.id}">
                                                        <a href="#" onclick="viewPatientRecords(${p.id})">View Records</a>
                                                        <a href="#" onclick="editPatient(${p.id})">Edit</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                });

            // Trigger the file input when the div is clicked
document.getElementById('profile-picture').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

// Display the selected image in the div
document.getElementById('fileInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('selectedImage').src = e.target.result;
            document.getElementById('selectedImage').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
        </script>
    </body>
</html>

