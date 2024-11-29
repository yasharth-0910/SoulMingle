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

// Handle match actions
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'delete_match':
            if($admin->deleteMatch($_POST['match_id'])) {
                $success_message = "Match deleted successfully";
            } else {
                $error_message = "Failed to delete match";
            }
            break;
        case 'update_status':
            if($admin->updateMatchStatus($_POST['match_id'], $_POST['status'])) {
                $success_message = "Match status updated successfully";
            } else {
                $error_message = "Failed to update match status";
            }
            break;
    }
}

// Get filters and pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$status = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get matches with pagination
$matches = $admin->getAllMatches($page, $limit, $status, $search);
$total_matches = $admin->getTotalMatches($status, $search);
$total_pages = ceil($total_matches / $limit);

// Get match statistics
$match_stats = $admin->getMatchStatistics();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Match Management - SoulMingle Admin</title>
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
                    <h1 class="text-3xl font-bold text-gray-800">Match Management</h1>
                    <div class="flex items-center space-x-4">
                        <form class="flex items-center space-x-2">
                            <input type="text" name="search" placeholder="Search matches..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <select name="status" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Statuses</option>
                                <option value="Connected" <?php echo $status === 'Connected' ? 'selected' : ''; ?>>Connected</option>
                                <option value="Interested" <?php echo $status === 'Interested' ? 'selected' : ''; ?>>Interested</option>
                                <option value="Rejected" <?php echo $status === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <a href="export-matches.php" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-file-export mr-2"></i> Export
                        </a>
                    </div>
                </div>

                <!-- Match Statistics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500">
                                <i class="fas fa-heart text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Connected Matches</p>
                                <p class="text-2xl font-semibold text-gray-800"><?php echo $match_stats['connected']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Pending Matches</p>
                                <p class="text-2xl font-semibold text-gray-800"><?php echo $match_stats['interested']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-500">
                                <i class="fas fa-times text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-500">Rejected Matches</p>
                                <p class="text-2xl font-semibold text-gray-800"><?php echo $match_stats['rejected'] ?? 0; ?></p>
                            </div>
                        </div>
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

                <!-- Matches Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User 1</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User 2</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($matches as $match): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="../uploads/photos/<?php echo $match['user1_photo'] ?? 'default.jpg'; ?>" 
                                                     alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($match['user1_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($match['user1_email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="../uploads/photos/<?php echo $match['user2_photo'] ?? 'default.jpg'; ?>" 
                                                     alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($match['user2_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($match['user2_email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                     <?php 
                                                        echo match($match['status']) {
                                                            'Connected' => 'bg-green-100 text-green-800',
                                                            'Interested' => 'bg-yellow-100 text-yellow-800',
                                                            'Rejected' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        };
                                                     ?>">
                                            <?php echo $match['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($match['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <form method="post" class="inline">
                                                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                                <select name="status" onchange="this.form.submit()" 
                                                        class="text-sm border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    <option value="Connected" <?php echo $match['status'] === 'Connected' ? 'selected' : ''; ?>>Connected</option>
                                                    <option value="Interested" <?php echo $match['status'] === 'Interested' ? 'selected' : ''; ?>>Interested</option>
                                                    <option value="Rejected" <?php echo $match['status'] === 'Rejected' ? 'selected' : ''; ?>>Rejected</option>
                                                </select>
                                                <input type="hidden" name="action" value="update_status">
                                            </form>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="match_id" value="<?php echo $match['id']; ?>">
                                                <button type="submit" name="action" value="delete_match"
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure you want to delete this match?')">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if($total_pages > 1): ?>
                    <div class="flex justify-center mt-6">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>&search=<?php echo urlencode($search); ?>" 
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