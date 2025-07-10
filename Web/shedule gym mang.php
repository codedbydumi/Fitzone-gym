<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Dashboard</title>
    <link rel="stylesheet" href="shedule gym mang.css">
</head>
<body>
    <div class="header">
        <h1>Schedule Management Dashboard</h1>
    </div>
    <div class="nav">
        <a href="gym man.html">Home</a>
        <a href="member man.php">Members</a>
        <a href="pay gym mang.html">Payments</a>
        <a href="shedule gym mang.php">Schedules</a>
        <a href="reports gym mang.html">Reports</a>
    </div>
    <div class="main-content">
        <h2>Class and Trainer Schedules</h2>
        <div class="access-message">
            <p><strong>Note:</strong> This area is restricted to management only. Ensure your login system checks user roles before accessing.</p>
        </div>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Trainer</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection details
                $servername = "localhost";
                $username = "root"; // Update with your database username
                $password = ""; // Update with your database password
                $dbname = "nalifitzone"; // Update with your database name

                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // SQL query to fetch schedule data
                $sql = "SELECT class_name, trainer, day_of_week, TIME_FORMAT(time, '%h:%i %p') as formatted_time, duration FROM class_schedules";
                $result = $conn->query($sql);

                // Display each row from the table
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $row["class_name"] . "</td>
                                <td>" . $row["trainer"] . "</td>
                                <td>" . $row["day_of_week"] . "</td>
                                <td>" . $row["formatted_time"] . "</td>
                                <td>" . $row["duration"] . "</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No schedules available</td></tr>";
                }

                // Close the database connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
