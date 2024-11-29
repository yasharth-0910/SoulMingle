<?php
session_start();

// Check if user is logged in and is admin
if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Admin.php";
require_once "../includes/MatchMaker.php";
require_once "../includes/Notification.php";

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);
$matchMaker = new MatchMaker($db);

// Handle export requests
if(isset($_GET['export'])) {
    $type = $_GET['type'] ?? 'users';
    $format = $_GET['format'] ?? 'csv';
    
    switch($format) {
        case 'pdf':
            require_once 'export-pdf.php';
            exit();
        case 'excel':
            require_once 'export-excel.php';
            exit();
        default:
            require_once 'export-stats.php';
            exit();
    }
}

// Get statistics
$total_users = $admin->getTotalUsers();
$total_matches = $admin->getTotalMatches();
$stats = $admin->getMatchStatistics();
$daily_stats = $admin->getDailyStats();
$monthly_stats = $admin->getMonthlyStats();

// Get gender distribution
$gender_stats = $admin->getGenderDistribution();

// Get age distribution
$age_stats = $admin->getAgeDistribution();

// Get religion distribution
$religion_stats = $admin->getReligionDistribution();

// Get success rate
$success_rate = $admin->getMatchSuccessRate();

// Get recent users
$recent_users = $admin->getRecentUsers(5);

// Get recent matches
$recent_matches = $admin->getRecentMatches(5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SoulMingle</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-md">
            <div class="p-4 border-b">
                <h2 class="text-2xl font-bold text-purple-600"><i class="fas fa-heart"></i> SoulMingle</h2>
                <p class="text-sm text-gray-600">Admin Panel</p>
            </div>
            
            <nav class="mt-4">
                <a href="#dashboard" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
                <a href="users.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-users mr-2"></i> Users
                </a>
                <a href="profiles.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-user-circle mr-2"></i> Profiles
                </a>
                <a href="matches.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-heart mr-2"></i> Matches
                </a>
                <a href="reports.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-flag mr-2"></i> Reports
                </a>
                <a href="settings.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>
            </nav>

            <div class="absolute bottom-0 w-64 p-4 border-t">
                <a href="../public/logout.php" class="block w-full py-2 px-4 bg-red-500 text-white text-center rounded hover:bg-red-600 transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
                    <div class="text-sm text-gray-600">
                        Welcome, <span class="font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                </div>

                <!-- Quick Actions Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="dropdown relative">
                            <button class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                <i class="fas fa-download mr-2"></i> Export Data
                            </button>
                            <div class="dropdown-content hidden absolute w-full bg-white shadow-lg rounded mt-1">
                                <a href="?export=users&format=pdf" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i> Export Users (PDF)
                                </a>
                                <a href="?export=users&format=excel" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-file-excel text-green-500 mr-2"></i> Export Users (Excel)
                                </a>
                                <a href="?export=matches&format=pdf" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i> Export Matches (PDF)
                                </a>
                                <a href="?export=matches&format=excel" class="block px-4 py-2 hover:bg-gray-100">
                                    <i class="fas fa-file-excel text-green-500 mr-2"></i> Export Matches (Excel)
                                </a>
                            </div>
                        </div>
                        <!-- Add more quick actions as needed -->
                    </div>
                </div>

                <!-- Statistics Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                        <div class="rounded-full bg-blue-100 p-3 mr-4">
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-800"><?php echo $total_users; ?></div>
                            <div class="text-sm text-gray-600">Total Users</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                        <div class="rounded-full bg-green-100 p-3 mr-4">
                            <i class="fas fa-heart text-green-500 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-800"><?php echo $stats['connected']; ?></div>
                            <div class="text-sm text-gray-600">Successful Matches</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                        <div class="rounded-full bg-yellow-100 p-3 mr-4">
                            <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-800"><?php echo $stats['interested']; ?></div>
                            <div class="text-sm text-gray-600">Pending Matches</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6 flex items-center">
                        <div class="rounded-full bg-purple-100 p-3 mr-4">
                            <i class="fas fa-user-plus text-purple-500 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-800"><?php echo $admin->getTodaySignups(); ?></div>
                            <div class="text-sm text-gray-600">Today's Signups</div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4">User Growth</h3>
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4">Match Statistics</h3>
                        <canvas id="matchStatsChart"></canvas>
                    </div>
                </div>

                <!-- Demographics Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4">Gender Distribution</h3>
                        <canvas id="genderChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4">Age Distribution</h3>
                        <canvas id="ageChart"></canvas>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-xl font-semibold mb-4">Religion Distribution</h3>
                        <canvas id="religionChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Recent Users -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="p-4 border-b">
                            <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-user-clock mr-2 text-blue-500"></i>Recent Users</h3>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-gray-600 text-sm">
                                        <th class="pb-3 font-semibold">Username</th>
                                        <th class="pb-3 font-semibold">Email</th>
                                        <th class="pb-3 font-semibold">Joined</th>
                                        <th class="pb-3 font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_users as $user): ?>
                                        <tr class="border-t">
                                            <td class="py-3 text-sm"><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td class="py-3 text-sm"><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td class="py-3 text-sm"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                            <td class="py-3 text-sm">
                                                <a href="view-user.php?id=<?php echo $user['id']; ?>" 
                                                   class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition-colors duration-200">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Matches -->
                    <div class="bg-white rounded-lg shadow-md">
                        <div class="p-4 border-b">
                            <h3 class="text-xl font-semibold text-gray-800"><i class="fas fa-heart mr-2 text-red-500"></i>Recent Matches</h3>
                        </div>
                        <div class="p-4 overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-gray-600 text-sm">
                                        <th class="pb-3 font-semibold">User 1</th>
                                        <th class="pb-3 font-semibold">User 2</th>
                                        <th class="pb-3 font-semibold">Status</th>
                                        <th class="pb-3 font-semibold">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_matches as $match): ?>
                                        <tr class="border-t">
                                            <td class="py-3 text-sm"><?php echo htmlspecialchars($match['user1_name']); ?></td>
                                            <td class="py-3 text-sm"><?php echo htmlspecialchars($match['user2_name']); ?></td>
                                            <td class="py-3 text-sm">
                                                <span class="px-2 py-1 text-xs rounded-full <?php echo strtolower($match['status']) === 'connected' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                    <?php echo $match['status']; ?>
                                                </span>
                                            </td>
                                            <td class="py-3 text-sm"><?php echo date('M j, Y', strtotime($match['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize dropdown functionality
        document.querySelectorAll('.dropdown').forEach(dropdown => {
            dropdown.addEventListener('click', function() {
                this.querySelector('.dropdown-content').classList.toggle('hidden');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dropdown')) {
                document.querySelectorAll('.dropdown-content').forEach(content => {
                    content.classList.add('hidden');
                });
            }
        });

        // Initialize charts with PHP data
        const chartData = {
            userGrowth: <?php echo json_encode($monthly_stats); ?>,
            matchStats: <?php echo json_encode($stats); ?>,
            genderDistribution: <?php echo json_encode($gender_stats); ?>,
            ageDistribution: <?php echo json_encode($age_stats); ?>,
            religionDistribution: <?php echo json_encode($religion_stats); ?>
        };

        // Initialize all charts
        document.addEventListener('DOMContentLoaded', function() {
            initializeUserActivityCharts();
            initializeMatchSuccessCharts();
            initializeDemographicsCharts();
        });
    </script>
    <script src="../assets/js/admin-charts.js"></script>
</body>
</html>