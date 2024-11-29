<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Profile.php";
require_once "../includes/MatchMaker.php";
require_once "../includes/Notification.php";

$database = new Database();
$db = $database->getConnection();
$profile = new Profile($db);
$matchMaker = new MatchMaker($db);

// Get user's profile
$has_profile = $profile->getByUserId($_SESSION['user_id']);

// Include notification bar`
require_once "../includes/notification-bar.php";

// Add this function at the top to handle profile photo
function getProfilePhotoUrl($photoUrl) {
    if (empty($photoUrl)) {
        return "../assets/images/default-avatar.png";
    }
    
    $photoPath = "../uploads/photos/" . $photoUrl;
    if (file_exists($photoPath)) {
        return $photoPath;
    }
    
    return "../assets/images/default-avatar.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SoulMingle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="../assets/js/notifications.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/modern-style.css">
</head>
<body>
    <div class="dashboard-header">
        <div class="logo">
            <h1>SoulMingle</h1>
        </div>
        <div class="nav-actions">
            <?php include "../includes/notification-bar.php"; ?>
            <div class="nav-links">
                <a href="search.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Find Matches
                </a>
                <a href="matches.php" class="btn btn-outline">
                    <i class="fas fa-heart"></i> My Matches
                </a>
                <a href="logout.php" class="btn btn-outline">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <div class="glass-card profile-container animate-fade-in">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        
        <?php if($has_profile): ?>
            <div class="profile-grid">
                <div class="profile-photo-section">
                    <img src="<?php echo getProfilePhotoUrl($profile->photo_url); ?>" 
                         alt="Profile Photo" 
                         class="profile-photo rounded-full shadow-lg"
                         onerror="this.src='../assets/images/default-avatar.png'">
                    <div class="profile-actions mt-4">
                        <a href="edit-profile.php" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    </div>
                </div>
                <div class="profile-details">
                    <div class="stat-card">
                        <i class="fas fa-user"></i>
                        <div class="stat-number"><?php echo htmlspecialchars($profile->age); ?></div>
                        <div class="stat-label">Years Old</div>
                    </div>
                    <!-- Add more profile details in a modern layout -->
                </div>
            </div>
        <?php else: ?>
            <div class="no-profile-message glass-card">
                <i class="fas fa-user-plus fa-3x"></i>
                <h3>Complete Your Profile</h3>
                <p>Create your profile to start finding your perfect match!</p>
                <a href="create-profile.php" class="btn btn-primary">Get Started</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 