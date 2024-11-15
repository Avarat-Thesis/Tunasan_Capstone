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

    // Handle the form submission for updating a profile
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateProfile'])) {
    $patientID = $_POST['patientID'];
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'];
    $lastName = $_POST['lastName'];
    $gender = ($_POST['gender'] === 'Male') ? 0 : 1;
    $dob = $_POST['dob'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $contact = $_POST['contact'];

    // Update query
    $sql = "UPDATE tblpatientprofile SET 
                FirstName='$firstName',
                MiddleName='$middleName',
                LastName='$lastName',
                Gender='$gender',
                DateofBirth='$dob',
                Email='$email',
                `Address`='$address'
            WHERE PatientID='$patientID'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!-- NAVBAR -->
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
                $sql = "SELECT * FROM tblpatientprofile";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $count = 1;
                    while ($row = $result->fetch_assoc()) {
                        $patientName = $row['FirstName'] . ' ' . $row['MiddleName'] . ' ' . $row['LastName'];
                        echo "<tr data-id='{$row['PatientID']}' data-gender='{$row['Gender']}' data-dob='{$row['DateofBirth']}' data-address='{$row['Address']}' data-email='{$row['Email']}'>
                                <td>{$count}</td>
                                <td>{$row['PatientID']}</td>
                                <td>{$patientName}</td>
                                <td>
                                    <div class='dropdown'>
                                        <button onclick=\"toggleDropdown('{$row['PatientID']}')\">Action</button>
                                        <div class='dropdown-content' id='dropdown-{$row['PatientID']}'>
                                            <a href='#' onclick=\"viewPatientRecords('{$row['PatientID']}')\">View Records</a>
                                            <a href='#' onclick=\"openEditModal('{$row['PatientID']}')\">Edit</a>
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

    <div id="patientDetailsPage" class="dynamic-content" style="display: none;"></div>
</div>

<div class="modal-overlay" id="modalOverlay"></div>

<!-- View and Edit Modals -->
<div id="editProfileModal" class="addprofile-modal" style="display: none;">
    <h2>Edit Profile</h2>
    <form id="editProfileForm" method="POST">
        <input type="hidden" name="patientID" id="editPatientID">
        <div class="form-container">
            <div class="form-column">
                <div class="form-group"><label>First Name</label><input type="text" name="firstName" id="editFirstName" required></div>
                <div class="form-group"><label>Last Name</label><input type="text" name="lastName" id="editLastName" required></div>
                <div class="form-group"><label>Gender</label>
                    <select name="gender" id="editGender">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="form-group"><label>Email</label><input type="email" name="email" id="editEmail"></div>
            </div>
            <div class="form-column">
                <div class="form-group"><label>Middle Name</label><input type="text" name="middleName" id="editMiddleName"></div>
                <div class="form-group"><label>Suffix</label><input type="text" name="suffix" id="editSuffix"></div>
                <div class="form-group"><label>Date of Birth</label><input type="date" name="dob" id="editDob" required></div>
                <div class="form-group"><label>Contact #</label><input type="tel" name="contact" id="editContact" required></div>
            </div>
        </div>
        <div class="form-group full-width"><label>Address</label><textarea rows="3" name="address" id="editAddress"></textarea></div>
        <div class="form-actions">
            <button type="button" onclick="closeEditProfileModal()">Cancel</button>
            <button type="submit" name="updateProfile">Update</button>
        </div>
    </form>
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
    let patients = Array.from(document.querySelectorAll('#patientTable tbody tr')).map((row) => ({
        id: row.getAttribute('data-id'),
        code: row.cells[1].innerText,
        name: row.cells[2].innerText,
        gender: row.getAttribute('data-gender'),
        dob: row.getAttribute('data-dob'),
        address: row.getAttribute('data-address'),
        email: row.getAttribute('data-email'),
        contact: row.getAttribute('data-contact')
    }));

    let currentPage = 1;
    let entriesPerPage = parseInt(document.getElementById("entries").value);

    function renderTable() {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';
        const startIndex = (currentPage - 1) * entriesPerPage;
        const paginatedPatients = patients.slice(startIndex, startIndex + entriesPerPage);

        paginatedPatients.forEach((patient, index) => {
            tableBody.innerHTML += `
                <tr>
                    <td>${startIndex + index + 1}</td>
                    <td>${patient.code}</td>
                    <td>${patient.name}</td>
                    <td>
                        <div class="dropdown">
                            <button onclick="toggleDropdown('${patient.id}')">Action</button>
                            <div class="dropdown-content" id="dropdown-${patient.id}">
                                <a href="#" onclick="viewPatientRecords('${patient.id}')">View Records</a>
                                <a href="#" onclick="openEditModal('${patient.id}')">Edit</a>
                            </div>
                        </div>
                    </td>
                </tr>`;
        });
        document.getElementById("pageNumber").textContent = currentPage;
    }

    function updateEntries() {
        entriesPerPage = parseInt(document.getElementById("entries").value);
        currentPage = 1;
        renderTable();
    }

    function nextPage() {
        if (currentPage * entriesPerPage < patients.length) {
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

    function toggleDropdown(id) {
        document.querySelectorAll('.dropdown-content').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
        document.getElementById(`dropdown-${id}`).style.display = 'block';
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    function openEditModal(patientID) {
        const patient = patients.find(p => p.id === patientID);
        if (patient) {
            document.getElementById('editPatientID').value = patient.id;
            document.getElementById('editFirstName').value = patient.name.split(' ')[0];
            document.getElementById('editMiddleName').value = patient.name.split(' ')[1] || '';
            document.getElementById('editLastName').value = patient.name.split(' ')[2] || '';
            document.getElementById('editGender').value = patient.gender === '0' ? 'Male' : 'Female';
            document.getElementById('editEmail').value = patient.email || '';
            document.getElementById('editDob').value = patient.dob;
            document.getElementById('editAddress').value = patient.address;
            document.getElementById('editContact').value = patient.contact || '';

            document.getElementById('editProfileModal').style.display = 'block';
            document.getElementById('modalOverlay').style.display = 'block';
        }
    }

    function closeEditProfileModal() {
        document.getElementById('editProfileModal').style.display = 'none';
        document.getElementById('modalOverlay').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', () => {
        renderTable();
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
