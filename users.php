<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "nalifitzone");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM users WHERE id = $delete_id");
    header("Location: users.php"); // Refresh the page after deletion
    exit();
}

// Fetch all users from the users table
$result = $conn->query("SELECT id, name, email, username, gender, date, usertype FROM users");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <h2 class="logo">Admin Dashboard</h2>
            <ul class="menu">
                <li><a href="messages.php">Customer Messages</a></li>
                <li><a href="schedules.php">Manage Schedules</a></li>
                <li><a href="user.php">Manage Users</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Manage Users Section -->
            <section id="users" class="users-section">
                <h2>Manage Users</h2>
                <a href="sinew.php"><button id="addUserButton">Add New User</button></a>

                <!-- User Table -->
                <div class="user-list">
                    <h3>All Users</h3>
                    <table border="1" cellpadding="10">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Gender</th>
                                <th>Date</th>
                                <th>User Type</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['email'] . "</td>";
                                    echo "<td>" . $row['username'] . "</td>";
                                    echo "<td>" . $row['gender'] . "</td>";
                                    echo "<td>" . $row['date'] . "</td>";
                                    echo "<td>" . $row['usertype'] . "</td>";
                                    echo "<td><a href='users.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</a></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7'>No users found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
