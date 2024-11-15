<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="ManageUser.css">
    <link rel="icon" href="Images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="Images/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="Images/apple-touch-icon.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-192x192.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-512x512.png">
    <link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
    <link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
</head>
<body>
                    <?php
                    require 'databaseconnection.php';
                    require 'Auth.php'; // Check if user is logged in
                    ?>

     <!-- NAVBAR CONTENTS START -->
   <div class="Navbar">
        <div class="NavImg">
            <img src="Images/Tunasan Logo.png" alt="Logo">
        </div>
        
        <div class="dashboard-container">
            <div class="sidebar-item">
            <img src="Images/dashboardPIC.png" alt="dashh" class="side_image">
            <a href="AdminDashboard.php" class="Tab" id="Dashboard">Dashboard</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/manage.png" alt="mad" class="side_image">
            <a href="ManageUser.php" class="Tab" id="ManageUser">Manage User</a>
        </div>
        
        <div class="sidebar-item">
            <img src="Images/viewpatientrecordPIC.png" alt="payrec" class="side_image">
            <a href="ViewPatientRecords.php" class="Tab" id="ViewPatientRecords">View Patient Records</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/viewactivitylogsPIC.png" alt="actlogs" class="side_image">
            <a href="ViewActLogs.php" class="Tab" id="ViewActivityLogs">View Activity Logs</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/reportPIC.png" alt="rep" class="side_image">
            <a href="#" class="Tab" id="Posting">Posting</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/reportPIC.png" alt="rep" class="side_image">
            <a href="#" class="Tab" id="Reports">Reports</a>
        </div>

        <div class="sidebar-item">
            <img src="Images/logoutPIC.png" alt="log" class="side_image">
            <a href="logout.php" class="Tab" id="Logout">Logout</a>
        </div>
    </div>

</div>
    <!-- NAVBAR CONTENTS END -->
    
    <!-- Modal HTML -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    </div>
    

    <!-- Modal HTML -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userModalLabel">Notification</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalMessage">
        <!-- Message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

    <div class="Workspace">
        <div class="LeftContent">
            <!-- Table 1: Users -->
            <div class="TableContainer">
                <h2>Users</h2>
                <div class="TableWrapper">
                    <div class="ScrollableTable">
                        <table id="tblUsers">
                            <thead>
                                <tr>
                                    <th>User ID</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Phone No</th>
                                    <th>Email</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th> <!-- New column header -->
                                </tr>
                            </thead>
                        <tbody>

                            <?php
                            require 'databaseconnection.php';
                            // Auth

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['addUser'])) {
        // Adding a new user
        $lastName = sanitizeInput($_POST['lastName']);
        $firstName = sanitizeInput($_POST['firstName']);
        $phoneNo = sanitizeInput($_POST['phoneNo']);
        $email = sanitizeInput($_POST['email']);
        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);
        $role = sanitizeInput($_POST['role']);
        $status = sanitizeInput($_POST['status']); // New field

        // Validate required fields
        if (empty($lastName) || empty($firstName) || empty($username) || empty($password) || empty($role) || empty($status)) {
            echo json_encode(array('success' => false, 'error' => 'Please fill in all required fields.'));
        } else {
            // Insert into tbluser
            $sqlInsertUser = "INSERT INTO tbluser (LastName, FirstName, PhoneNo, Email)
                             VALUES ('$lastName', '$firstName', '$phoneNo', '$email')";
            if ($conn->query($sqlInsertUser) === TRUE) {
                $lastUserID = $conn->insert_id; // Get the last inserted UserID

                // Insert into tblusercredentials with plain text password
                $sqlInsertCredentials = "INSERT INTO tblusercredentials (UserCredID, Username, Password, Role, Status)
                                        VALUES ($lastUserID, '$username', '$password', '$role', '$status')";
                if ($conn->query($sqlInsertCredentials) === TRUE) {
                    // Successfully added user, return response
                    echo json_encode(array('success' => true, 'message' => 'User added successfully'));
                } else {
                    echo json_encode(array('success' => false, 'error' => 'Error adding user credentials: ' . $conn->error));
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Error adding user: ' . $conn->error));
            }
        }
    } elseif (isset($_POST['updateUser'])) {
        // Updating an existing user
        $userID = sanitizeInput($_POST['userID']);
        $lastName = sanitizeInput($_POST['lastName']);
        $firstName = sanitizeInput($_POST['firstName']);
        $phoneNo = sanitizeInput($_POST['phoneNo']);
        $email = sanitizeInput($_POST['email']);
        $username = sanitizeInput($_POST['username']);
        $role = sanitizeInput($_POST['role']);
        $status = sanitizeInput($_POST['status']); // New field
        $password = sanitizeInput($_POST['password']); // New field

        // Validate required fields
        if (empty($userID) || empty($lastName) || empty($firstName) || empty($username) || empty($role) || empty($status)) {
            echo json_encode(array('success' => false, 'error' => 'Please fill in all required fields.'));
        } else {
            // Update tbluser
            $sqlUpdateUser = "UPDATE tbluser SET LastName='$lastName', FirstName='$firstName', PhoneNo='$phoneNo', Email='$email' WHERE UserID=$userID";
            if ($conn->query($sqlUpdateUser) === TRUE) {
                // Prepare SQL to update tblusercredentials
                $sqlUpdateCredentials = "UPDATE tblusercredentials SET Username='$username', Role='$role', Status='$status'";
                
                // Add password to SQL query if provided
                if (!empty($password)) {
                    $sqlUpdateCredentials .= ", Password='$password'";
                }

                $sqlUpdateCredentials .= " WHERE UserCredID=$userID";
                
                if ($conn->query($sqlUpdateCredentials) === TRUE) {
                    // Successfully updated user, return response
                    echo json_encode(array('success' => true, 'message' => 'User updated successfully'));
                } else {
                    echo json_encode(array('success' => false, 'error' => 'Error updating user credentials: ' . $conn->error));
                }
            } else {
                echo json_encode(array('success' => false, 'error' => 'Error updating user: ' . $conn->error));
            }
        }
    } else {
        // Invalid request
        //echo json_encode(array('success' => false, 'error' => 'Invalid request'));
    }
}

// Fetch the next available UserID
$sqlGetMaxUserID = "SELECT MAX(UserID) AS maxUserID FROM tbluser";
$result = $conn->query($sqlGetMaxUserID);
$nextUserID = 1; // Default value if no users are present

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nextUserID = $row['maxUserID'] + 1; // Increment by 1 to get the next UserID
}

// Echo this variable to be used by the front end
echo '<script>var nextUserID = ' . $nextUserID . ';</script>';

// Fetch existing users
$sql = "SELECT u.UserID, u.LastName, u.FirstName, u.PhoneNo, u.Email, c.Username, c.Role, c.Status
        FROM tbluser u
        INNER JOIN tblusercredentials c ON u.UserID = c.UserCredID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["UserID"] . "</td>";
        echo "<td>" . $row["LastName"] . "</td>";
        echo "<td>" . $row["FirstName"] . "</td>";
        echo "<td>" . $row["PhoneNo"] . "</td>";
        echo "<td>" . $row["Email"] . "</td>";
        echo "<td>" . $row["Username"] . "</td>";
        echo "<td>" . $row["Role"] . "</td>";
        echo "<td>" . $row["Status"] . "</td>"; // Display status
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No data found</td></tr>";
}
$conn->close();
?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="RightContent">
            <!-- Form fields for selected item -->
            <div class="FormContainer">
                <h2>Selected User Details</h2>
                <form id="userDetailsForm" method="post">
                    <label for="userID">User ID:</label>
                    <input type="text" id="userID" name="userID" readonly>

                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" required>

                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" required>

                    <label for="phoneNo">Phone No:</label>
                    <input type="text" id="phoneNo" name="phoneNo">

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email">

                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>

                    <!-- Password field -->
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">

                    <label for="role">Role:</label>
                    <select id="role" name="role" class="Field" required>
                        <option value="" disabled selected hidden>Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="BHW">BHW</option>
                        <option value="Dentist">Dentist</option>
                        <option value="General Doctor">General Doctor</option>
                        <option value="OB/Neonatal">OB/Neonatal</option>
                    </select>

                    <!-- Status field -->
                    <label for="status">Status:</label>
                    <select id="status" name="status" class="field" required>
                        <option value="" disabled selected hidden>Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>

                    <div class="ButtonContainer">
                        <button type="submit" name="addUser">Add User</button>
                        <button type="button" id="btnUpdate">Update</button>
                        <button type="button" id="btnClear">Clear</button>
                    </div>
                </form>

                <div id="messageBox" style="display: none;">
                    <p id="messageText"></p>
                </div>
            </div>

            
        </div>
    </div>

                                                           
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha384-oP6fABP1x60Ndodlzw3R+Mco73/23y2NDdX3XkBHK1Y66CV9O68lFRJl+Xk3JbFF" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js" integrity="sha384-wWfwhRY9Lh9bu7D3Hgpy8Wb72yyPZ98Pcl9QphPSpVowStHp+kaLNY6gCC5gz59" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>

        // Assuming this is triggered after an AJAX call to your PHP script
$.ajax({
    url: 'ManageUser.php', // Replace with your PHP URL
    type: 'POST',
    data: { /* your form data */ },
    success: function(response) {
        var result = JSON.parse(response);
        var modalMessage = '';

        if (result.success) {
            modalMessage = result.message;
        } else {
            modalMessage = result.error;
        }

        // Update modal message and show modal
        $('#modalMessage').text(modalMessage);
        $('#userModal').modal('show');
    }
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

        $(document).ready(function() {
        // Handle table row click event
        $('#tblUsers tbody').on('click', 'tr', function() {
            var userID = $(this).find('td:eq(0)').text().trim();
            var lastName = $(this).find('td:eq(1)').text().trim();
            var firstName = $(this).find('td:eq(2)').text().trim();
            var phoneNo = $(this).find('td:eq(3)').text().trim();
            var email = $(this).find('td:eq(4)').text().trim();
            var username = $(this).find('td:eq(5)').text().trim();
            var role = $(this).find('td:eq(6)').text().trim();
            var status = $(this).find('td:eq(7)').text().trim(); // Adjust index if necessary
            
            $('#userID').val(userID);
            $('#lastName').val(lastName);
            $('#firstName').val(firstName);
            $('#phoneNo').val(phoneNo);
            $('#email').val(email);
            $('#username').val(username);
            $('#role').val(role);
            $('#status').val(status); // Set status field
            $('#password').prop('disabled', true); // Disable password field
        });

        // Implement button actions (update, clear)
        $('#btnUpdate').click(function() {
            var userID = $('#userID').val();
            var lastName = $('#lastName').val();
            var firstName = $('#firstName').val();
            var phoneNo = $('#phoneNo').val();
            var email = $('#email').val();
            var username = $('#username').val();
            var role = $('#role').val();
            var status = $('#status').val();

            $.ajax({
                url: 'ManageUser.php',
                method: 'POST',
                data: {
                    userID: userID,
                    lastName: lastName,
                    firstName: firstName,
                    phoneNo: phoneNo,
                    email: email,
                    username: username,
                    role: role,
                    status: status,
                    updateUser: true // Indicates an update operation
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#messageText').text(response.message);
                        $('#messageBox').removeClass('error').addClass('success').show();
                        setTimeout(function() {
                            $('#messageBox').fadeOut('fast');
                        }, 3000); // Hide after 3 seconds (3000 milliseconds)
                        // Optionally update UI or reload data
                    } else {
                        $('#messageText').text('Error: ' + response.error);
                        $('#messageBox').removeClass('success').addClass('error').show();
                    }
                },
                error: function(xhr, status, error) {
                    $('#messageText').text('Error updating user details: ' + error);
                    $('#messageBox').removeClass('success').addClass('error').show();
                }
            });
        });

        $('#btnClear').click(function() {
            // Clear form fields and enable password field
            $('#userDetailsForm')[0].reset();
            $('#password').prop('disabled', false); // Enable password field
            $('#messageBox').hide(); // Hide message box on clear
        });
    });

    $(document).ready(function() {
    // Populate the UserID field with the next available ID when the page loads
    $('#userID').val(nextUserID);

    // Clear form fields when adding a new user
    $('#btnClear').click(function() {
        $('#userDetailsForm')[0].reset(); // Clear form
        $('#userID').val(nextUserID); // Reset UserID to next available value
        $('#password').prop('disabled', false); // Enable password field
        $('#messageBox').hide(); // Hide message box on clear
    });
});

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['Username']) || !isset($_SESSION['Role'])) {
    header("Location: Login.php"); // Redirect to login page if not logged in
    exit();
}
?>
    </script>
</body>
</html>
