<?php
session_start(); // Start the session
require 'databaseconnection.php'; // Include the database connection file

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to log activity
function logActivity($conn, $UserID, $Username, $Role, $Activity) {
    // Prepare an SQL statement
    $stmt = $conn->prepare("INSERT INTO tblactlogs (UserID, Username, `Role`, Activity) VALUES (?, ?, ?, ?)");

    // Bind parameters
    $stmt->bind_param("isss", $UserID, $Username, $Role, $Activity);

    // Execute the query
    $stmt->execute();
    $stmt->close();
}

// Initialize error variable
$error = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['Username']);
    $password = sanitizeInput($_POST['Password']);

    // Validate required fields
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Prepare and execute SQL statement to fetch user credentials
        $sql = "SELECT * FROM tblusercredentials WHERE Username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                
                // Verify password (assumes plaintext; ideally, use hashed passwords)
                if ($password === $row['Password']) {
                    // Password is correct; start a session and redirect based on role
                    $_SESSION['UserID'] = $row['UserCredID'];
                    $_SESSION['Username'] = $row['Username'];
                    $_SESSION['Role'] = $row['Role'];

                    // Log successful login activity
                    logActivity($conn, $row['UserCredID'], $row['Username'], $row['Role'], 'User Logged in');

                    
                    // Check user role and redirect to the appropriate dashboard
                    switch ($row['Role']) {
                        case 'Admin':
                            header("Location: AdminDashboard.php");
                            break;
                        case 'BHW':
                            header("Location: BHWDashboard.php");
                            break;
                        case 'Dentist':
                            header("Location: DentistDashboard.php");
                            break;
                        case 'General Doctor':
                            header("Location: GenDocDashboard.php");
                            break;
                        case 'OB/Neonatal':
                            header("Location: OBDashboard.php");
                            break;
                        default:
                            $error = 'Unknown role.';
                            break;
                    }
                    
                    exit();
                } else {
                    // Log failed login attempt due to incorrect password
                    logActivity($conn, $row['UserCredID'], $row['Username'], $row['Role'], 'Failed login attempt (incorrect password)');
                    $error = 'Invalid password.';
                }
            } else {
                $error = 'No user found with this username.';
            }
            
            $stmt->close();
        } else {
            $error = 'Error preparing the SQL statement.';
        }
    }
    
    $conn->close(); // Close the database connection
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Tunasan Health Center</title>
    <link rel="stylesheet" href="Login.css">
    <link rel="icon" href="Images/favicon.ico" type="image/x-icon">
    <link rel="icon" href="Images/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="Images/apple-touch-icon.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-192x192.png">
    <link rel="android-chrome-icon" href="Images/android-chrome-512x512.png">
    <link rel="icon" sizes="32x32" href="Images/favicon-32x32.png">
    <link rel="icon" sizes="16x16" href="Images/favicon-16x16.png">
</head>
<body>
    <main>
        <div class="container">
            <div class="left">
                <h2> <span style="color: #16348C;"> &#10074 </span> WELCOME BACK </h2>
                <form action="Login.php" method="POST">
                    <input type="text" id="Username" name="Username" placeholder="Username" required>
                    <input type="password" id="Password" name="Password" placeholder="Password" required>
                    <label> Forgot Password </label>
                    <button type="submit">Log In</button>
                </form>
                <?php
                if (!empty($error)) {
                    echo '<p style="color:red;">' . htmlspecialchars($error) . '</p>';
                }
                ?>
            </div>
            <div class="right">
                <div class="LogoContainer">
                    <img src="Images/Health Center Logo.png" alt="Logo" class="logo">
                </div>
                <p class="tagline"> TUNASAN HEALTH CENTER </p>
                <p> Offering free and quality healthcare for Tunasanians <br> <br> </p>
                <p> <span style="font-size: 16px"> Not a member yet? <br> <br> Contact administrator to join us. </span>  </p>
            </div>
        </div>
    </main>
</body>
</html>
