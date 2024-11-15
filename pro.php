<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHW | Patient Profiling</title>
    <link rel="stylesheet" href="PatientProfiling.css">
    <link rel="icon" href="Images/favicon.ico">
    <link rel="apple-touch-icon" href="Images/apple-touch-icon.png">
    <link rel="stylesheet" href="Dashboard.css">
</head>
<body>

                                    <?php
                                    require 'databaseconnection.php';
                                    require 'auth.php';


// Check if form is submitted to add a new profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saveProfile'])) {
    $patientID = uniqid('PA-'); // Generate a unique Patient ID
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $gender = ($_POST['gender'] === 'Male') ? 0 : 1; // Map to tinyint(1) (0 for male, 1 for female)
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Handle file upload for PatientPhoto
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = addslashes(file_get_contents($_FILES['photo']['tmp_name']));
    }

    // Insert new patient profile
    $sql = "INSERT INTO tblpatientprofile (PatientID, FirstName, MiddleName, LastName, Gender, DateofBirth, Email, `Address`, PatientPhoto)
            VALUES ('$patientID', '$firstName', '$middleName', '$lastName', '$gender', '$dob', '$email', '$address', '$photo')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New patient profile added successfully!');</script>";
        
        // Log the activity
        $userID = $_SESSION['userID']; // Assuming the user ID is stored in the session
        $username = $_SESSION['username']; // Assuming the username is stored in the session
        $role = $_SESSION['role']; // Assuming the role is stored in the session
        $activity = "Added new patient profile for: $firstName $middleName $lastName (Patient ID: $patientID)";
        $logSql = "INSERT INTO tblactlogs (UserID, Username, `Role, Activity) VALUES ('$userID', '$username', '$role', '$activity')";
        $conn->query($logSql);
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
    <!-- NAVBAR -->
    <div class="Navbar">
        <div class="NavImg">
            <img src="Images/Tunasan Logo.png" alt="Logo">
        </div>

            <div class="dashboard-container">
                <div class="sidebar-item">
                    <img src="Images/dashboardPIC.png" alt="dash" class="side_image">
                    <a href="BHWDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
                </div>
                <div class="sidebar-item">
                    <img src="Images/profile.png" alt="prof" class="side_image">
                    <a href="PatientProfiling.php" class="Tab" id="Profiling">Profiling</a>
                </div>
                <div class="sidebar-item">
                    <img src="Images/schedules.png" alt="sched" class="side_image">
                    <a href="Schedules.php" class="Tab" id="Schedules">Schedules</a>
                </div>
                <div class="sidebar-item">
                    <img src="Images/report.png" alt="rep" class="side_image">
                    <a href="#" class="Tab" id="GenerateReports">Generate Reports</a>
                </div>
                <div class="sidebar-item">
                    <img src="Images/logout.png" alt="log" class="side_image">
                    <a href="logout.php" class="Tab" id="Logout">Logout</a>
                </div>
            </div>
            
    </div>

    <div class="right">
        <div class="container">
            <h2>List of Patients</h2>
            <div class="table-controls">
                <label>Show <select id="entries" onchange="updateEntries()">
                    <option value="5">5</option><option value="10" selected>10</option><option value="15">15</option>
                </select> entries</label>
                <input type="text" id="search" placeholder="Search" onkeyup="filterTable()">
                <button onclick="openModal()">+ Add New Profile</button>
            </div>
            
            <table id="patientTable" class="styled-table">
                <thead>
                <tr><th>#</th><th>Code</th><th>Patient Name</th><th>Action</th></tr>
                </thead>
                <tbody id="tableBody">
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
</tbody>
            </table>
            <div class="pagination">
                <button onclick="prevPage()">Previous</button>
                <span id="pageNumber">1</span>
                <button onclick="nextPage()">Next</button>
            </div>
        </div>

        <!-- Dynamic Patient Details Page -->
        <div id="patientDetailsPage" class="dynamic-content" style="display: none;"></div>
    </div>

    <!-- MODALS -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div id="addProfileModal" class="addprofile-modal">
        <h2>Add New Profile</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-container">
                <div class="form-column">
                    <div class="form-group"><label>First Name</label><input type="text" name="firstName" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" name="lastName" required></div>
                    <div class="form-group"><label>Gender</label>
                        <select name="gender">
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Email</label><input type="email" name="email"></div>
                </div>
                <div class="form-column">
                    <div class="form-group"><label>Middle Name</label><input type="text" name="middleName"></div>
                    <div class="form-group"><label>Suffix</label><input type="text" name="suffix"></div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" required></div>
                    <div class="form-group"><label>Contact #</label><input type="tel" name="contact" required></div>
                </div>
            </div>
            <div class="form-group full-width"><label>Address</label><textarea rows="3" name="address"></textarea></div>
            <div class="form-group full-width"><label>Profile Photo</label><input type="file" name="photo" accept="image/*"></div>
            <div class="form-actions">
                <button type="button" onclick="closeAddProfileModal()">Cancel</button>
                <button type="submit" name="saveProfile">Save</button>
            </div>
        </form>
    </div>

    <!-- Edit Modal -->
    <div id="editProfileModal" class="addprofile-modal">
        <h2>Edit Profile</h2>
        <form>
            <div class="form-container">
                <div class="form-column">
                    <div class="form-group"><label>First Name</label><input type="text" id="editFirstName" required></div>
                    <div class="form-group"><label>Last Name</label><input type="text" id="editLastName" required></div>
                    <div class="form-group"><label>Gender</label>
                        <select id="editGender">
                            <option>Male</option>
                            <option>Female</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Email</label><input type="email" id="editEmail"></div>
                </div>
                <div class="form-column">
                    <div class="form-group"><label>Middle Name</label><input type="text" id="editMiddleName"></div>
                    <div class="form-group"><label>Suffix</label><input type="text" id="editSuffix"></div>
                    <div class="form-group"><label>Date of Birth</label><input type="date" id="editDob" required></div>
                    <div class="form-group"><label>Contact #</label><input type="tel" id="editContact" required></div>
                </div>
            </div>
            <div class="form-group full-width"><label>Address</label><textarea rows="3" id="editAddress"></textarea></div>
            <div class="form-group full-width"><label>Profile Photo</label><input type="file" accept="image/*" id="editPhoto"></div>
            <div class="form-actions">
                <button type="button" onclick="closeEditProfileModal()">Cancel</button>
                <button type="submit">Update</button>
            </div>
        </form>
    </div>

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
        // Assuming patients is an array that holds all patient data loaded from the server.
let patients = [];
let currentPage = 1;
let entriesPerPage = 10;

// Populate the patients array from the initial server-side load (e.g., from PHP)
// or mock it with dummy data for testing.
patients = Array.from(document.querySelectorAll('#patientTable tbody tr')).map((row, index) => ({
    id: index + 1,
    code: row.cells[1].innerText,
    name: row.cells[2].innerText,
}));

function renderTable() {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';
    
    // Calculate start and end indexes based on the current page and entries per page.
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    const paginatedPatients = patients.slice(startIndex, endIndex);
    
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
    
    // Update the page number display.
    document.getElementById('pageNumber').innerText = currentPage;
}

function nextPage() {
    if ((currentPage * entriesPerPage) < patients.length) {
        currentPage++;
        renderTable();
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
}

function updateEntries() {
    entriesPerPage = parseInt(document.getElementById('entries').value);
    currentPage = 1; // Reset to the first page when the number of entries changes.
    renderTable();
}

function filterTable() {
    const searchTerm = document.getElementById('search').value.toLowerCase();
    
    // Filter the original array based on the search term.
    const filteredPatients = patients.filter(p => 
        p.name.toLowerCase().includes(searchTerm) || p.code.toLowerCase().includes(searchTerm)
    );

    // Save the filtered list in a temporary variable.
    const filteredResults = filteredPatients;
    currentPage = 1; // Reset to the first page for the filtered results.
    renderFilteredTable(filteredResults);
}

function renderFilteredTable(filteredResults) {
    const tableBody = document.getElementById('tableBody');
    tableBody.innerHTML = '';

    // Calculate start and end indexes based on the current page and entries per page.
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    const paginatedPatients = filteredResults.slice(startIndex, endIndex);

    // Populate the table with the paginated data from the filtered results.
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

    // Update the page number display.
    document.getElementById('pageNumber').innerText = currentPage;
}

// Attach the filterTable function to the input's keyup event.
document.getElementById('search').addEventListener('keyup', filterTable);


// Initial render when the page loads.
document.addEventListener('DOMContentLoaded', () => {
    renderTable();
});

        function viewPatientRecords(id) {
            const patient = patients.find(p => p.id === id);
            if (patient) {
                const patientDetailsPage = document.getElementById('patientDetailsPage');
                patientDetailsPage.innerHTML = `
                    <h2>Patient Code: ${patient.code}</h2>
                    <p><strong>Patient Fullname:</strong> ${patient.name}</p>
                    <p><strong>Gender:</strong> ${patient.gender}</p>
                    <p><strong>Birthday:</strong> ${new Date(patient.dob).toDateString()}</p>
                    <p><strong>Address:</strong> ${patient.address}</p>
                    <h3>History</h3>
                    <button onclick="addVitals(${patient.id})">+ Add Vitals</button>
                    <table class="styled-table">
                        <thead>
                            <tr><th>Date</th><th>Diagnosis</th><th>Doctor</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>2021-12-30</td><td>This is a sample diagnosis only</td><td>Dr. John D Smith</td></tr>
                            <tr><td>2021-12-30</td><td>Illness Diagnosis 102</td><td>MD. Claire C Blake</td></tr>
                        </tbody>
                    </table>
                    <button onclick="showPatientList()">Back to List</button>
                `;
                document.querySelector('.container').style.display = 'none';
                patientDetailsPage.style.display = 'block';
            } else {
                alert('Patient not found');
            }
        }

        function editPatient(id) {
            const patient = patients.find(p => p.id === id);
            if (patient) {
                document.getElementById('editFirstName').value = patient.firstName;
                document.getElementById('editLastName').value = patient.lastName;
                document.getElementById('editMiddleName').value = patient.middleName;
                document.getElementById('editSuffix').value = patient.suffix;
                document.getElementById('editGender').value = patient.gender;
                document.getElementById('editEmail').value = patient.email;
                document.getElementById('editDob').value = patient.dob;
                document.getElementById('editContact').value = patient.contact;
                document.getElementById('editAddress').value = patient.address;

                document.getElementById('editProfileModal').style.display = 'block';
                document.getElementById('modalOverlay').style.display = 'block';
            } else {
                alert('Patient not found');
            }
        }

        function addVitals(id) { alert(`Add vitals for patient ID: ${id}`); }

        // Modal Logic
        // Modal Logic
    function openModal() {
        document.getElementById('addProfileModal').style.display = 'block';
        document.getElementById('modalOverlay').style.display = 'block';
    }

    function closeAddProfileModal() {
        document.getElementById('addProfileModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    function closeEditProfileModal() {
        document.getElementById('editProfileModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    // Event listener to close modal when clicking outside of it
    document.getElementById('modalOverlay').addEventListener('click', function () {
        closeAddProfileModal();
        closeEditProfileModal();
    });

    // Dropdown Logic
    function toggleDropdown(id) {
        const dropdown = document.getElementById(`dropdown-${id}`);
        const isCurrentlyVisible = dropdown.style.display === 'block';
        
        // Hide all dropdowns first
        document.querySelectorAll('.dropdown-content').forEach(dropdown => {
            dropdown.style.display = 'none';
        });

        // Show the clicked dropdown if it was not already visible
        if (!isCurrentlyVisible) {
            dropdown.style.display = 'block';
        }
    }

    // Event listener to close dropdown when clicking outside of it
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    // Event listener for DOM content loaded
    document.addEventListener('DOMContentLoaded', () => {
        renderTable();
    });

    // Pagination, rendering table, and patient records logic...
    function showPatientList() {
        document.querySelector('.container').style.display = 'block'; // Show the main list container
        document.getElementById('patientDetailsPage').style.display = 'none'; // Hide the patient details page
    }

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
