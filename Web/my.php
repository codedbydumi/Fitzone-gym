<?php
// Start session with security settings
session_start([
    'cookie_httponly' => 1,
    'cookie_secure' => isset($_SERVER['HTTPS']), // Enable if using HTTPS
    'use_strict_mode' => 1
]);

// Database connection
$host = 'localhost';
$dbname = 'nalifitzone';
$username = 'root';
$password = '';

// Create connection with error handling
try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8mb4 for proper encoding
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['username']) && !empty($_SESSION['username']);
$currentUser = $isLoggedIn ? $_SESSION['username'] : '';

// Handle login redirect for class registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_to_register'])) {
    // Store the class ID in session for after login
    $_SESSION['register_class_id'] = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
    $_SESSION['login_message'] = "Please login to register for this class.";
    
    // Redirect to login page (assuming you have a login.php or dashboard login)
    header("Location: login.php"); // Change this to your actual login page
    exit();
}

// Handle class registration with prepared statements
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_class'])) {
    if (!$isLoggedIn) {
        $_SESSION['error'] = "Please login to register for classes!";
        header("Location: login.php"); // Redirect to login page
        exit();
    }
    
    // Validate and sanitize input
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    // Validate input
    if ($class_id <= 0 || $user_id <= 0) {
        $_SESSION['error'] = "Invalid class or user ID!";
        header("Location: classes.php");
        exit();
    }
    
    try {
        // Check if user is already registered for this class
        $check_stmt = $conn->prepare("SELECT * FROM class_registrations WHERE user_id = ? AND class_id = ?");
        if (!$check_stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        if (!$check_stmt->bind_param("ii", $user_id, $class_id)) {
            throw new Exception("Binding parameters failed: " . $check_stmt->error);
        }
        
        if (!$check_stmt->execute()) {
            throw new Exception("Error executing statement: " . $check_stmt->error);
        }
        
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['error'] = "You are already registered for this class!";
        } else {
            // Register user for class
            $register_stmt = $conn->prepare("INSERT INTO class_registrations (user_id, class_id, registration_date) VALUES (?, ?, NOW())");
            if (!$register_stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            
            if (!$register_stmt->bind_param("ii", $user_id, $class_id)) {
                throw new Exception("Binding parameters failed: " . $register_stmt->error);
            }
            
            if ($register_stmt->execute()) {
                $_SESSION['success'] = "Successfully registered for class!";
            } else {
                throw new Exception("Error registering for class: " . $conn->error);
            }
            
            $register_stmt->close();
        }
        
        $check_stmt->close();
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: classes.php");
    exit();
}

// Handle class unregistration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['unregister_class'])) {
    if (!$isLoggedIn) {
        $_SESSION['error'] = "Please login to unregister from classes!";
        header("Location: login.php");
        exit();
    }
    
    // Validate and sanitize input
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    // Validate input
    if ($class_id <= 0 || $user_id <= 0) {
        $_SESSION['error'] = "Invalid class or user ID!";
        header("Location: classes.php");
        exit();
    }
    
    try {
        // Unregister user from class
        $unregister_stmt = $conn->prepare("DELETE FROM class_registrations WHERE user_id = ? AND class_id = ?");
        if (!$unregister_stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        if (!$unregister_stmt->bind_param("ii", $user_id, $class_id)) {
            throw new Exception("Binding parameters failed: " . $unregister_stmt->error);
        }
        
        if ($unregister_stmt->execute()) {
            if ($unregister_stmt->affected_rows > 0) {
                $_SESSION['success'] = "Successfully unregistered from class!";
            } else {
                $_SESSION['error'] = "You were not registered for this class!";
            }
        } else {
            throw new Exception("Error unregistering from class: " . $conn->error);
        }
        
        $unregister_stmt->close();
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header("Location: classes.php");
    exit();
}

// Get user's registered classes if logged in
$userClasses = [];
$user_id = null;

if ($isLoggedIn) {
    try {
        // Use prepared statement to prevent SQL injection
        $user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$user_stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        if (!$user_stmt->bind_param("s", $currentUser)) {
            throw new Exception("Binding parameters failed: " . $user_stmt->error);
        }
        
        if (!$user_stmt->execute()) {
            throw new Exception("Error executing statement: " . $user_stmt->error);
        }
        
        $user_result = $user_stmt->get_result();
        
        if ($user_result->num_rows > 0) {
            $user_data = $user_result->fetch_assoc();
            $user_id = $user_data['id'];
            
            // Get registered classes with class details
            $registered_stmt = $conn->prepare("SELECT cr.*, c.class_name, cs.day_of_week, cs.start_time, cs.end_time 
                                            FROM class_registrations cr 
                                            JOIN classes c ON cr.class_id = c.id 
                                            LEFT JOIN class_schedules cs ON cr.class_id = cs.class_id
                                            WHERE cr.user_id = ?");
            if (!$registered_stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }
            
            if (!$registered_stmt->bind_param("i", $user_id)) {
                throw new Exception("Binding parameters failed: " . $registered_stmt->error);
            }
            
            if (!$registered_stmt->execute()) {
                throw new Exception("Error executing statement: " . $registered_stmt->error);
            }
            
            $registered_result = $registered_stmt->get_result();
            
            while ($row = $registered_result->fetch_assoc()) {
                $userClasses[] = $row;
            }
            
            $registered_stmt->close();
        }
        
        $user_stmt->close();
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
}

// Get user's registered class IDs for checking
$userRegisteredClasses = array_column($userClasses, 'class_id');

// Define class information
$classInfo = [
    1 => [
        'name' => 'Yoga for Beginners',
        'description' => 'Join us for a relaxing session focused on basic yoga poses and breathing techniques. Perfect for building flexibility and inner peace.',
        'schedule' => 'Mon & Wed, 6:00 PM - 7:00 PM',
        'price' => '$15 per session',
        'trainer' => 'Sarah Johnson',
        'duration' => '60 minutes',
        'emoji' => '🧘‍♀️'
    ],
    2 => [
        'name' => 'High-Intensity Interval Training (HIIT)',
        'description' => 'A fast-paced class designed to improve strength and endurance with short bursts of intense exercise followed by recovery periods.',
        'schedule' => 'Tue & Thu, 7:00 PM - 8:00 PM',
        'price' => '$20 per session',
        'trainer' => 'Mike Chen',
        'duration' => '60 minutes',
        'emoji' => '🔥'
    ],
    3 => [
        'name' => 'Strength Training',
        'description' => 'Build muscle and strength in this class, focusing on weights and resistance training techniques for all fitness levels.',
        'schedule' => 'Mon & Fri, 5:00 PM - 6:00 PM',
        'price' => '$18 per session',
        'trainer' => 'David Smith',
        'duration' => '60 minutes',
        'emoji' => '💪'
    ],
    4 => [
        'name' => 'Cardio Dance',
        'description' => 'Get your heart pumping with this fun and energetic dance class suitable for all levels. No dance experience required!',
        'schedule' => 'Saturdays, 10:00 AM - 11:00 AM',
        'price' => '$16 per session',
        'trainer' => 'Emma Wilson',
        'duration' => '60 minutes',
        'emoji' => '💃'
    ],
    5 => [
        'name' => 'Cardio Blast',
        'description' => 'High-energy cardio workout designed to burn calories and improve cardiovascular health through various aerobic exercises.',
        'schedule' => 'Wed & Fri, 6:00 PM - 7:00 PM',
        'price' => '$17 per session',
        'trainer' => 'Alex Rodriguez',
        'duration' => '60 minutes',
        'emoji' => '🏃‍♂️'
    ],
    6 => [
        'name' => 'Pilates',
        'description' => 'Focus on core strength, flexibility, and body awareness through controlled movements and breathing techniques.',
        'schedule' => 'Tue & Thu, 5:00 PM - 6:00 PM',
        'price' => '$19 per session',
        'trainer' => 'Lisa Parker',
        'duration' => '60 minutes',
        'emoji' => '🧘‍♂️'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FitZone fitness classes registration page">
    <title>FitZone - Our Fitness Classes</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href = "css/ll.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="navbar">
            <a href="index.php" class="logo">FitZone</a>
            <ul class="nav-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="my.php">CLASSES</a></li>
              <li><a href="trainers.php">TRAINERS</a></li>
                <li><a href="membership.html">MEMBERSHIP</a></li>
                <li><a href="blog.html">BLOG</a></li>
                <li><a href="contact.php">CONTACT</a></li>
            </ul>
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search classes...">
                <button class="search-btn">Search</button>
            </div>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>OUR FITNESS CLASSES</h1>
            <p>Transform your body and mind with our expert-led fitness classes designed for all levels</p>
            <?php if (!$isLoggedIn): ?>
                <a href="login.php" class="cta-button">Login to Register</a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Display success/error messages
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-error'>" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "</div>";
            unset($_SESSION['error']);
        }
        ?>

        <!-- Classes Grid -->
        <div class="classes-grid">
            <?php foreach ($classInfo as $id => $class): ?>
            <div class="class-card scroll-reveal">
                <div class="class-image"><?php echo htmlspecialchars($class['emoji'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="class-content">
                    <h3 class="class-title"><?php echo htmlspecialchars($class['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="class-description"><?php echo htmlspecialchars($class['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="class-details">
                        <div class="class-detail">
                            <span><strong>Schedule:</strong></span>
                            <span><?php echo htmlspecialchars($class['schedule'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="class-detail">
                            <span><strong>Price:</strong></span>
                            <span><?php echo htmlspecialchars($class['price'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="class-detail">
                            <span><strong>Trainer:</strong></span>
                            <span><?php echo htmlspecialchars($class['trainer'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="class-detail">
                            <span><strong>Duration:</strong></span>
                            <span><?php echo htmlspecialchars($class['duration'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                    
                    <?php if ($isLoggedIn): ?>
                        <?php if (in_array($id, $userRegisteredClasses)): ?>
                            <form method="POST">
                                <input type="hidden" name="class_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <button type="submit" name="unregister_class" class="register-btn">Unregister</button>
                            </form>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="class_id" value="<?php echo $id; ?>">
                                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                                <button type="submit" name="register_class" class="register-btn">Register Now</button>
                            </form>
                        <?php endif; ?>
                    <?php else: ?>
                        <!-- Modified button for non-logged-in users -->
                        <form method="POST">
                            <input type="hidden" name="class_id" value="<?php echo $id; ?>">
                            <button type="submit" name="login_to_register" class="register-btn login-required">
                                Login to Register
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- My Registered Classes Section -->
        <?php if ($isLoggedIn && !empty($userClasses)): ?>
            <div class="table-section scroll-reveal">
                <h2 class="table-title">My Registered Classes</h2>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Schedule</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userClasses as $class): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($class['class_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php 
                                    if (!empty($class['day_of_week']) && !empty($class['start_time']) && !empty($class['end_time'])) {
                                        echo htmlspecialchars($class['day_of_week'], ENT_QUOTES, 'UTF-8') . ', ' . 
                                             date('g:i A', strtotime($class['start_time'])) . ' - ' . 
                                             date('g:i A', strtotime($class['end_time']));
                                    } else {
                                        echo 'Schedule TBD';
                                    }
                                    ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($class['registration_date'])); ?></td>
                                <td><span class="status-badge status-registered">Registered</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Class Schedule Section -->
        <div class="table-section scroll-reveal">
            <h2 class="table-title">Weekly Class Schedule</h2>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>5:00 PM</strong></td>
                        <td>Strength Training</td>
                        <td>Pilates</td>
                        <td>-</td>
                        <td>Pilates</td>
                        <td>Strength Training</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><strong>6:00 PM</strong></td>
                        <td>Yoga for Beginners</td>
                        <td>-</td>
                        <td>Yoga for Beginners<br>Cardio Blast</td>
                        <td>-</td>
                        <td>Cardio Blast</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><strong>7:00 PM</strong></td>
                        <td>-</td>
                        <td>HIIT</td>
                        <td>-</td>
                        <td>HIIT</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><strong>10:00 AM</strong></td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>Cardio Dance</td>
                        <td>-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Scroll reveal animation
        function revealOnScroll() {
            const reveals = document.querySelectorAll('.scroll-reveal');
            
            reveals.forEach(reveal => {
                const windowHeight = window.innerHeight;
                const elementTop = reveal.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    reveal.classList.add('revealed');
                }
            });
        }

        // Search functionality
        function setupSearch() {
            const searchInput = document.querySelector('.search-input');
            const searchBtn = document.querySelector('.search-btn');
            const classCards = document.querySelectorAll('.class-card');

            function performSearch() {
                const searchTerm = searchInput.value.toLowerCase();
                
                classCards.forEach(card => {
                    const title = card.querySelector('.class-title').textContent.toLowerCase();
                    const description = card.querySelector('.class-description').textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        card.style.display = 'block';
                        card.style.opacity = '1';
                    } else {
                        card.style.display = searchTerm === '' ? 'block' : 'none';
                        card.style.opacity = searchTerm === '' ? '1' : '0';
                    }
                });
            }

            searchBtn.addEventListener('click', performSearch);
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        }

        // Header scroll effect
        function setupHeaderScroll() {
            const header = document.querySelector('.header');
            
            window.addEventListener('scroll', () => {
                if (window.scrollY > 100) {
                    header.style.background = 'rgba(26, 26, 26, 0.95)';
                    header.style.backdropFilter = 'blur(20px)';
                } else {
                    header.style.background = 'rgba(42, 42, 42, 0.95)';
                    header.style.backdropFilter = 'blur(20px)';
                }
            });
        }

        // Loading animation
        function setupLoadingAnimation() {
            const elements = document.querySelectorAll('.class-card, .table-section');
            
            elements.forEach((element, index) => {
                element.classList.add('loading');
                element.style.animationDelay = `${index * 0.1}s`;
            });
        }

        // Initialize all functions
        document.addEventListener('DOMContentLoaded', function() {
            setupLoadingAnimation();
            setupSearch();
            setupHeaderScroll();
            revealOnScroll();
        });

        window.addEventListener('scroll', revealOnScroll);

        // Form submission enhancement
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = form.querySelector('button[type="submit"]');
                if (button && !button.classList.contains('registered')) {
                    // Special handling for login redirect buttons
                    if (button.name === 'login_to_register') {
                        button.innerHTML = '<span class="spinner"></span> Redirecting to Login...';
                    } else {
                        button.innerHTML = '<span class="spinner"></span> Processing...';
                    }
                    button.style.opacity = '0.7';
                    button.disabled = true;
                }
            });
        });

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>