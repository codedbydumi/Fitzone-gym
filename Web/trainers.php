<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "nalifitzone"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch trainers data
$sql = "SELECT * FROM trainers ORDER BY experience_years DESC, created_at DESC";
$result = $conn->query($sql);

// Sample trainer images (you can replace these with actual photos)
$sample_images = [
    'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'https://images.unsplash.com/photo-1594381898411-846e7d193883?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'https://images.unsplash.com/photo-1518611012118-696072aa579a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'https://images.unsplash.com/photo-1544717297-fa95b6ee9643?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'https://images.unsplash.com/photo-1540569014015-19a7be504e3a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80',
    'https://images.unsplash.com/photo-1506629905962-b0e4c34f0fdc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80'
];

function formatPhoneNumber($phone) {
    return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', preg_replace('/\D/', '', $phone));
}

function getRandomImage($sample_images) {
    return $sample_images[array_rand($sample_images)];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elite Fitness Trainers - FitZone</title>
    <link rel="stylesheet" href="css/traniers.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0a0a;
            color: #ffffff;
            line-height: 1.6;
        }

        .main {
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
        }

        .navbar {
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #333;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-size: 2rem;
            font-weight: 900;
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .menu ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .menu a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .menu a:hover {
            color: #ff6b35;
        }

        .search {
            display: flex;
            gap: 0.5rem;
        }

        .srch {
            padding: 0.5rem 1rem;
            border: 1px solid #333;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            outline: none;
        }

        .srch::placeholder {
            color: #999;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .hero {
            text-align: center;
            padding: 5rem 2rem;
            background: radial-gradient(circle at 50% 50%, rgba(255, 107, 53, 0.1) 0%, transparent 50%);
        }

        .hero h1 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, #ffffff, #ff6b35);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.2rem;
            color: #cccccc;
            margin-bottom: 2rem;
        }

        .cta-button {
            display: inline-block;
            padding: 1rem 2rem;
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 107, 53, 0.4);
        }

        .trainers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .trainer-card {
            background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
            border-radius: 20px;
            padding: 0;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            border: 1px solid #333;
            position: relative;
        }

        .trainer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(255, 107, 53, 0.2);
            border-color: #ff6b35;
        }

        .trainer-image-container {
            position: relative;
            height: 300px;
            overflow: hidden;
        }

        .trainer-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .trainer-card:hover .trainer-image {
            transform: scale(1.05);
        }

        .trainer-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
        }

        .trainer-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .trainer-info {
            padding: 2rem;
        }

        .trainer-name {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .trainer-specialization {
            color: #ff6b35;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .trainer-stats {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-item {
            text-align: center;
            padding: 0.5rem;
            background: rgba(255, 107, 53, 0.1);
            border-radius: 10px;
            flex: 1;
            border: 1px solid rgba(255, 107, 53, 0.3);
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ff6b35;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #cccccc;
        }

        .trainer-bio {
            color: #cccccc;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid #333;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #cccccc;
        }

        .contact-item i {
            color: #ff6b35;
            width: 20px;
        }

        .book-session-btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .book-session-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 107, 53, 0.3);
        }

        .no-trainers {
            text-align: center;
            padding: 5rem 2rem;
            color: #cccccc;
        }

        .no-trainers h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #ffffff;
        }

        .filter-section {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .filter-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #333;
            border-radius: 25px;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: linear-gradient(45deg, #ff6b35, #ff8e53);
            border-color: #ff6b35;
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .trainers-grid {
                grid-template-columns: 1fr;
                padding: 1rem;
            }
            
            .trainer-card {
                margin-bottom: 2rem;
            }
            
            .menu {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="main">
        <nav class="navbar">
            <div class="navbar-content">
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
          
            </div>
        </nav>
        
        <section class="hero">
            <h1>ELITE FITNESS TRAINERS</h1>
            <p>World-class certified professionals dedicated to transforming your fitness journey</p>
            <a href="#membership" class="cta-button">Start Your Journey</a>
        </section>

        <section class="filter-section">
            <div class="filter-buttons">
                <button class="filter-btn active" onclick="filterTrainers('all')">All Trainers</button>
                <button class="filter-btn" onclick="filterTrainers('yoga')">Yoga & Flexibility</button>
                <button class="filter-btn" onclick="filterTrainers('strength')">Strength Training</button>
                <button class="filter-btn" onclick="filterTrainers('cardio')">Cardio & Dance</button>
                <button class="filter-btn" onclick="filterTrainers('nutrition')">Nutrition</button>
            </div>
        </section>
        
        <section id="trainers" class="content">
            <div class="trainers-grid" id="trainersGrid">
                <?php
                if ($result->num_rows > 0) {
                    $imageIndex = 0;
                    while($row = $result->fetch_assoc()) {
                        $profileImage = !empty($row["profile_image"]) ? $row["profile_image"] : $sample_images[$imageIndex % count($sample_images)];
                        $imageIndex++;
                        
                        echo '<div class="trainer-card" data-name="' . strtolower($row["name"]) . '" data-specialization="' . strtolower($row["specialization"]) . '">';
                        
                        // Image container with overlay
                        echo '<div class="trainer-image-container">';
                        echo '<img src="' . htmlspecialchars($profileImage) . '" alt="' . htmlspecialchars($row["name"]) . '" class="trainer-image">';
                        echo '<div class="trainer-overlay"></div>';
                        
                        // Experience badge
                        if (!empty($row["experience_years"])) {
                            echo '<div class="trainer-badge">' . htmlspecialchars($row["experience_years"]) . '+ Years</div>';
                        }
                        echo '</div>';
                        
                        // Trainer info
                        echo '<div class="trainer-info">';
                        echo '<h2 class="trainer-name">' . htmlspecialchars($row["name"]) . '</h2>';
                        echo '<p class="trainer-specialization">' . htmlspecialchars($row["specialization"]) . '</p>';
                        
                        // Stats section
                        echo '<div class="trainer-stats">';
                        if (!empty($row["experience_years"])) {
                            echo '<div class="stat-item">';
                            echo '<div class="stat-number">' . htmlspecialchars($row["experience_years"]) . '</div>';
                            echo '<div class="stat-label">Years Exp</div>';
                            echo '</div>';
                        }
                        
                        // Random client count for demo
                        $clientCount = rand(50, 500);
                        echo '<div class="stat-item">';
                        echo '<div class="stat-number">' . $clientCount . '+</div>';
                        echo '<div class="stat-label">Happy Clients</div>';
                        echo '</div>';
                        
                        // Random rating for demo
                        $rating = number_format(rand(45, 50) / 10, 1);
                        echo '<div class="stat-item">';
                        echo '<div class="stat-number">' . $rating . '</div>';
                        echo '<div class="stat-label">⭐ Rating</div>';
                        echo '</div>';
                        echo '</div>';
                        
                        // Bio
                        if (!empty($row["bio"])) {
                            echo '<p class="trainer-bio">' . htmlspecialchars($row["bio"]) . '</p>';
                        } else {
                            echo '<p class="trainer-bio">Dedicated fitness professional committed to helping you achieve your health and wellness goals through personalized training programs and expert guidance.</p>';
                        }
                        
                        // Contact information
                        echo '<div class="contact-info">';
                        if (!empty($row["email"])) {
                            echo '<div class="contact-item">';
                            echo '<i class="fas fa-envelope"></i>';
                            echo '<span>' . htmlspecialchars($row["email"]) . '</span>';
                            echo '</div>';
                        }
                        if (!empty($row["phone"])) {
                            echo '<div class="contact-item">';
                            echo '<i class="fas fa-phone"></i>';
                            echo '<span>' . formatPhoneNumber($row["phone"]) . '</span>';
                            echo '</div>';
                        }
                        echo '</div>';
                        
                        // Book session button
                        echo '<button class="book-session-btn" onclick="bookSession(\'' . htmlspecialchars($row["name"]) . '\')">';
                        echo '<i class="fas fa-calendar-plus"></i> Book Training Session';
                        echo '</button>';
                        
                        echo '</div>'; // trainer-info
                        echo '</div>'; // trainer-card
                    }
                } else {
                    echo '<div class="no-trainers">';
                    echo '<h3>No trainers found</h3>';
                    echo '<p>We are currently updating our trainer profiles. Please check back soon!</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </section>
    </div>

    <script>
        function searchTrainers() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const trainerCards = document.querySelectorAll('.trainer-card');
            
            trainerCards.forEach(card => {
                const name = card.getAttribute('data-name');
                const specialization = card.getAttribute('data-specialization');
                
                if (name.includes(searchInput) || specialization.includes(searchInput)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function filterTrainers(category) {
            const trainerCards = document.querySelectorAll('.trainer-card');
            const filterBtns = document.querySelectorAll('.filter-btn');
            
            // Update active button
            filterBtns.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            trainerCards.forEach(card => {
                const specialization = card.getAttribute('data-specialization');
                
                if (category === 'all') {
                    card.style.display = 'block';
                } else if (category === 'yoga' && specialization.includes('yoga')) {
                    card.style.display = 'block';
                } else if (category === 'strength' && (specialization.includes('strength') || specialization.includes('crossfit') || specialization.includes('powerlifting'))) {
                    card.style.display = 'block';
                } else if (category === 'cardio' && (specialization.includes('dance') || specialization.includes('cardio'))) {
                    card.style.display = 'block';
                } else if (category === 'nutrition' && specialization.includes('nutrition')) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function bookSession(trainerName) {
            alert(`Booking session with ${trainerName}. This would redirect to booking form in a real application.`);
        }
        
        // Real-time search
        document.getElementById('searchInput').addEventListener('input', searchTrainers);

        // Add smooth scroll and animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.trainer-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });
            
            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>