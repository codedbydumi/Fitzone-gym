<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = "";

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Check for empty fields (optional, since HTML 'required' already handles this)
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Retrieve user ID based on username
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($user_id);
                $stmt->fetch();

                // Insert message into queries table
                $query_stmt = $conn->prepare("INSERT INTO queries (user_id, query_text, status) VALUES (?, ?, 'pending')");
                if ($query_stmt) {
                    $query_stmt->bind_param("is", $user_id, $message);
                    
                    if ($query_stmt->execute()) {
                        $success_message = "Message sent successfully!";
                    } else {
                        $success_message = "Failed to send message. Please try again. Error: " . $query_stmt->error;
                    }
                    $query_stmt->close();
                } else {
                    $success_message = "Failed to prepare query. Error: " . $conn->error;
                }
            } else {
                $success_message = "Username not found.";
            }
            $stmt->close();
        } else {
            $success_message = "Failed to prepare user lookup. Error: " . $conn->error;
        }
    } else {
        $success_message = "Please fill in all required fields.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone - Contact Us</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: #ffffff;
            min-height: 100vh;
        }

        .main {
            width: 100%;
            min-height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23000" width="1200" height="800"/><g fill-opacity="0.1"><polygon fill="%23fff" points="957.5,500 1200,800 715,800"/><polygon fill="%23fff" points="400,600 657.5,800 142.5,800"/></g></svg>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .navbar {
            width: 100%;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 100px;
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            z-index: 1000;
        }

        .logo {
            color: #ff6b35;
            font-size: 35px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .menu ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .menu ul li a {
            text-decoration: none;
            color: #ffffff;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        .menu ul li a:hover {
            color: #ff6b35;
        }

        .menu ul li a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ff6b35;
            transition: width 0.3s;
        }

        .menu ul li a:hover::after {
            width: 100%;
        }

        .search {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .srch {
            padding: 8px 15px;
            border: 2px solid #ff6b35;
            background: transparent;
            color: #ffffff;
            border-radius: 25px;
            outline: none;
        }

        .btn {
            padding: 8px 20px;
            background: #ff6b35;
            color: #ffffff;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #e55a2b;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 120px 20px 50px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .contact-header h1 {
            font-size: 48px;
            color: #ff6b35;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .contact-header p {
            font-size: 20px;
            color: #cccccc;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .contact-info {
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .contact-info h2 {
            color: #ff6b35;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        .contact-methods {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .contact-item {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #ff6b35;
            transition: transform 0.3s;
        }

        .contact-item:hover {
            transform: translateX(10px);
        }

        .contact-item h3 {
            color: #ff6b35;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .contact-item p {
            color: #cccccc;
            font-size: 16px;
        }

        .contact-item a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .contact-item a:hover {
            color: #ff6b35;
        }

        .message-form {
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .message-form h2 {
            color: #ff6b35;
            font-size: 28px;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #ffffff;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: #ffffff;
            border-radius: 10px;
            outline: none;
            transition: border-color 0.3s;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #ff6b35;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #999999;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #ff6b35, #e55a2b);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
        }

        .success-message {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .map-section {
            background: rgba(255,255,255,0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            text-align: center;
        }

        .map-section h2 {
            color: #ff6b35;
            font-size: 28px;
            margin-bottom: 30px;
        }

        .map-placeholder {
            background: rgba(255,255,255,0.05);
            height: 300px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cccccc;
            font-size: 18px;
            border: 2px dashed rgba(255,255,255,0.3);
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 0 20px;
            }
            
            .menu ul {
                gap: 15px;
            }
            
            .contact-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .contact-header h1 {
                font-size: 36px;
            }
            
            .container {
                padding: 100px 20px 50px;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <div class="navbar">
            <div class="icon">
                <h2 class="logo">FitZone</h2>
            </div>
            <div class="menu">
                <ul>
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="my.php">CLASSES</a></li>
                    <li><a href="trainers.php">TRAINERS</a></li>
                    <li><a href="membership.html">MEMBERSHIP</a></li>
                    <li><a href="blog.html">BLOG</a></li>
                    <li><a href="contact.php">CONTACT</a></li>
                </ul>
            </div>
            <div class="search">
                <input class="srch" type="search" placeholder="Search...">
                <button class="btn">Search</button>
            </div>
        </div>

        <div class="container">
            <div class="contact-header">
                <h1>Contact Us</h1>
                <p>Ready to start your fitness journey? Get in touch with us and let's make your fitness goals a reality!</p>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <h2>Get in Touch</h2>
                    <div class="contact-methods">
                        <div class="contact-item">
                            <h3>📞 Phone</h3>
                            <p><a href="tel:+123456789">+1 234 567 89</a></p>
                        </div>
                        <div class="contact-item">
                            <h3>📧 Email</h3>
                            <p><a href="mailto:info@fitzone.com">info@fitzone.com</a></p>
                        </div>
                        <div class="contact-item">
                            <h3>📍 Address</h3>
                            <p>123 Fitness St, Fit City, FC 12345</p>
                        </div>
                        <div class="contact-item">
                            <h3>🕐 Hours</h3>
                            <p>Mon-Fri: 6:00 AM - 10:00 PM<br>
                               Sat-Sun: 8:00 AM - 8:00 PM</p>
                        </div>
                    </div>
                </div>

                <div class="message-form">
                    <h2>Send Us a Message</h2>
                    
                    <form action="contact.php" method="post">
                        <div class="form-group">
                            <label for="name">Username:</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea id="message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" name="submit" class="submit-btn">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="map-section">
                <h2>Find Us</h2>
                <div class="map-placeholder">
                    <p>Interactive Map Coming Soon</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>