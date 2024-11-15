

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patient Records</title>
    <link rel="stylesheet" href="PatientProfiling.css">
    <link rel="icon" href="Images/favicon.ico">
    <link rel="apple-touch-icon" href="Images/apple-touch-icon.png">
    <link rel="stylesheet" href="Dashboard.css">
</head>
<body>

                <?php
                require 'databaseconnection.php';
                require 'auth.php'; 

if (!isset($_SESSION['Role'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['Role'];

if (!isset($_SESSION['Role'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['Role'];

// Pagination variables
$limit = $_GET['limit'] ?? 10; // Default 10 entries per page
$page = $_GET['page'] ?? 1; // Default page 1
$offset = ($page - 1) * $limit;

// Count total records for pagination
$totalPatientsQuery = "SELECT COUNT(*) AS total FROM tblpatientprofile";
$totalPatientsResult = $conn->query($totalPatientsQuery);
$totalPatients = $totalPatientsResult->fetch_assoc()['total'];
$totalPages = ceil($totalPatients / $limit);

// Fetch patient records with pagination
$sql = "SELECT PatientID, CONCAT(FirstName, ' ', MiddleName, ' ', LastName) AS Name, Gender, DateofBirth, Address 
        FROM tblpatientprofile 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<div class="Navbar">
    <div class="NavImg">
        <img src="Images/Tunasan Logo.png" alt="Logo">
    </div>

    <div class="dashboard-container">
        <?php
        if ($user_role === 'Admin') {
            echo '
            <a href="AdminDashboard.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/dashboardPIC.png" alt="dash" class="side_image"> Dashboard
                </div>
            </a>
            <a href="ManageUser.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/profilePIC.png" alt="prof" class="side_image"> Manage User
                </div>
            </a>
            <a href="ViewPatientRecords.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/viewpatientrecordPIC.png" alt="folder" class="side_image"> View Patient Records
                </div>
            </a>
            <a href="ViewActLogs.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/viewactivitylogsPIC.png" alt="sched" class="side_image"> View Activity Logs
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/generatereportPIC.png" alt="rep" class="side_image"> Generate Reports
                </div>
            </a>
            <a href="logout.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/logoutPIC.png" alt="log" class="side_image"> Logout
                </div>
            </a>';
        } elseif ($user_role === 'General Doctor') {
            echo '
            <a href="GenDocDashboard.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/dashboardPIC.png" alt="dash" class="side_image"> Dashboard
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/diagnosis.png" alt="diag" class="side_image"> Add Patient Diagnosis
                </div>
            </a>
            <a href="ViewPatientRecords.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/folder.png" alt="folder" class="side_image"> View Patient Records
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/report.png" alt="rep" class="side_image"> Generate Reports
                </div>
            </a>
            <a href="logout.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/logout.png" alt="log" class="side_image"> Logout
                </div>
            </a>';
        } elseif ($user_role === 'Dentist') {
            echo '
            <a href="DentistDashboard.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/dashboardPIC.png" alt="dash" class="side_image"> Dashboard
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/diagnosis.png" alt="diag" class="side_image"> Add Patient Diagnosis
                </div>
            </a>
            <a href="ViewPatientRecords.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/folder.png" alt="folder" class="side_image"> View Patient Records
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/report.png" alt="rep" class="side_image"> Generate Reports
                </div>
            </a>
            <a href="logout.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/logout.png" alt="log" class="side_image"> Logout
                </div>
            </a>';
        } elseif ($user_role === 'OB/Neonatal') {
            echo '
            <a href="OBDashboard.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/dashboardPIC.png" alt="dash" class="side_image"> Dashboard
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/diagnosis.png" alt="diag" class="side_image"> Add Patient Diagnosis
                </div>
            </a>
            <a href="ViewPatientRecords.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/folder.png" alt="folder" class="side_image"> View Patient Records
                </div>
            </a>
            <a href="#" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/report.png" alt="rep" class="side_image"> Generate Reports
                </div>
            </a>
            <a href="logout.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/logout.png" alt="log" class="side_image"> Logout
                </div>
            </a>';
        } else {
            echo '
            <a href="login.php" class="Tab">
                <div class="sidebar-item">
                    <img src="Images/login.png" alt="login" class="side_image"> Login
                </div>
            </a>';
        }
        ?>
    </div>
</div>                                                      

<div class="right">
    <div class="container" id="patientList">
        <h2>List of Patients</h2>
        <div class="table-controls">
            <label>Show 
                <select id="entries" onchange="updateEntries()">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                </select> entries
            </label>
            <input type="text" id="search" placeholder="Search" onkeyup="filterTable()">
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
                        $patientGender = $row['Gender'];
                        $patientDOB = $row['DateofBirth'];
                        $patientAddress = $row['Address'];

                        echo "<tr>
                                <td>{$count}</td>
                                <td>{$row['PatientID']}</td>
                                <td>{$patientName}</td>
                                <td>
                                    <button onclick=\"viewPatientRecords('{$row['PatientID']}', '{$patientName}', '{$patientGender}', '{$patientDOB}', '{$patientAddress}')\">View Records</button>
                                </td>
                            </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='4'>No patients found</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php
            $start = max(1, $page - 2);
            $end = min($totalPages, $start + 4);

            if ($page > 1) echo "<button onclick=\"window.location.href='?page=1&limit=$limit'\"><<</button>";
            if ($page > 1) echo "<button onclick=\"window.location.href='?page=" . ($page - 1) . "&limit=$limit'\">Prev</button>";
            for ($i = $start; $i <= $end; $i++) {
                echo "<button onclick=\"window.location.href='?page=$i&limit=$limit'\" " . ($i == $page ? "class='active'" : "") . ">$i</button>";
            }
            if ($page < $totalPages) echo "<button onclick=\"window.location.href='?page=" . ($page + 1) . "&limit=$limit'\">Next</button>";
            if ($page < $totalPages) echo "<button onclick=\"window.location.href='?page=$totalPages&limit=$limit'\">>></button>";
            ?>
        </div>
    </div>

    <div id="patientDetailsPage" class="dynamic-content" style="display: none;">
        <h2 id="patientCode"></h2>
        <p><strong>Patient Fullname:</strong> <span id="patientName"></span></p>
        <p><strong>Gender:</strong> <span id="patientGender"></span></p>
        <p><strong>Birthday:</strong> <span id="patientDob"></span></p>
        <p><strong>Address:</strong> <span id="patientAddress"></span></p>
        <h3>History</h3>
        <table class="styled-table">
            <thead>
                <tr><th>Date</th><th>Diagnosis</th><th>Doctor</th></tr>
            </thead>
            <tbody id="historyTable">
                <!-- Add Diagnosis Button -->
                <?php if ($user_role !== 'Admin'): ?>
                    <tr>
                        <td colspan="3">
                            <div class="add-diagnosis-container">
                                <button class="add-diagnosis-button" onclick="openAddDiagnosisModal()">
                                    <span class="add-icon">+</span> Add Diagnosis
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                
                <!-- Existing Diagnosis Rows -->
                <tr><td>2021-12-30</td><td>Diagnosis Example 1</td><td>Dr. John Doe</td></tr>
                <tr><td>2021-12-31</td><td>Diagnosis Example 2</td><td>Dr. Jane Smith</td></tr>
            </tbody>
        </table>
        <button onclick="showPatientList()">Back to List</button>
    </div>
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
    let patients = Array.from(document.querySelectorAll('#patientTable tbody tr'));
    let currentPage = 1;
    let entriesPerPage = parseInt(document.getElementById('entries').value);

    function renderTable() {
        const tableBody = document.getElementById('tableBody');
        tableBody.innerHTML = '';
        const startIndex = (currentPage - 1) * entriesPerPage;
        const paginatedPatients = patients.slice(startIndex, startIndex + entriesPerPage);

        paginatedPatients.forEach((row, index) => {
            tableBody.appendChild(row);
        });
        document.getElementById('pageNumber').textContent = currentPage;
    }

    function updateEntries() {
        entriesPerPage = parseInt(document.getElementById('entries').value);
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

    function filterTable() {
        const searchTerm = document.getElementById('search').value.toLowerCase();
        patients.forEach(row => {
            const name = row.cells[2].textContent.toLowerCase();
            row.style.display = name.includes(searchTerm) ? '' : 'none';
        });
    }

    function viewPatientRecords(patientID, patientName, patientGender, patientDOB, patientAddress) {
        document.getElementById('patientCode').textContent = `Patient Code: ${patientID}`;
        document.getElementById('patientName').textContent = patientName;
        document.getElementById('patientGender').textContent = patientGender || 'Not Specified';
        document.getElementById('patientDob').textContent = patientDOB || 'Not Specified';
        document.getElementById('patientAddress').textContent = patientAddress || 'Not Specified';

        const historyTable = document.getElementById('historyTable');
        historyTable.innerHTML = `
            <?php if ($user_role !== 'Admin'): ?>
                <tr>
                    <td colspan="3">
                        <button onclick="openAddDiagnosisModal()">Add Diagnosis</button>
                    </td>
                </tr>
            <?php endif; ?>
            <tr><td>2021-12-30</td><td>Diagnosis Example 1</td><td>Dr. John Doe</td></tr>
            <tr><td>2021-12-31</td><td>Diagnosis Example 2</td><td>Dr. Jane Smith</td></tr>
        `;

        document.getElementById('patientList').style.display = 'none';
        document.getElementById('patientDetailsPage').style.display = 'block';
    }

    function showPatientList() {
        document.getElementById('patientList').style.display = 'block';
        document.getElementById('patientDetailsPage').style.display = 'none';
    }

    function openAddDiagnosisModal() {
        alert('Add Diagnosis Modal');
    }

    document.addEventListener('DOMContentLoaded', renderTable);

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
