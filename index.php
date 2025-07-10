<?php
// Start the session to access user information
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['username']);
$username = $isLoggedIn ? $_SESSION['username'] : '';
$usertype = $isLoggedIn ? $_SESSION['usertype'] : '';

// Function to get dashboard URL based on user type
function getDashboardUrl($usertype) {
    switch ($usertype) {
        case 'Customer':
            return 'user.html';
        case 'Staff':
            return 'gym man.html';
        case 'Admin':
            return 'admin.php';
        default:
            return 'index.php';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitZone - Premium Fitness Center</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(0, 0, 0, 0.98);
            backdrop-filter: blur(20px);
            z-index: 1000;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #ff6b35;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #ff6b35;
            transition: width 0.3s ease;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            padding: 10px 20px;
            color: white;
            outline: none;
            transition: all 0.3s ease;
            width: 250px;
        }

        .search-box:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 20px rgba(255, 107, 53, 0.3);
        }

        .search-btn {
            background: #ff6b35;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .search-btn:hover {
            background: #e55a2b;
            transform: translateY(-2px);
        }

        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: black;
            border: 1px solid #333;
            border-radius: 10px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1001;
            display: none;
            margin-top: 5px;
        }

        .search-dropdown.show {
            display: block;
        }

        .search-item {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 1px solid #333;
            color: white;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s ease;
        }

        .search-item:hover {
            background-color: #333;
        }

        .search-item:last-child {
            border-bottom: none;
        }

        .no-results {
            padding: 12px 20px;
            color: #888;
            font-style: italic;
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            background: linear-gradient(135deg, rgba(10, 10, 10, 0.8), rgba(255, 107, 53, 0.1));
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="grad1" cx="50%" cy="50%" r="50%"><stop offset="0%" style="stop-color:%23d4853a;stop-opacity:0.3"/><stop offset="100%" style="stop-color:%2300ccff;stop-opacity:0.1"/></radialGradient></defs><circle cx="200" cy="300" r="150" fill="url(%23grad1)"/><circle cx="800" cy="700" r="200" fill="url(%23grad1)"/></svg>');
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .hero-content {
            text-align: center;
            z-index: 2;
            max-width: 900px;
            padding: 2rem;
        }

        .hero h1 {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: #ffffff;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { filter: brightness(1); }
            to { filter: brightness(1.2); }
        }

        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #ff6b35;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.4);
            background: #e55a2b;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
            background: rgba(255, 255, 255, 0.02);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 3rem;
            color: #ffffff;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #ff6b35;
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
        }

        .feature-icon {
            font-size: 3rem;
            color: #ff6b35;
            margin-bottom: 1rem;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .feature-description {
            opacity: 0.8;
            line-height: 1.6;
        }

        /* User Dashboard / Login Section */
        .user-section {
            position: fixed;
            top: 50%;
            right: 2rem;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            width: 320px;
            z-index: 100;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: white;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-input:focus {
            border-color: #ff6b35;
            box-shadow: 0 0 10px rgba(255, 107, 53, 0.3);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #ff6b35;
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
            background: #e55a2b;
        }

        .social-login {
            text-align: center;
            margin-top: 1.5rem;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-icon:hover {
            background: #ff6b35;
            transform: scale(1.1);
        }

        /* User Dashboard Styles */
        .user-dashboard {
            text-align: center;
        }

        .user-welcome {
            margin-bottom: 1rem;
        }

        .user-type-badge {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: inline-block;
        }

        .username-display {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #00ff88;
        }

        .dashboard-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(45deg, #d4853a, #00ccff);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }

        .dashboard-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 255, 136, 0.3);
        }

        .logout-btn {
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 0, 0, 0.9);
        }

        /* Promotions Section */
        .promotions {
            padding: 100px 0;
            background: linear-gradient(135deg, rgba(255, 107, 53, 0.1), rgba(0, 0, 0, 0.3));
        }

        .promotions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .promotion-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .promotion-card:hover {
            transform: translateY(-5px);
            border-color: #ff6b35;
            box-shadow: 0 20px 40px rgba(255, 107, 53, 0.2);
        }

        .promotion-discount {
            font-size: 3rem;
            font-weight: 900;
            color: #ff6b35;
        }

        .promotion-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin: 1rem 0;
        }

        .promotion-description {
            opacity: 0.8;
            margin-bottom: 1.5rem;
        }

        .promotion-btn {
            background: #ff6b35;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .promotion-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
            background: #e55a2b;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-menu {
                display: none;
            }
            
            .user-section {
                position: relative;
                right: auto;
                top: auto;
                transform: none;
                margin: 2rem auto;
                width: 90%;
                max-width: 320px;
            }
            
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .search-box {
                width: 150px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Utility Classes */
        .hidden {
            display: none;
        }

        .show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">FitZone</div>
            <ul class="nav-menu">
                <li><a href="index.php">HOME</a></li>
                <li><a href="my.php">CLASSES</a></li>
                <li><a href="trainers.php">TRAINERS</a></li>
                <li><a href="membership.html">MEMBERSHIP</a></li>
                <li><a href="blog.html">BLOG</a></li>
                <li><a href="contact.php">CONTACT</a></li>
            </ul>
            <div class="search-container">
                <input type="text" class="search-box" placeholder="Search..." id="searchInput">
                <button class="search-btn" id="searchBtn">Search</button>
                <div class="search-dropdown" id="searchDropdown"></div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Welcome to FitZone!</h1>
            <p>Your journey to fitness starts here. Join us for personalized training, diverse classes, and a supportive community that will help you achieve your goals.</p>
            <a href="membership.html" class="cta-button">
                <i class="fas fa-rocket"></i>
                Start Your Journey
            </a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose FitZone?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-dumbbell"></i>
                    </div>
                    <h3 class="feature-title">State-of-the-Art Equipment</h3>
                    <p class="feature-description">Train with the latest, professional-grade fitness equipment from world-renowned brands for optimal results.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h3 class="feature-title">Expert Trainers</h3>
                    <p class="feature-description">Work with certified professionals who will guide you every step of your fitness journey with personalized attention.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="feature-title">Diverse Classes</h3>
                    <p class="feature-description">From yoga to HIIT, find the perfect class that matches your fitness style and goals in our comprehensive program.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="feature-title">Supportive Community</h3>
                    <p class="feature-description">Join a motivating community of fitness enthusiasts who support each other's success and celebrate achievements together.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Promotions Section -->
    <section class="promotions" id="promotions">
        <div class="container">
            <h2 class="section-title">Current Promotions</h2>
            <div class="promotions-grid">
                <div class="promotion-card">
                    <div class="promotion-discount">20%</div>
                    <h3 class="promotion-title">New Member Special</h3>
                    <p class="promotion-description">Get 20% off your first month membership. Perfect for beginners ready to start their fitness journey!</p>
                    <button class="promotion-btn" onclick="window.location.href='membership.html'">Claim Offer</button>
                </div>
                <div class="promotion-card">
                    <div class="promotion-discount">$50</div>
                    <h3 class="promotion-title">Referral Bonus</h3>
                    <p class="promotion-description">Refer a friend and both of you receive $50 credit towards your membership or personal training sessions.</p>
                    <button class="promotion-btn" onclick="window.location.href='contact.php'">Refer Now</button>
                </div>
                <div class="promotion-card">
                    <div class="promotion-discount">7 Days</div>
                    <h3 class="promotion-title">Free Trial</h3>
                    <p class="promotion-description">Experience all our facilities and classes with a complimentary 7-day trial membership.</p>
                    <button class="promotion-btn" onclick="window.location.href='membership.html'">Start Trial</button>
                </div>
            </div>
        </div>
    </section>

    <!-- User Section (Login/Dashboard) -->
    <div class="user-section">
        <?php if (!$isLoggedIn): ?>
        <!-- Login Form -->
        <div class="login-form" id="loginForm">
            <h2>Member Login</h2>
            <form action="login_process.php" method="POST">
                <div class="form-group">
                    <input type="text" name="username" class="form-input" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <div class="social-login">
                <p style="margin-bottom: 1rem; opacity: 0.7;">Or login with</p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-google"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- User Dashboard -->
        <div class="user-dashboard" id="userDashboard">
            <div class="user-welcome">
                <h3>Welcome Back!</h3>
                <div class="user-type-badge"><?php echo htmlspecialchars($usertype); ?></div>
                <div class="username-display">
                    <i class="fas fa-user"></i>
                    <span><?php echo htmlspecialchars($username); ?></span>
                </div>
            </div>
            <a href="<?php echo getDashboardUrl($usertype); ?>" class="dashboard-btn">
                <i class="fas fa-tachometer-alt"></i>
                Go to Dashboard
            </a>
            <br>
            <a href="?logout=true" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Navigation links data
        const navLinks = [
            { name: 'HOME', url: 'index.php' },
            { name: 'CLASSES', url: 'my.php' },
            { name: 'TRAINERS', url: 'trainers.php' },
            { name: 'MEMBERSHIP', url: 'membership.html' },
            { name: 'BLOG', url: 'blog.html' },
            { name: 'CONTACT', url: 'contact.php' }
        ];

        const searchInput = document.getElementById('searchInput');
        const searchBtn = document.getElementById('searchBtn');
        const searchDropdown = document.getElementById('searchDropdown');

        // Search functionality
        function performSearch() {
            const query = searchInput.value.toLowerCase().trim();
            
            if (query === '') {
                searchDropdown.classList.remove('show');
                return;
            }

            // Filter navigation links based on search query
            const filteredLinks = navLinks.filter(link => 
                link.name.toLowerCase().includes(query)
            );

            // Clear previous results
            searchDropdown.innerHTML = '';

            if (filteredLinks.length > 0) {
                filteredLinks.forEach(link => {
                    const item = document.createElement('a');
                    item.href = link.url;
                    item.className = 'search-item';
                    item.textContent = link.name;
                    searchDropdown.appendChild(item);
                });
                searchDropdown.classList.add('show');
            } else {
                const noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.textContent = 'No results found';
                searchDropdown.appendChild(noResults);
                searchDropdown.classList.add('show');
            }
        }

        // Event listeners for search
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch();
        });

        searchInput.addEventListener('input', function() {
            performSearch();
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchDropdown.classList.remove('show');
            }
        });

        // Handle search item clicks
        searchDropdown.addEventListener('click', function(e) {
            if (e.target.classList.contains('search-item')) {
                searchDropdown.classList.remove('show');
                searchInput.value = '';
            }
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(0, 0, 0, 0.99)';
            } else {
                navbar.style.background = 'rgba(0, 0, 0, 0.98)';
            }
        });
    </script>
</body>
</html>