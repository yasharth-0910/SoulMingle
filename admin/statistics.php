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

// Get various statistics
$user_stats = $admin->getUserStats();
$match_stats = $admin->getMatchStatistics();
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics & Analytics - SoulMingle Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include "includes/sidebar.php"; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">Statistics & Analytics</h1>

                <!-- Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Total Users</p>
                                <p class="text-2xl font-semibold text-gray-800"><?php echo $user_stats['total_users']; ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-green-500 text-sm">
                                <i class="fas fa-arrow-up"></i> 
                                <?php echo $daily_stats['user_growth']; ?>% growth
                            </span>
                            <span class="text-sm text-gray-500 ml-2">from last month</span>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500">
                                <i class="fas fa-heart text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Successful Matches</p>
                                <p class="text-2xl font-semibold text-gray-800"><?php echo $match_stats['connected']; ?></p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-green-500 text-sm">
                                <?php echo $success_rate; ?>% success rate
                            </span>
                        </div>
                    </div>

                    <!-- Add more overview cards -->
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- User Growth Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">User Growth</h2>
                        <canvas id="userGrowthChart"></canvas>
                    </div>

                    <!-- Match Statistics Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">Match Statistics</h2>
                        <canvas id="matchStatsChart"></canvas>
                    </div>
                </div>

                <!-- Demographics Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Gender Distribution -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">Gender Distribution</h2>
                        <canvas id="genderChart"></canvas>
                    </div>

                    <!-- Age Distribution -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">Age Distribution</h2>
                        <canvas id="ageChart"></canvas>
                    </div>

                    <!-- Religion Distribution -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold mb-4">Religion Distribution</h2>
                        <canvas id="religionChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize charts with the PHP data
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        const matchStatsCtx = document.getElementById('matchStatsChart').getContext('2d');
        const genderCtx = document.getElementById('genderChart').getContext('2d');
        const ageCtx = document.getElementById('ageChart').getContext('2d');
        const religionCtx = document.getElementById('religionChart').getContext('2d');

        // User Growth Chart
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($monthly_stats)); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_values($monthly_stats)); ?>,
                    borderColor: '#6366f1',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Add more chart initializations for other statistics
    </script>
</body>
</html> 