<?php
ob_start(); // Start output buffering

require 'auth.php';
require 'databaseconnection.php';

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$limit = $_GET['limit'] ?? 10;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

$conditions = [];
if ($startDate) $conditions[] = "Timestamp >= '$startDate 00:00:00'";
if ($endDate) $conditions[] = "Timestamp <= '$endDate 23:59:59'";
$whereClause = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

$totalLogs = $conn->query("SELECT COUNT(*) AS total FROM tblactlogs $whereClause")->fetch_assoc()['total'];
$totalPages = ceil($totalLogs / $limit);

$query = "SELECT Username, Role, Activity, Timestamp FROM tblactlogs $whereClause ORDER BY Timestamp ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | View Activity Logs</title>
    <link rel="stylesheet" href="ViewActLogs.css">
</head>
<body>
                                                           
    <!-- Navbar and Content -->
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
                    <img src="Images/viewpatientrecordPIC.png" alt="folder" class="side_image"> View Patient Records
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

 

    <div class="Workspace">
        <div class="Container">
            <h2>Activity Logs</h2>
            <form method="GET" action="">
                <label for="start_date">Start Date:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                <label for="end_date">End Date:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                <input type="submit" value="Filter">
            </form>
            <div class="table-controls">
                <label>Show
                    <select id="entries" name="limit" onchange="updateEntries()">
                        <?php
                        foreach ([5, 10, 25, 50] as $option) {
                            echo "<option value=\"$option\" " . ($limit == $option ? 'selected' : '') . ">$option</option>";
                        }
                        ?>
                    </select> entries
                </label>
                <input type="text" id="search" placeholder="Search" onkeyup="filterTable()">
            </div>
            <table id="activityTable" class="styled-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Activity</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['Username']}</td>
                                    <td>{$row['Role']}</td>
                                    <td>{$row['Activity']}</td>
                                    <td>{$row['Timestamp']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No activity logs found for the selected date range.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination">
                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $start + 4);

                if ($page > 1) echo "<button onclick=\"window.location.href='?page=1&limit=$limit&start_date=$startDate&end_date=$endDate'\"><<</button>";
                if ($page > 1) echo "<button onclick=\"window.location.href='?page=" . ($page - 1) . "&limit=$limit&start_date=$startDate&end_date=$endDate'\">Prev</button>";
                for ($i = $start; $i <= $end; $i++) {
                    echo "<button onclick=\"window.location.href='?page=$i&limit=$limit&start_date=$startDate&end_date=$endDate'\" " . ($i == $page ? "class='active'" : "") . ">$i</button>";
                }
                if ($page < $totalPages) echo "<button onclick=\"window.location.href='?page=" . ($page + 1) . "&limit=$limit&start_date=$startDate&end_date=$endDate'\">Next</button>";
                if ($page < $totalPages) echo "<button onclick=\"window.location.href='?page=$totalPages&limit=$limit&start_date=$startDate&end_date=$endDate'\">>></button>";
                ?>
            </div>
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
        function updateEntries() {
            const entriesSelect = document.getElementById("entries");
            const selectedValue = entriesSelect.value;
            const url = new URL(window.location.href);
            url.searchParams.set('limit', selectedValue);
            window.location.href = url;
        }

        function filterTable() {
            const input = document.getElementById("search").value.toLowerCase();
            const rows = document.getElementById("tableBody").getElementsByTagName("tr");
            Array.from(rows).forEach(row => {
                const rowText = Array.from(row.getElementsByTagName("td")).map(cell => cell.textContent.toLowerCase()).join('');
                row.style.display = rowText.includes(input) ? "" : "none";
            });
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

<?php ob_end_flush(); // End output buffering ?>
