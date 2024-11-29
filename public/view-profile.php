<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Profile.php";
require_once "../includes/MatchMaker.php";

$database = new Database();
$db = $database->getConnection();
$profile = new Profile($db);
$matchMaker = new MatchMaker($db);

$profile_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if(!$profile->getByUserId($profile_id)) {
    header("Location: search.php");
    exit();
}

// Handle interest expression
if(isset($_POST['express_interest'])) {
    $result = $matchMaker->expressInterest($_SESSION['user_id'], $profile_id);
    if($result === 'Connected') {
        $success_message = "It's a match! You are now connected.";
    } elseif($result === 'Interested') {
        $success_message = "Interest expressed successfully!";
    } else {
        $error_message = "Unable to express interest.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile - SoulMingle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100 font-inter">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <?php if(isset($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p><?php echo $success_message; ?></p>
                </div>
            <?php endif; ?>

            <?php if(isset($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p><?php echo $error_message; ?></p>
                </div>
            <?php endif; ?>

            <div class="md:flex">
                <div class="md:flex-shrink-0">
                    <img class="h-48 w-full object-cover md:w-48" src="../uploads/photos/<?php echo htmlspecialchars($profile->photo_url); ?>" alt="Profile Photo">
                </div>
                <div class="p-8">
                    <div class="uppercase tracking-wide text-sm text-indigo-500 font-semibold">Profile</div>
                    <h2 class="mt-1 text-3xl font-bold leading-tight text-gray-900"><?php echo htmlspecialchars($profile->username); ?></h2>
                    
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="profile-field">
                            <label class="block text-sm font-medium text-gray-700">Age:</label>
                            <span class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($profile->age); ?></span>
                        </div>
                        
                        <div class="profile-field">
                            <label class="block text-sm font-medium text-gray-700">Gender:</label>
                            <span class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($profile->gender); ?></span>
                        </div>
                        
                        <div class="profile-field">
                            <label class="block text-sm font-medium text-gray-700">Religion:</label>
                            <span class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($profile->religion); ?></span>
                        </div>
                        
                        <div class="profile-field">
                            <label class="block text-sm font-medium text-gray-700">Education:</label>
                            <span class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($profile->education); ?></span>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700">Interests:</label>
                        <p class="mt-1 text-lg text-gray-900"><?php echo nl2br(htmlspecialchars($profile->interests)); ?></p>
                    </div>

                    <form method="post" class="mt-8">
                        <input type="hidden" name="match_user_id" value="<?php echo $profile_id; ?>">
                        <button type="submit" name="express_interest" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                            <i class="fas fa-heart mr-2"></i> Express Interest
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>