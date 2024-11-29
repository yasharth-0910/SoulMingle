<?php
session_start();

// If user is already logged in, redirect appropriately
if(isset($_SESSION['user_id'])) {
    if(isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        header("Location: admin/index.php");
    } else {
        header("Location: public/dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to SoulMingle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .login-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
        }

        .login-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .login-card h2 {
            margin-bottom: 1rem;
            color: white;
        }

        .login-card p {
            margin-bottom: 2rem;
            color: rgba(255, 255, 255, 0.8);
        }

        .btn-white {
            background: white;
            color: #764ba2;
            padding: 0.75rem 2rem;
            border-radius: 9999px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-white:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .logo {
            margin-bottom: 3rem;
        }

        .logo h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: white;
        }

        .logo p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
        }

        @media (max-width: 768px) {
            .login-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-heart"></i> SoulMingle</h1>
                <p>Find your perfect match</p>
            </div>

            <div class="login-options">
                <div class="login-card animate-fade-in">
                    <i class="fas fa-user"></i>
                    <h2>User Login</h2>
                    <p>Find your soulmate and start your journey to lasting love.</p>
                    <a href="public/login.php" class="btn-white">Login as User</a>
                    <p style="margin-top: 1rem;">
                        <a href="public/register.php" style="color: white;">New user? Register here</a>
                    </p>
                </div>

                <div class="login-card animate-fade-in">
                    <i class="fas fa-user-shield"></i>
                    <h2>Admin Login</h2>
                    <p>Manage users, moderate profiles, and maintain the platform.</p>
                    <a href="admin/login.php" class="btn-white">Login as Admin</a>
                    <p style="margin-top: 1rem;">
                        <small style="color: rgba(255, 255, 255, 0.8);">Authorized personnel only</small>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add smooth scroll for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 