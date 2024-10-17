<?php
// Assuming the Access database is named "example.mdb" and is located in the same directory as this script.
$dbName = "example.mdb";
// Connect to the database
$conn = new COM("ADODB.Connection");
$conn->Open("Provider=Microsoft.Jet.OLEDB.4.0; Data Source=$AvaratCapstoneDatabase");

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];

// Insert data into Access database
$sql = "INSERT INTO TableName (Name, Email) VALUES ('$name', '$email')";
$conn->Execute($sql);

// Close connection
$conn->Close();

// Redirect back to the form
header("Location: index.html");
exit();
?>
