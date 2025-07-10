<?php
// Start session to store any error messages
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
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $birthday = $conn->real_escape_string($_POST['birthday']);
    $user_type = $conn->real_escape_string($_POST['user_type']);
    $password = $conn->real_escape_string($_POST['password']); // Store password as entered (no hashing)

    // SQL query to insert the user data into the users table
    $sql = "INSERT INTO users (name, email, username, gender, date, usertype, password) 
            VALUES ('$name', '$email', '$username', '$gender', '$birthday', '$user_type', '$password')";

    // Execute the query and check if successful
    if ($conn->query($sql) === TRUE) {
        // Redirect to index.php on successful signup
        header("Location: index.php");
        exit();
    } else {
        // Store the error in session and redirect to signup page
        $_SESSION['error'] = "Error: " . $conn->error;
        header("Location: signup.php");
        exit();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            object-fit: cover;
        }
        .container {
            max-width: 400px;
            margin: auto;
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<video class="video-background" autoplay muted loop>
    <source src="sign up.mp4" type="video/mp4">
</video>

<div class="container">
    <h2>Sign Up</h2>
    
    <?php
    // Display error message if it's set
    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']); // Clear the error after displaying
    }
    ?>

    <form action="signup.php" method="POST">
        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="gender" required>
            <option value="" disabled selected>Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        <input type="date" name="birthday" required>
        <select name="user_type" required>
            <option value="" disabled selected>Select User Type</option>
            <option value="admin">Admin</option>
            <option value="Staff">Gym Management</option>
            <option value="Customer">Customer</option>
        </select>
        <button type="submit">Sign Up</button>
    </form>
</div>

</body>
</html>
