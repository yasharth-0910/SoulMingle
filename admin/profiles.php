<?php
session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Admin.php";

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Handle profile actions
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'approve_profile':
            if($admin->approveProfile($_POST['profile_id'])) {
                $success_message = "Profile approved successfully";
            } else {
                $error_message = "Failed to approve profile";
            }
            break;
        case 'reject_profile':
            if($admin->rejectProfile($_POST['profile_id'])) {
                $success_message = "Profile rejected successfully";
            } else {
                $error_message = "Failed to reject profile";
            }
            break;
    }
}

// Get profiles for moderation
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$profiles = $admin->getProfilesForModeration($page, $limit);
$total_profiles = $admin->getTotalPendingProfiles();
$total_pages = ceil($total_profiles / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Moderation - SoulMingle Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include "includes/sidebar.php"; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Profile Moderation</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">
                            Pending Profiles: <?php echo $total_profiles; ?>
                        </span>
                    </div>
                </div>

                <?php if(isset($success_message)): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($error_message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($profiles as $profile): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <img src="../uploads/photos/<?php echo htmlspecialchars($profile['photo_url']); ?>" 
                                 alt="Profile Photo" class="w-full h-48 object-cover">
                            
                            <div class="p-4">
                                <h3 class="text-xl font-semibold mb-2">
                                    <?php echo htmlspecialchars($profile['username']); ?>
                                </h3>
                                
                                <div class="space-y-2 mb-4">
                                    <p class="text-gray-600">
                                        <span class="font-medium">Age:</span> 
                                        <?php echo htmlspecialchars($profile['age']); ?>
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Gender:</span> 
                                        <?php echo htmlspecialchars($profile['gender']); ?>
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Religion:</span> 
                                        <?php echo htmlspecialchars($profile['religion']); ?>
                                    </p>
                                    <p class="text-gray-600">
                                        <span class="font-medium">Education:</span> 
                                        <?php echo htmlspecialchars($profile['education']); ?>
                                    </p>
                                </div>

                                <div class="flex space-x-2">
                                    <form method="post" class="flex-1">
                                        <input type="hidden" name="profile_id" value="<?php echo $profile['id']; ?>">
                                        <button type="submit" name="action" value="approve_profile"
                                                class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                                            <i class="fas fa-check mr-2"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <form method="post" class="flex-1">
                                        <input type="hidden" name="profile_id" value="<?php echo $profile['id']; ?>">
                                        <button type="submit" name="action" value="reject_profile"
                                                class="w-full bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors">
                                            <i class="fas fa-times mr-2"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if($total_pages > 1): ?>
                    <div class="flex justify-center mt-6">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium 
                                          <?php echo $page === $i ? 'text-indigo-600 border-indigo-500 z-10' : 'text-gray-500 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html> 