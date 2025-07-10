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

// Get today's day of the week (1=Monday, 2=Tuesday, etc.)
$today = date('N');

// Handle Add Class Form Submission
if ($_POST['action'] == 'add_class') {
    $class_name = $_POST['class_name'];
    $description = $_POST['description'];
    $trainer = $_POST['trainer'];
    $schedule_days = $_POST['schedule_days'];
    $schedule_time = $_POST['schedule_time'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $capacity = $_POST['capacity'];
    
    $sql = "INSERT INTO classes (class_name, description, trainer, schedule_days, schedule_time, price, duration, capacity, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssdii", $class_name, $description, $trainer, $schedule_days, $schedule_time, $price, $duration, $capacity);
    
    if ($stmt->execute()) {
        echo "<div style='color: green; margin: 10px 0;'>Class added successfully!</div>";
    } else {
        echo "<div style='color: red; margin: 10px 0;'>Error adding class: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Handle Add Schedule Form Submission
if ($_POST['action'] == 'add_schedule') {
    $class_id = $_POST['class_id'];
    $trainer_id = $_POST['trainer_id'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $sql = "INSERT INTO class_schedules (class_id, trainer_id, day_of_week, start_time, end_time, room, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiisssi", $class_id, $trainer_id, $day_of_week, $start_time, $end_time, $room, $is_active);
    
    if ($stmt->execute()) {
        echo "<div style='color: green; margin: 10px 0;'>Schedule added successfully!</div>";
    } else {
        echo "<div style='color: red; margin: 10px 0;'>Error adding schedule: " . $conn->error . "</div>";
    }
    $stmt->close();
}

// Get day name for display
function getDayName($day_number) {
    $days = ['', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    return $days[$day_number];
}

// Get all classes for dropdown
$classes_query = "SELECT * FROM classes ORDER BY class_name";
$classes_result = $conn->query($classes_query);

// Get all trainers for dropdown (assuming you have a trainers table)
$trainers_query = "SELECT DISTINCT trainer FROM classes WHERE trainer IS NOT NULL AND trainer != ''";
$trainers_result = $conn->query($trainers_query);

// Get today's schedule
$today_schedule_query = "
    SELECT 
        cs.*, 
        c.class_name, 
        c.description, 
        c.trainer, 
        c.price, 
        c.duration, 
        c.capacity
    FROM class_schedules cs
    JOIN classes c ON cs.class_id = c.id
    WHERE cs.day_of_week = ? AND cs.is_active = 1
    ORDER BY cs.start_time
";

$stmt = $conn->prepare($today_schedule_query);
$stmt->bind_param("i", $today);
$today_result = $stmt->execute();
$today_schedule = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Schedule Management</title>
</head>
<body>
    <h1>Gym Schedule Management</h1>
    
    <h2>Today's Schedule - <?php echo getDayName($today) . " (" . date('Y-m-d') . ")"; ?></h2>
    
    <?php if ($today_schedule->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Class Name</th>
                    <th>Trainer</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Room</th>
                    <th>Duration (min)</th>
                    <th>Capacity</th>
                    <th>Price</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $today_schedule->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['trainer']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['room']); ?></td>
                        <td><?php echo htmlspecialchars($row['duration']); ?></td>
                        <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No classes scheduled for today.</p>
    <?php endif; ?>
    
    <hr>
    
    <h2>Add New Class</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add_class">
        
        <table>
            <tr>
                <td><label for="class_name">Class Name:</label></td>
                <td><input type="text" id="class_name" name="class_name" required></td>
            </tr>
            <tr>
                <td><label for="description">Description:</label></td>
                <td><textarea id="description" name="description" rows="3" cols="50"></textarea></td>
            </tr>
            <tr>
                <td><label for="trainer">Trainer:</label></td>
                <td><input type="text" id="trainer" name="trainer" required></td>
            </tr>
            <tr>
                <td><label for="schedule_days">Schedule Days:</label></td>
                <td>
                    <select id="schedule_days" name="schedule_days" required>
                        <option value="">Select Days</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Monday,Wednesday,Friday">Monday, Wednesday, Friday</option>
                        <option value="Tuesday,Thursday">Tuesday, Thursday</option>
                        <option value="Saturday,Sunday">Weekend</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="schedule_time">Schedule Time:</label></td>
                <td><input type="text" id="schedule_time" name="schedule_time" placeholder="e.g., 6:00 PM - 7:00 PM" required></td>
            </tr>
            <tr>
                <td><label for="price">Price:</label></td>
                <td><input type="number" id="price" name="price" step="0.01" min="0" required></td>
            </tr>
            <tr>
                <td><label for="duration">Duration (minutes):</label></td>
                <td><input type="number" id="duration" name="duration" min="15" max="180" required></td>
            </tr>
            <tr>
                <td><label for="capacity">Capacity:</label></td>
                <td><input type="number" id="capacity" name="capacity" min="1" max="100" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Add Class">
                </td>
            </tr>
        </table>
    </form>
    
    <hr>
    
    <h2>Add Class Schedule</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add_schedule">
        
        <table>
            <tr>
                <td><label for="class_id">Select Class:</label></td>
                <td>
                    <select id="class_id" name="class_id" required>
                        <option value="">Select a Class</option>
                        <?php 
                        $classes_result->data_seek(0); // Reset result pointer
                        while($class = $classes_result->fetch_assoc()): ?>
                            <option value="<?php echo $class['id']; ?>">
                                <?php echo htmlspecialchars($class['class_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="trainer_id">Trainer ID:</label></td>
                <td><input type="number" id="trainer_id" name="trainer_id" min="1" required></td>
            </tr>
            <tr>
                <td><label for="day_of_week">Day of Week:</label></td>
                <td>
                    <select id="day_of_week" name="day_of_week" required>
                        <option value="">Select Day</option>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="7">Sunday</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="start_time">Start Time:</label></td>
                <td><input type="time" id="start_time" name="start_time" required></td>
            </tr>
            <tr>
                <td><label for="end_time">End Time:</label></td>
                <td><input type="time" id="end_time" name="end_time" required></td>
            </tr>
            <tr>
                <td><label for="room">Room:</label></td>
                <td>
                    <select id="room" name="room" required>
                        <option value="">Select Room</option>
                        <option value="Studio A">Studio A</option>
                        <option value="Studio B">Studio B</option>
                        <option value="Studio C">Studio C</option>
                        <option value="Weight Room">Weight Room</option>
                        <option value="Cardio Room">Cardio Room</option>
                        <option value="Yoga Room">Yoga Room</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="is_active">Active:</label></td>
                <td><input type="checkbox" id="is_active" name="is_active" checked></td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="submit" value="Add Schedule">
                </td>
            </tr>
        </table>
    </form>
    
    <hr>
    
    <h2>All Classes</h2>
    <?php 
    $all_classes_query = "SELECT * FROM classes ORDER BY created_at DESC";
    $all_classes_result = $conn->query($all_classes_query);
    
    if ($all_classes_result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Trainer</th>
                    <th>Schedule Days</th>
                    <th>Schedule Time</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Capacity</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php while($class = $all_classes_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $class['id']; ?></td>
                        <td><?php echo htmlspecialchars($class['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($class['trainer']); ?></td>
                        <td><?php echo htmlspecialchars($class['schedule_days']); ?></td>
                        <td><?php echo htmlspecialchars($class['schedule_time']); ?></td>
                        <td>$<?php echo htmlspecialchars($class['price']); ?></td>
                        <td><?php echo htmlspecialchars($class['duration']); ?> min</td>
                        <td><?php echo htmlspecialchars($class['capacity']); ?></td>
                        <td><?php echo htmlspecialchars($class['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No classes found.</p>
    <?php endif; ?>
    
    <hr>
    
    <h2>All Schedules</h2>
    <?php 
    $all_schedules_query = "
        SELECT 
            cs.*, 
            c.class_name,
            CASE cs.day_of_week
                WHEN 1 THEN 'Monday'
                WHEN 2 THEN 'Tuesday'
                WHEN 3 THEN 'Wednesday'
                WHEN 4 THEN 'Thursday'
                WHEN 5 THEN 'Friday'
                WHEN 6 THEN 'Saturday'
                WHEN 7 THEN 'Sunday'
            END as day_name
        FROM class_schedules cs
        JOIN classes c ON cs.class_id = c.id
        ORDER BY cs.day_of_week, cs.start_time
    ";
    $all_schedules_result = $conn->query($all_schedules_query);
    
    if ($all_schedules_result->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Room</th>
                    <th>Active</th>
                    <th>Trainer ID</th>
                </tr>
            </thead>
            <tbody>
                <?php while($schedule = $all_schedules_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $schedule['id']; ?></td>
                        <td><?php echo htmlspecialchars($schedule['class_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['day_name']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($schedule['room']); ?></td>
                        <td><?php echo $schedule['is_active'] ? 'Yes' : 'No'; ?></td>
                        <td><?php echo htmlspecialchars($schedule['trainer_id']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No schedules found.</p>
    <?php endif; ?>
    
</body>
</html>

<?php
$conn->close();
?>