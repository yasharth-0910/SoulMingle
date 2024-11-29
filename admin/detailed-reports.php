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

// Get report type from query string
$report_type = isset($_GET['type']) ? $_GET['type'] : 'user_activity';
$date_range = isset($_GET['range']) ? $_GET['range'] : 'last_30_days';

// Get report data based on type and range
switch($report_type) {
    case 'user_activity':
        $report_data = $admin->getUserActivityReport($date_range);
        break;
    case 'match_success':
        $report_data = $admin->getMatchSuccessReport($date_range);
        break;
    case 'demographics':
        $report_data = $admin->getDemographicsReport();
        break;
    case 'engagement':
        $report_data = $admin->getEngagementReport($date_range);
        break;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detailed Reports - SoulMingle Admin</title>
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
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">Detailed Reports</h1>
                    <div class="flex items-center space-x-4">
                        <select id="reportType" onchange="window.location.href='?type=' + this.value + '&range=<?php echo $date_range; ?>'"
                                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="user_activity" <?php echo $report_type === 'user_activity' ? 'selected' : ''; ?>>User Activity</option>
                            <option value="match_success" <?php echo $report_type === 'match_success' ? 'selected' : ''; ?>>Match Success</option>
                            <option value="demographics" <?php echo $report_type === 'demographics' ? 'selected' : ''; ?>>Demographics</option>
                            <option value="engagement" <?php echo $report_type === 'engagement' ? 'selected' : ''; ?>>User Engagement</option>
                        </select>
                        
                        <?php if($report_type !== 'demographics'): ?>
                            <select id="dateRange" onchange="window.location.href='?type=<?php echo $report_type; ?>&range=' + this.value"
                                    class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="last_7_days" <?php echo $date_range === 'last_7_days' ? 'selected' : ''; ?>>Last 7 Days</option>
                                <option value="last_30_days" <?php echo $date_range === 'last_30_days' ? 'selected' : ''; ?>>Last 30 Days</option>
                                <option value="last_90_days" <?php echo $date_range === 'last_90_days' ? 'selected' : ''; ?>>Last 90 Days</option>
                                <option value="last_year" <?php echo $date_range === 'last_year' ? 'selected' : ''; ?>>Last Year</option>
                            </select>
                        <?php endif; ?>

                        <button onclick="exportReport()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i> Export Report
                        </button>
                    </div>
                </div>

                <!-- Report Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Main Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6 col-span-2">
                        <canvas id="mainChart"></canvas>
                    </div>

                    <!-- Additional Charts and Stats -->
                    <?php if($report_type === 'user_activity'): ?>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-4">User Growth</h3>
                            <canvas id="userGrowthChart"></canvas>
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-4">Daily Active Users</h3>
                            <canvas id="activeUsersChart"></canvas>
                        </div>
                    <?php elseif($report_type === 'match_success'): ?>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-4">Match Success Rate</h3>
                            <canvas id="matchRateChart"></canvas>
                        </div>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-4">Connection Timeline</h3>
                            <canvas id="connectionTimelineChart"></canvas>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Detailed Statistics Table -->
                <div class="bg-white rounded-lg shadow-md mt-8">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-4">Detailed Statistics</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <?php foreach(array_keys($report_data[0] ?? []) as $header): ?>
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <?php echo htmlspecialchars(str_replace('_', ' ', $header)); ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach($report_data as $row): ?>
                                        <tr>
                                            <?php foreach($row as $value): ?>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?php echo htmlspecialchars($value); ?>
                                                </td>
                                            <?php endforeach; ?>
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
        // Initialize charts based on report type
        const reportData = <?php echo json_encode($report_data); ?>;
        
        function initializeCharts() {
            const ctx = document.getElementById('mainChart').getContext('2d');
            
            switch('<?php echo $report_type; ?>') {
                case 'user_activity':
                    initializeUserActivityCharts();
                    break;
                case 'match_success':
                    initializeMatchSuccessCharts();
                    break;
                case 'demographics':
                    initializeDemographicsCharts();
                    break;
                case 'engagement':
                    initializeEngagementCharts();
                    break;
            }
        }

        function exportReport() {
            window.location.href = `export-stats.php?type=<?php echo $report_type; ?>&range=<?php echo $date_range; ?>`;
        }

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', initializeCharts);
    </script>
</body>
</html> 