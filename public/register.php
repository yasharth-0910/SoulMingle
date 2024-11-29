<?php
session_start();

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
    // Validate input
    if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password'])) {
        $message = "All fields are required.";
    } elseif(strlen($_POST['password']) < 6) {
        $message = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $user->email = $_POST['email'];
        if($user->emailExists()) {
            $message = "Email already exists.";
        } else {
            // Create new user
            $user->username = $_POST['username'];
            $user->password = $_POST['password'];
            
            if($user->create()) {
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                
                header("Location: create-profile.php");
                exit();
            } else {
                $message = "Unable to create account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SoulMingle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/modern-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="auth-page">
        <div class="glass-card auth-container animate-fade-in">
            <div class="auth-header">
                <h2><i class="fas fa-heart"></i> SoulMingle</h2>
                <p>Create your account and find your soulmate</p>
            </div>
            
            <?php if(!empty($message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="modern-form">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" required class="form-control" placeholder="Choose a username">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" required class="form-control" placeholder="Enter your email">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" required class="form-control" placeholder="Create a password">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html> 