<?php
$servername = "localhost"; // Replace with your server
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "nalifitzone"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
