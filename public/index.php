<?php
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SoulMingle - Find Your Perfect Match</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header class="landing-header">
        <nav>
            <div class="logo">
                <h1>SoulMingle</h1>
            </div>
            <div class="nav-links">
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            </div>
        </nav>
    </header>

    <main class="landing-main">
        <section class="hero">
            <div class="hero-content">
                <h1>Find Your Perfect Match</h1>
                <p>Join thousands of people who have found their soulmate through SoulMingle</p>
                <div class="cta-buttons">
                    <a href="register.php" class="btn btn-primary btn-large">Get Started</a>
                    <a href="#how-it-works" class="btn btn-outline btn-large">Learn More</a>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="features">
            <h2>How It Works</h2>
            <div class="feature-grid">
                <div class="feature-card">
                    <i class="fas fa-user-plus"></i>
                    <h3>Create Profile</h3>
                    <p>Sign up and create your detailed profile to start your journey</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-search"></i>
                    <h3>Find Matches</h3>
                    <p>Search and filter profiles based on your preferences</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-heart"></i>
                    <h3>Connect</h3>
                    <p>Express interest and connect with your potential matches</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-comments"></i>
                    <h3>Interact</h3>
                    <p>Start conversations and get to know each other better</p>
                </div>
            </div>
        </section>

        <section class="success-stories">
            <h2>Success Stories</h2>
            <div class="story-grid">
                <div class="story-card">
                    <img src="../assets/images/couple1.jpg" alt="Happy Couple">
                    <h3>Sarah & John</h3>
                    <p>"We found each other on SoulMingle and got married last year. Thank you for bringing us together!"</p>
                </div>
                <div class="story-card">
                    <img src="../assets/images/couple2.jpg" alt="Happy Couple">
                    <h3>Mike & Lisa</h3>
                    <p>"SoulMingle's matching system really works! We're so happy we gave it a try."</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="landing-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About SoulMingle</h3>
                <p>We're dedicated to helping people find meaningful relationships and lasting love.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="privacy.php">Privacy Policy</a></li>
                    <li><a href="terms.php">Terms of Service</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Connect With Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 SoulMingle. All rights reserved.</p>
        </div>
    </footer>
</body>
</html> 