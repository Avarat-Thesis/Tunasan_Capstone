<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="width=device-width, initial-scale=1.0">
    <title>Admin | Manage Users</title>
    <link rel="stylesheet" href="ManageUser.css">
    <link rel="icon" href="Images/favicon.ico" type="image/x-icon">
</head>
<body>

                    <?php
                    require 'databaseconnection.php';
                    require 'auth.php';

$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// Count total records for pagination
$totalUsersQuery = "SELECT COUNT(*) AS total FROM tbluser";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total'];
$totalPages = ceil($totalUsers / $limit);

// Fetch users with pagination
$sql = "SELECT tbluser.UserID, CONCAT(tbluser.FirstName, ' ', tbluser.LastName) AS Name, 
                tbluser.PhoneNo, tbluser.Email, tblusercredentials.Username, 
                tblusercredentials.Role, tblusercredentials.Status
        FROM tbluser
        JOIN tblusercredentials ON tbluser.UserID = tblusercredentials.UserCredID
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['editUserID'])) {
        // Edit User Mode 
        $userID = $_POST['editUserID'];
        $firstName = mysqli_real_escape_string($conn, $_POST['editFirstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['editLastName']);
        $phoneNo = mysqli_real_escape_string($conn, $_POST['editPhoneNo']);
        $email = mysqli_real_escape_string($conn, $_POST['editEmail']);
        $username = mysqli_real_escape_string($conn, $_POST['editUsername']);
        $role = mysqli_real_escape_string($conn, $_POST['editRole']);
        $status = mysqli_real_escape_string($conn, $_POST['editStatus']);

        $conn->begin_transaction();

        try {
            $updateUser = "UPDATE tbluser SET FirstName = ?, LastName = ?, PhoneNo = ?, Email = ? WHERE UserID = ?";
            $stmtUser = $conn->prepare($updateUser);
            $stmtUser->bind_param("ssssi", $firstName, $lastName, $phoneNo, $email, $userID);
            $stmtUser->execute();

            $updateCred = "UPDATE tblusercredentials SET Username = ?, Role = ?, Status = ? WHERE UserCredID = ?";
            $stmtCred = $conn->prepare($updateCred);
            $stmtCred->bind_param("sssi", $username, $role, $status, $userID);
            $stmtCred->execute();

            // Log the edit activity
            $activity = "Updated user: $firstName $lastName";
            $logActivity = "INSERT INTO tblactlogs (UserID, Username, Role, Activity) VALUES (?, ?, ?, ?)";
            $stmtLog = $conn->prepare($logActivity);
            $stmtLog->bind_param("isss", $userID, $username, $role, $activity);
            $stmtLog->execute();

            $conn->commit();
            echo "<script>alert('User updated successfully!');</script>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
        }
    } else {
        // Add User Mode
        $firstName = mysqli_real_escape_string($conn, $_POST['FirstName']);
        $lastName = mysqli_real_escape_string($conn, $_POST['LastName']);
        $phoneNo = mysqli_real_escape_string($conn, $_POST['PhoneNo']);
        $email = mysqli_real_escape_string($conn, $_POST['Email']);
        $username = mysqli_real_escape_string($conn, $_POST['Username']);
        $password = mysqli_real_escape_string($conn, $_POST['Password']); // No password hashing here
        $role = mysqli_real_escape_string($conn, $_POST['Role']);
        $status = mysqli_real_escape_string($conn, $_POST['Status']);

        // Check for duplicates based on first and last name
        $duplicateCheckQuery = "SELECT * FROM tbluser WHERE FirstName = ? AND LastName = ?";
        $stmtCheck = $conn->prepare($duplicateCheckQuery);
        $stmtCheck->bind_param("ss", $firstName, $lastName);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            echo "<script>alert('A user with the same first name and last name already exists.');</script>";
        } else {
            // Proceed with insertion if no duplicates are found
            $conn->begin_transaction();

            try {
                $insertUser = "INSERT INTO tbluser (FirstName, LastName, PhoneNo, Email) VALUES (?, ?, ?, ?)";
                $stmtUser = $conn->prepare($insertUser);
                $stmtUser->bind_param("ssss", $firstName, $lastName, $phoneNo, $email);
                $stmtUser->execute();

                $userID = $conn->insert_id;

                $insertCred = "INSERT INTO tblusercredentials (UserCredID, Username, Password, Role, Status) VALUES (?, ?, ?, ?, ?)";
                $stmtCred = $conn->prepare($insertCred);
                $stmtCred->bind_param("issss", $userID, $username, $password, $role, $status);
                $stmtCred->execute();

                // Insert into activity logs
                $activity = "Added new user: $firstName $lastName";
                $logActivity = "INSERT INTO tblactlogs (UserID, Username, Role, Activity) VALUES (?, ?, ?, ?)";
                $stmtLog = $conn->prepare($logActivity);
                $stmtLog->bind_param("isss", $userID, $username, $role, $activity);
                $stmtLog->execute();

                $conn->commit();
                echo "<script>alert('User added successfully!');</script>";
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
            }
        }
    }
}
?>

<!-- The rest of your HTML structure remains the same -->


    <!-- NAVBAR CONTENTS START -->
    <div class="Navbar">
        <div class="NavImg">
            <img src="Images/Tunasan Logo.png" alt="Logo">
        </div>

            <div class="dashboard-container">
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
                        <img src="Images/viewpatientrecordPIC.png" alt="sched" class="side_image"> View Patient Records
                    </div>
                </a>

                <a href="ViewActLogs.php" class="Tab">
                    <div class="sidebar-item">
                        <img src="Images/viewactivitylogsPIC.png" alt="sched" class="side_image"> View Activity Logs
                    </div>
                </a>

                <a href="#" class="Tab" id="GenerateReports">
                    <div class="sidebar-item">
                        <img src="Images/generatereportPIC.png" alt="rep" class="side_image"> Generate Reports
                    </div>
                </a>

                <a href="logout.php" class="Tab" id="Logout">
                    <div class="sidebar-item">
                        <img src="Images/logoutPIC.png" alt="log" class="side_image"> Logout
                    </div>
                </a>
            </div>
            
</div>
    <!-- NAVBAR CONTENTS END -->

    <!-- Modal HTML -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
        <span onclick="closeLogoutModal()" class="close">&times;</span>
        <p>Are you sure you want to log out?</p>
        <button id="confirmLogout">Yes</button>
        <button id="cancelLogout">No</button>
        </div>
    </div>

    <!-- MAIN CONTENT START -->
    <div class="main-content">
        <div class="container">
            <h2>List of Users</h2>
            <div class="table-controls">
                <label for="entries">Show 
                    <select id="entries">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select> entries
                </label>
                <input type="text" placeholder="Search" class="search-bar">
                <button class="add-button">+ Add New User</button>
            </div>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    $sql = "SELECT tbluser.UserID, CONCAT(tbluser.FirstName, ' ', tbluser.LastName) AS Name, 
                    tbluser.PhoneNo, tbluser.Email, tblusercredentials.Username, 
                    tblusercredentials.Role, tblusercredentials.Status
            FROM tbluser
            JOIN tblusercredentials ON tbluser.UserID = tblusercredentials.UserCredID";
    $result = $conn->query($sql);
    $counter = 1; // Start a counter here

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$counter}</td> <!-- Replace UserID with counter here -->
                <td>{$row['Name']}</td>
                <td>{$row['Role']}</td>
                <td>{$row['Status']}</td>
                <td>
                    <div class='dropdown'>
                        <button onclick='toggleDropdown(\"{$row['UserID']}\")'>Action</button>
                        <div class='dropdown-content' id='dropdown-{$row['UserID']}'>
                            <a href='#' onclick='editUser(\"{$row['UserID']}\", \"{$row['Name']}\", \"{$row['PhoneNo']}\", \"{$row['Email']}\", \"{$row['Username']}\", \"{$row['Role']}\", \"{$row['Status']}\")'>Edit</a>
                        </div>
                    </div>
                </td>
            </tr>";
            $counter++; // Increment the counter for each row
        }
    } else {
        echo "<tr><td colspan='5'>No records found.</td></tr>";
    }

    $conn->close();
    ?>
</tbody>

            </table>
            <!-- Pagination Controls -->
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
    </div>

    <!-- Add New User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h2>Add New Profile</h2>
        <form id="addUserForm" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <label>First Name</label>
                <input type="text" name="FirstName" required>
                <label>Last Name</label>
                <input type="text" name="LastName" required>
            </div>

            <div class="form-row">
                <label>Phone No.</label>
                <input type="text" name="PhoneNo" required>
                <label>Email</label>
                <input type="email" name="Email" required>
            </div>

            <div class="form-row">
                <label>Username</label>
                <input type="text" name="Username" required>
                <label>Password</label>
                <input type="password" name="Password" required>
            </div>

            <div class="form-row">
                <label>Role</label>
                <select name="Role" required>
                    <option value="Admin">Admin</option>
                    <option value="BHW">BHW</option>
                    <option value="Dentist">Dentist</option>
                </select>

                <label>Status</label>
                <select name="Status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="cancel-button">Cancel</button>
                <button type="submit" class="save-button">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <span class="close-edit-button">&times;</span>
        <h2>Edit User Profile</h2>
        <form id="editUserForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="editUserID" id="editUserID">
            <div class="form-row">
                <label>First Name</label>
                <input type="text" name="editFirstName" id="editFirstName" required>
                <label>Last Name</label>
                <input type="text" name="editLastName" id="editLastName" required>
            </div>

            <div class="form-row">
                <label>Phone No.</label>
                <input type="text" name="editPhoneNo" id="editPhoneNo" required>
                <label>Email</label>
                <input type="email" name="editEmail" id="editEmail" required>
            </div>

            <div class="form-row">
                <label>Username</label>
                <input type="text" name="editUsername" id="editUsername" required>
                <label>Role</label>
                <select name="editRole" id="editRole" required>
                    <option value="Admin">Admin</option>
                    <option value="BHW">BHW</option>
                    <option value="Dentist">Dentist</option>
                </select>
                <label>Status</label>
                <select name="editStatus" id="editStatus" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="cancel-edit-button">Cancel</button>
                <button type="submit" class="save-edit-button">Save</button>
            </div>
        </form>
    </div>
</div>

    <!-- MAIN CONTENT END -->

                                                            <?php
                                                            require 'databaseconnection.php';
                                                            require 'Auth.php'; // Check if user is logged in
                                                            ?>

    <script>
        // JavaScript for Add User Modal
        document.addEventListener('DOMContentLoaded', () => {
            const addUserButton = document.querySelector('.add-button');
            const addUserModal = document.getElementById('addUserModal');
            const closeButton = document.querySelector('.close-button');
            const cancelButton = document.querySelector('.cancel-button');

            addUserButton.addEventListener('click', () => {
                addUserModal.style.display = 'flex';
            });

            closeButton.addEventListener('click', () => {
                addUserModal.style.display = 'none';
            });

            cancelButton.addEventListener('click', () => {
                addUserModal.style.display = 'none';
            });

            window.addEventListener('click', (event) => {
                if (event.target == addUserModal) {
                    addUserModal.style.display = 'none';
                }
            });
        });

        // JavaScript for Edit User Modal
        function editUser(id, name, phoneNo, email, username, role, status) {
    document.getElementById('editUserID').value = id;

    const nameParts = name.trim().split(' ');
    let firstName, lastName;

    if (nameParts.length === 1) {
        // Only one name part provided, treat it as the first name
        firstName = nameParts[0];
        lastName = '';
    } else if (nameParts.length === 2) {
        // Two name parts provided, treat them as first and last names respectively
        firstName = nameParts[0];
        lastName = nameParts[1];
    } else {
        // More than two parts: take the first two as first name, rest as last name
        firstName = nameParts.slice(0, 2).join(' ');
        lastName = nameParts.slice(2).join(' ');
    }

    document.getElementById('editFirstName').value = firstName;
    document.getElementById('editLastName').value = lastName;

    document.getElementById('editPhoneNo').value = phoneNo;
    document.getElementById('editEmail').value = email;
    document.getElementById('editUsername').value = username;
    document.getElementById('editRole').value = role;
    document.getElementById('editStatus').value = status;

    const editUserModal = document.getElementById('editUserModal');
    editUserModal.style.display = 'flex';
}

        document.addEventListener('DOMContentLoaded', () => {
            const editUserModal = document.getElementById('editUserModal');
            const closeEditButton = document.querySelector('.close-edit-button');
            const cancelEditButton = document.querySelector('.cancel-edit-button');

            closeEditButton.addEventListener('click', () => {
                editUserModal.style.display = 'none';
            });

            cancelEditButton.addEventListener('click', () => {
                editUserModal.style.display = 'none';
            });

            window.addEventListener('click', (event) => {
                if (event.target == editUserModal) {
                    editUserModal.style.display = 'none';
                }
            });
        });

        function toggleDropdown(id) {
            const dropdown = document.getElementById(`dropdown-${id}`);
            const isCurrentlyVisible = dropdown.style.display === 'block';
            
            document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                dropdown.style.display = 'none';
            });

            if (!isCurrentlyVisible) {
                dropdown.style.display = 'block';
            }
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-content').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // JavaScript for Search Bar and Entries Functionality
document.addEventListener('DOMContentLoaded', () => {
    const searchBar = document.querySelector('.search-bar');
    const entriesDropdown = document.getElementById('entries');
    const userTableBody = document.querySelector('.user-table tbody');

    // Search functionality
    searchBar.addEventListener('input', () => {
        const searchTerm = searchBar.value.toLowerCase();
        const rows = userTableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const rowText = row.innerText.toLowerCase();
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });

    // Entries functionality
    entriesDropdown.addEventListener('change', () => {
        const selectedEntries = parseInt(entriesDropdown.value, 10);
        const rows = userTableBody.querySelectorAll('tr');

        // Show only the number of rows based on the selected entries
        rows.forEach((row, index) => {
            row.style.display = index < selectedEntries ? '' : 'none';
        });
    });

    // Initialize the entries dropdown on page load
    entriesDropdown.dispatchEvent(new Event('change'));
});

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
