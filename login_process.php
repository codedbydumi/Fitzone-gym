<?php
// Start the session to store user information and error messages
session_start();

// Database connection configuration
$host = 'localhost';
$dbname = 'nalifitzone';
$username = 'root';
$password = '';

// Create a new database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form input values
    $username = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);
    
    // SQL query to check if the user exists
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
        
        // Verify password (use password_verify if passwords are hashed)
        if ($password === $user['password']) {  // Replace this with password_verify for hashed passwords
            // Store user information in session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['usertype'] = $user['usertype'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect to home page after successful login
            header("Location: index.php");
            exit();
        } else {
            // Set an error message if the password is incorrect
            $_SESSION['error'] = "Incorrect password!";
        }
    } else {
        // Set an error message if the username is not found
        $_SESSION['error'] = "Username not found!";
    }
    
    // Redirect back to the home page to display the error
    header("Location: index.php");
    exit();
}

// Close the database connection
$conn->close();
?>