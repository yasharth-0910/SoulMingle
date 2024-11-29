<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/MatchMaker.php";
require_once "../includes/Notification.php";

$database = new Database();
$db = $database->getConnection();
$matchMaker = new MatchMaker($db);

// Handle interest expression
if(isset($_POST['express_interest'])) {
    $match_user_id = (int)$_POST['match_user_id'];
    $result = $matchMaker->expressInterest($_SESSION['user_id'], $match_user_id);
    if($result === 'Connected') {
        $success_message = "It's a match! You are now connected.";
    } elseif($result === 'Interested') {
        $success_message = "Interest expressed successfully!";
    } else {
        $error_message = "Unable to express interest.";
    }
}

// Get user's matches
$connected_matches = $matchMaker->getMatches($_SESSION['user_id'], 'Connected');
$pending_matches = $matchMaker->getMatches($_SESSION['user_id'], 'Interested');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Matches - SoulMingle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-inter">
    <header class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <?php include "../includes/notification-bar.php"; ?>
            <nav class="space-x-4">
                <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                <a href="search.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-search mr-2"></i> Find More Matches
                </a>
            </nav>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
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

        <!-- Connected Matches Section -->
        <section class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold mb-4"><i class="fas fa-heart text-red-500 mr-2"></i> Connected Matches</h2>
            <?php if(empty($connected_matches)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-heart-broken text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500">No connections yet. Start exploring profiles!</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($connected_matches as $match): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:scale-105">
                            <img src="../uploads/photos/<?php echo htmlspecialchars($match['photo_url']); ?>" 
                                 alt="Profile Photo" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($match['username']); ?></h3>
                                <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($match['age']); ?> years old</p>
                                <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($match['religion']); ?></p>
                                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($match['education']); ?></p>
                                <a href="view-profile.php?id=<?php echo $match['user_id']; ?>" 
                                   class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 ease-in-out">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Pending Interests Section -->
        <section class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-4"><i class="fas fa-clock text-yellow-500 mr-2"></i> Pending Interests</h2>
            <?php if(empty($pending_matches)): ?>
                <div class="text-center py-8">
                    <i class="fas fa-hourglass-half text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500">No pending interests at the moment.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($pending_matches as $match): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:scale-105">
                            <img src="../uploads/photos/<?php echo htmlspecialchars($match['photo_url']); ?>" 
                                 alt="Profile Photo" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($match['username']); ?></h3>
                                <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($match['age']); ?> years old</p>
                                <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($match['religion']); ?></p>
                                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($match['education']); ?></p>
                                <a href="view-profile.php?id=<?php echo $match['user_id']; ?>" 
                                   class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 ease-in-out">View Profile</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>n