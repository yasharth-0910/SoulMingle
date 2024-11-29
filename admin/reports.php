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

// Handle report actions
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'resolve_report':
            if($admin->resolveReport($_POST['report_id'])) {
                $success_message = "Report marked as resolved";
            } else {
                $error_message = "Failed to resolve report";
            }
            break;
        case 'delete_report':
            if($admin->deleteReport($_POST['report_id'])) {
                $success_message = "Report deleted successfully";
            } else {
                $error_message = "Failed to delete report";
            }
            break;
    }
}

// Get reports with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending'; // pending, resolved, all

$reports = $admin->getReports($page, $limit, $filter);
$total_reports = $admin->getTotalReports($filter);
$total_pages = ceil($total_reports / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management - SoulMingle Admin</title>
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
                    <h1 class="text-3xl font-bold text-gray-800">Reports Management</h1>
                    <div class="flex items-center space-x-4">
                        <select id="reportFilter" onchange="window.location.href='?filter=' + this.value" 
                                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Reports</option>
                            <option value="pending" <?php echo $filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="resolved" <?php echo $filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                        </select>
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

                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reported User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reported By
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($reports as $report): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="../uploads/photos/<?php echo htmlspecialchars($report['reported_user_photo']); ?>" 
                                                     alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($report['reported_username']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($report['reported_email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($report['reporter_username']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($report['reason']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                     <?php echo $report['status'] === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo ucfirst($report['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($report['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <?php if($report['status'] !== 'resolved'): ?>
                                                <form method="post" class="inline">
                                                    <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                    <button type="submit" name="action" value="resolve_report"
                                                            class="text-green-600 hover:text-green-900">
                                                        Resolve
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="report_id" value="<?php echo $report['id']; ?>">
                                                <button type="submit" name="action" value="delete_report"
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure you want to delete this report?')">
                                                    Delete
                                                </button>
                                            </form>
                                            <a href="view-user.php?id=<?php echo $report['reported_user_id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900">
                                                View User
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if($total_pages > 1): ?>
                    <div class="flex justify-center mt-6">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&filter=<?php echo $filter; ?>" 
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