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
            

            <form action="" method="POST">
                <div class="container">
                    <div class="Column0">
                        <h2> <span style="color: #16348C;"> &#10074 </span> PATIENT PROFILING </h2> <br>
                    </div>

                    <div class="sample">
                        <div class="Column1">
                            <fieldset>
                                <div class="profile-section">
                                    <h3>Personal Details</h3>
                                    <div class="profile-photo">
                                        <img src="Images/Formal ni Charles.PNG" alt="Patient Photo">
                                        <p>Patient No: <em>0001-24</em></p>
                                    </div>

                                    <div class="basic-info">
                                        <input type="text" name="LastName" id="LastName" placeholder="Last Name" required>
                                        <input type="text" name="FirstName" id="FirstName" placeholder="First Name" required>
                                        <input type="text" name="MiddleName" id="MiddleName" placeholder="Middle Name">
                                        <input type="text" name="Ext" id="Ext" placeholder="Ext">
                                    </div>

                                    <div class="sex">
                                        <label>Sex</label>
                                        <input type="radio" name="sex" value="male"> Male
                                        <input type="radio" name="sex" value="female"> Female
                                    </div>

                                    <div>
                                        <label>Birthday</label>
                                        <input type="date">
                                    </div> 

                                    <div class="field-group">
                                        <input type="text" placeholder="Civil Status">
                                        <input type="text" placeholder="Occupation">
                                        <input type="text" placeholder="Educational Attainment">
                                        <input type="text" placeholder="Religion">
                                        <input type="text" placeholder="Blood Type">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                            
                        <div class="Column2">
                            <fieldset>
                                <div class="field-group">
                                    <h3>Contact Details</h3>
                                    <input type="text" placeholder="Phone Number">
                                    <input type="text" placeholder="Email">
                                </div>
                            </fieldset>

                            <fieldset>
                                <div class="field-group">
                                    <h3>Present Address</h3>
                                    <input type="text" placeholder="House Number">
                                    <input type="text" placeholder="Street">
                                    <input type="text" placeholder="Subdivision/Village">
                                    <input type="text" placeholder="Barangay">
                                    <input type="text" placeholder="Municipality">
                                    <input type="text" placeholder="Zip Code">
                                </div>
                            </fieldset>
                                
                            <fieldset>
                                <div class="field-group">
                                    <h3>Identification Card</h3>
                                    <input type="text" placeholder="ID Number">
                                    <select>
                                        <option>ID Issuer</option>
                                        <option>Option 1</option>
                                        <option>Option 2</option>
                                    </select>
                                </div>
                            </fieldset>
                                
                            <fieldset>
                                <div class="field-group">
                                    <h3>Resident Type</h3>
                                    <input type="radio" name="resident-type" value="permanent"> Permanent
                                    <input type="radio" name="resident-type" value="transient"> Transient
                                    <input type="radio" name="resident-type" value="current"> Current
                                </div>
                            </fieldset>
                                
                            <fieldset>
                                <div class="field-group">
                                    <h3>Emergency Contact</h3>
                                    <input type="text" placeholder="Contact Person">
                                    <input type="text" placeholder="Relationship">
                                    <input type="text" placeholder="Contact Number">
                                </div>
                                </fieldset>
                        </div>
                    </div>
                        <div class="actions">
                            <button type="button">Clear</button>
                            <button type="button">Update</button>
                            <button type="button">Save</button>
                        </div>
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

        </script>
    </body>
</html>

<style>
    .right {
    margin-left: 15vw; /* Offset left by the width of the navbar */
    padding-top: 20px;
    padding-left: 20px;
    background: linear-gradient(to bottom right, #62cff4, #2c67f2);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow-y: auto; /* Allows scrolling within the main content area */
    min-height: 100vh; /* Ensure it fills the viewport height */
    height: auto; /* Let content define the height */
    backdrop-filter: blur(10px); /* Applies a 10px blur to the background */
    -webkit-backdrop-filter: blur(10px);
}

.container
{
    background-color: white;
    padding: 2vh;
}

.sample {
    display: flex;
}

fieldset
{
    border-radius: 20px;
    display: flex;
    width: 90%;
    border: 1px solid #000;
    margin-bottom: 3vh;
}

.profile-photo
{
    border-radius: 50%;
    height: 13vw;
    width: 13vw;
    overflow: hidden;          /* Ensures the image doesn’t overflow the circle */
    display: flex;
    background-position: center;
    margin-bottom: 2vh;
}

.profile-photo img
{
    height: auto;

}

h2 {
    color: #16348C;
}

input[type="text"],
input[type="date"],
input[type="tel"],
input[type="email"],
select {
    width: 20%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

input[type="radio"] {
    margin-right: 5px;
}


button {
    background-color: #16348C;
    color: white;
    border: 1px solid #16348C;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
    margin: 1vh;
    width: 5vw;
    height: 5vh;
    border-radius: 10px;
}

button:hover {
    background-color: #fff;
    border: 1px solid #16348C;
    color: #000;
    transition: all 0.5 ease;
}

</style>