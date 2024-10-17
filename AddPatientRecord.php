<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> BHW | Add Patient Record </title>
    <link rel="stylesheet" href="AddPatientRecord.css">
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
    

    <!-- Modal HTML -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Are you sure you want to log out?</p>
            <button id="confirmLogout">Yes</button>
            <button id="cancelLogout">No</button>
        </div>
    </div>

<?php
require 'databaseconnection.php';
require 'Auth.php'; // Check if user is logged in

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT id, Lastname, Firstname, Middlename FROM tblpatientprofile WHERE Lastname LIKE ? OR Firstname LIKE ? OR Middlename LIKE ? LIMIT 5";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $suggestions = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $suggestions[] = [
                'id' => $row['id'],
                'fullname' => $row['Lastname'] . ', ' . $row['Firstname'] . ' ' . $row['Middlename']
            ];
        }
    }

    // Return the suggestions as a JSON response
    echo json_encode($suggestions);
}

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    // Fetch patient data from the database
    $sql = "SELECT * FROM patientprofile WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $patient = $result->fetch_assoc();
        // Extract patient details
        $lastname = $patient['Lastname'];
        $firstname = $patient['Firstname'];
        $middlename = $patient['Middlename'];
        $extension = $patient['Extension'];
        $photo = $patient['photo']; // Assume this is the path to the photo
        $emerg_name = $patient['EmergName'];
        $relationship = $patient['Relation'];
        $emerg_num = $patient['EmergNum'];
    }
}
?>



<form action="" method="POST">
    <div class="right">
        <div class="column1">
            <div class="PatientPersonalInfo">
                <h4> Personal Information </h4>
                <div class="PatientPhoto" id="photo-container" style="background-image: url('<?php echo $photo; ?>');">
                    <!-- File input is visually hidden but clickable -->
                    <input type="file" id="fileInput" name="patient_photo" accept="image/*" style="opacity: 0; position: absolute; width: 100%; height: 100%; cursor: pointer;">
                </div>
                <div class="PatientInfo">
                    <div class="InfoPatient">
                        <input type="text" id="Lastname" name="Lastname" placeholder="Lastname" value="<?php echo isset($lastname) ? $lastname : ''; ?>" required>
                    </div>
                    
                    <div class="InfoPatient">
                        <input type="text" id="Firstname" name="Firstname" placeholder="Firstname" value="<?php echo isset($firstname) ? $firstname : ''; ?>" required>
                    </div>

                    <div class="InfoPatient">
                        <input type="text" id="Middlename" name="Middlename" placeholder="Middlename" value="<?php echo isset($middlename) ? $middlename : ''; ?>" required>
                    </div>

                    <div class="InfoPatient">
                        <input type="text" id="Extension" name="Extension" placeholder="Extension" value="<?php echo isset($extension) ? $extension : ''; ?>" required>
                    </div>
                </div>
            </div>

            <div class="Vitals">
                <h4> Vitals </h4>
                <div class="vitalscol1">
                    <div class="Col1Vitals">
                        <input type="text" id="Height" name="Height" placeholder="Height" required>
                    </div>
                    
                    <div class="Col1Vitals">
                        <input type="text" id="Weight" name="Weight" placeholder="Weight" required>
                    </div>

                    <div class="Col1Vitals">
                        <input type="text" id="HeartRate" name="HeartRate" placeholder="Heart Rate" required>
                    </div>
                    
                    <div class="Col1Vitals">
                        <input type="text" id="BloodPressure" name="BloodPressure" placeholder="Blood Pressure" required>
                    </div>
                </div>

                <div class="vitalcol1">
                    <div class="Col1Vitals">
                        <input type="text" id="RespiratoryRate" name="RespiratoryRate" placeholder="Respiratory Rate" required>
                    </div>

                    <div class="Col1Vitals">
                        <input type="text" id="BodyTemp" name="BodyTemp" placeholder="Body Temperature" required>
                    </div>
                    
                    <div class="Col1Vitals">
                        <input type="date" id="LMP" name="LMP">
                    </div>
                </div>
            </div>

            <div class="EmergCon">
                <h4> Emergency Contact </h4>
                <div class="Col1Vitals">
                    <input type="text" id="EmergName" name="EmergName" placeholder="Emergency Contact Person" value="<?php echo isset($emerg_name) ? $emerg_name : ''; ?>" required>
                </div>
                
                <div class="Col1Vitals">
                    <input type="text" id="Relation" name="Relation" placeholder="Relationship" value="<?php echo isset($relationship) ? $relationship : ''; ?>" required>
                </div>

                <div class="Col1Vitals">
                    <input type="text" id="EmergNum" name="EmergNum" placeholder="Emergency Contact Number" value="<?php echo isset($emerg_num) ? $emerg_num : ''; ?>" required>
                </div>
            </div>
        </div>

        <div class="column2">
            <div class="PastIllnesses">
                <h4> Past Illnesses </h4>
                <input type="checkbox" id="epilepsy" name="medical_history[]" value="Epilepsy">
                <label for="epilepsy">Epilepsy</label><br>

                <input type="checkbox" id="depression" name="medical_history[]" value="Depression">
                <label for="depression">Depression</label><br>

                <input type="checkbox" id="asthma" name="medical_history[]" value="Asthma">
                <label for="asthma">Asthma</label><br>

                <input type="checkbox" id="hypertension" name="medical_history[]" value="Hypertension">
                <label for="hypertension">Hypertension</label><br>

                <input type="checkbox" id="Diabetes" name="medical_history[]" value="Diabetes">
                <label for="diabetes">Diabetes</label><br>

                <input type="checkbox" id="hepatitis" name="medical_history[]" value="Hepatitis">
                <label for="hepatitis">Hepatitis</label><br>

                <input type="checkbox" id="ptb" name="medical_history[]" value="PTB">
                <label for="ptb">Pulmonary Tuberculosis (PTB)</label><br>

                <input type="checkbox" id="measles" name="medical_history[]" value="Measles">
                <label for="measles">Measles</label><br>

                <input type="checkbox" id="chickenpox" name="medical_history[]" value="Chickenpox">
                <label for="chickenpox">Chickenpox</label><br>

                <input type="checkbox" id="typhoid" name="medical_history[]" value="Typhoid Fever">
                <label for="typhoid">Typhoid Fever</label><br>

                <label for="other_illnesses"><strong>Other Illnesses:</strong></label><br>
                <textarea id="other_illnesses" name="other_illnesses" rows="4" cols="50" placeholder="Please specify other illnesses..."></textarea><br><br>
            </div>

            <div class="KnownAllergies">
                <h4> Known Allergies </h4>
                <input type="checkbox" id="seafood" name="allergies[]" value="Seafood">
                <label for="seafood">Seafood</label><br>

                <input type="checkbox" id="dairy" name="allergies[]" value="Dairy">
                <label for="dairy">Dairy</label><br>

                <input type="checkbox" id="peanuts" name="allergies[]" value="Peanuts">
                <label for="peanuts">Peanuts</label><br>

                <input type="checkbox" id="antibiotics" name="allergies[]" value="Antibiotics">
                <label for="antibiotics">Antibiotics</label><br>

                <input type="checkbox" id="pollen" name="allergies[]" value="Pollen/Dust">
                <label for="pollen">Pollen/Dust</label><br>

                <input type="checkbox" id="insect" name="allergies[]" value="Insect Stings">
                <label for="insect">Insect Stings</label><br>

                <input type="checkbox" id="latex" name="allergies[]" value="Latex">
                <label for="latex">Latex</label><br>

                <input type="checkbox" id="hair_dyes" name="allergies[]" value="Hair Dyes">
                <label for="hair_dyes">Hair Dyes</label><br>

                <input type="checkbox" id="cleaning" name="allergies[]" value="Cleaning Products">
                <label for="cleaning">Cleaning Products</label><br>

                <!-- Textbox for other allergies -->
                <label for="other_allergies"><strong>Other Allergies:</strong></label><br>
                <textarea id="other_allergies" name="other_allergies" rows="3" cols="50" placeholder="Please specify other allergies..."></textarea><br><br>
            </div>
        </div>
    </div>
</form>

<form action="" method="GET">
    <input type="text" class="search-bar" name="query" id="search-input" placeholder="Search patients..." autocomplete="off" required  >
    <button type="submit" class="search-button">Search</button>
    <div id="suggestions" class="suggestions"></div>
</form>


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
                    window.location.href = 'Login.php'; // Redirect after successful logout
                } else {
                    console.error('Logout failed');
                }
            }).catch(error => {
                console.error('Error:', error);
            });
        });

        document.getElementById('photo-container').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

// Handle file input change
document.getElementById('fileInput').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-container').style.backgroundImage = 'url(' + e.target.result + ')';
                };
                reader.readAsDataURL(file);
            }
        });

document.getElementById('search-input').addEventListener('input', function() {
    const query = this.value;

    if (query.length > 1) {  // Fetch suggestions if more than 1 character is typed
        fetch(`search.php?query=${query}`)
        .then(response => response.json())
        .then(data => {
            const suggestionsDiv = document.getElementById('suggestions');
            suggestionsDiv.innerHTML = '';  // Clear previous suggestions

            data.forEach(item => {
                const suggestionItem = document.createElement('div');
                suggestionItem.classList.add('suggestion-item');
                suggestionItem.innerHTML = `<a href="AddPatientRecord.php?id=${item.id}">${item.fullname}</a>`;
                suggestionsDiv.appendChild(suggestionItem);
            });
        });
    } else {
        document.getElementById('suggestions').innerHTML = '';  // Clear suggestions when input is cleared
    }
});
    </script>
</body>
</html>
