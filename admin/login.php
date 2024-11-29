<?php
session_start();

// If admin is already logged in, redirect to admin dashboard
if(isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$message = "";

if($_POST) {
    $user->email = $_POST['email'];
    $user->password = $_POST['password'];
    
    if($user->login() && $user->is_admin) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['is_admin'] = true;
        
        header("Location: index.php");
        exit();
    } else {
        $message = "Invalid credentials or insufficient privileges.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - SoulMingle</title>
    <!-- Add Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Custom Tailwind configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#4f46e5',
                        accent: '#ec4899',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/modern-style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Add custom styles for Tailwind -->
    <style type="text/tailwindcss">
        @layer utilities {
            .auth-card {
                @apply bg-white bg-opacity-95 backdrop-blur-lg rounded-2xl shadow-xl p-8 w-full max-w-md;
            }
            .form-input {
                @apply w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent;
            }
            .btn-primary {
                @apply w-full bg-primary text-white py-2 rounded-lg hover:bg-secondary transition-colors duration-200;
            }
        }
    </style>
</head>
<body class="admin-login-page">
    <div class="auth-container">
        <div class="glass-card animate-fade-in">
            <div class="auth-header">
                <i class="fas fa-user-shield fa-3x"></i>
                <h2>Admin Login</h2>
                <p>Access the SoulMingle admin panel</p>
            </div>

            <?php if(!empty($message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="modern-form">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" required class="form-control" placeholder="Admin email">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required class="form-control" placeholder="Admin password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
                </button>
            </form>

            <div class="auth-footer">
                <a href="../index.php" class="btn btn-outline btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Main Site
                </a>
            </div>
        </div>
    </div>
</body>
</html> 