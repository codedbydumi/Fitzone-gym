<?php
// Start session and include DB connection
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

// Initialize variables
$username = $currentPassword = $newPassword = $confirmPassword = "";
$errorMessage = "";
$successMessage = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate new password and confirm password
    if ($newPassword !== $confirmPassword) {
        $errorMessage = "New password and confirmation do not match.";
    } else {
        // Fetch the user's current password from the database using the entered username
        $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($storedPassword);
            $stmt->fetch();

            // Verify the current password
            if ($currentPassword === $storedPassword) {
                // Update the new password directly in the database (without hashing)
                $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
                $updateStmt->bind_param("ss", $newPassword, $username);
                
                if ($updateStmt->execute()) {
                    // If successful, set success message
                    $successMessage = "Password updated successfully!";
                } else {
                    $errorMessage = "Error updating password. Please try again.";
                }
                $updateStmt->close();
            } else {
                $errorMessage = "inc--?";
            }
        } else {
            $errorMessage = "Username not found.";
        }

        $stmt->close();
    }
    $conn->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Change your password securely.">
    <title>Change Password</title>
    <link rel="stylesheet" href="change password.css">
</head>
<body>
    <header>
        <h1>Change Password</h1>
    </header>

    <main>
        <section class="change-password-form">
            <h2>Update Your Password</h2>
            <form id="changePasswordForm" action="change password.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="currentPassword">Current Password:</label>
                    <input type="password" id="currentPassword" name="currentPassword" required>
                </div>

                <div class="form-group">
                    <label for="newPassword">New Password:</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>

                <div class="form-group">
                    <button type="submit">Change Password</button>
                </div>

                <!-- Display error or success message -->
                <?php if ($errorMessage != ""): ?>
                    <p id="error-message" style="color:red;"><?php echo $errorMessage; ?></p>
                <?php elseif ($successMessage != ""): ?>
                    <p id="success-message" style="color:green;"><?php echo $successMessage; ?></p>
                <?php endif; ?>
            </form>
        </section>
    </main>

    <footer>
        <p>Need help? <a href="contact.html">Contact Support</a></p>
    </footer>
</body>
</html>
