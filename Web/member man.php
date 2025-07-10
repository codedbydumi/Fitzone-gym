<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members Dashboard</title>
    <link rel="stylesheet" href="mem gym manag.css">
</head>
<body>
    <div class="header">
        <h1>Members Management Dashboard</h1>
    </div>
    <div class="nav">
        <a href="gym man.html">Home</a>
        <a href="mem gym manag.html">Members</a>
        <a href="pay gym mang.html">Payments</a>
        <a href="shedule gym mang.html">Schedules</a>
        <a href="reports gym mang.html">Reports</a>
    </div>
    <div class="main-content">
        <h2>Member List</h2>
        <table class="members-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Active Status</th>
                    <th>Membership Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "nalifitzone";

                $conn = new mysqli($servername, $username, $password, $dbname);

                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Query to join users and memberships table
                $sql = "
                    SELECT memberships.member_id, users.name, memberships.status, memberships.membership_type
                    FROM memberships
                    INNER JOIN users ON memberships.member_id = users.id
                ";
                
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data for each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["member_id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["status"] . "</td>";
                        echo "<td>" . $row["membership_type"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No members found</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
