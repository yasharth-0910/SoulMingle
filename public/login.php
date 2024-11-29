<?php
session_start();

// If user is already logged in, redirect to dashboard
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
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
    
    if($user->login()) {
        // Create session
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SoulMingle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="auth-page">
        <div class="glass-card auth-container animate-fade-in">
            <div class="auth-header">
                <h2><i class="fas fa-heart"></i> SoulMingle</h2>
                <p>Welcome back! Please login to your account</p>
            </div>
            
            <?php if(!empty($message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="modern-form">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" required class="form-control" placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required class="form-control" placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="forgot-password.php">Forgot Password?</a></p>
            </div>
        </div>
    </div>
</body>
</html> 