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

// Handle user actions
if(isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'delete_user':
            if($admin->deleteUser($_POST['user_id'])) {
                $success_message = "User deleted successfully";
            } else {
                $error_message = "Failed to delete user";
            }
            break;
        case 'ban_user':
            if($admin->banUser($_POST['user_id'])) {
                $success_message = "User banned successfully";
            } else {
                $error_message = "Failed to ban user";
            }
            break;
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 15;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$users = $admin->getAllUsers($page, $limit, $search, $filter);
$total_users = $admin->getTotalUsers($search, $filter);
$total_pages = ceil($total_users / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - SoulMingle Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Include admin sidebar -->
        <?php include "includes/sidebar.php"; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
                    <div class="flex items-center space-x-4">
                        <form class="flex items-center space-x-2">
                            <input type="text" name="search" placeholder="Search users..." 
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <select name="filter" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">All Users</option>
                                <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="banned" <?php echo $filter === 'banned' ? 'selected' : ''; ?>>Banned</option>
                                <option value="incomplete" <?php echo $filter === 'incomplete' ? 'selected' : ''; ?>>Incomplete Profile</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <a href="export-users.php" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-file-export mr-2"></i> Export
                        </a>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profile</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full" 
                                                     src="<?php echo $user['photo_url'] ? '../uploads/photos/' . htmlspecialchars($user['photo_url']) : '../assets/images/default-avatar.png'; ?>" 
                                                     alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($user['email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                     <?php echo $user['is_banned'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo $user['is_banned'] ? 'Banned' : 'Active'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $user['has_profile'] ? 'Complete' : 'Incomplete'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="view-user.php?id=<?php echo $user['id']; ?>" 
                                               class="text-indigo-600 hover:text-indigo-900">View</a>
                                            <?php if(!$user['is_banned']): ?>
                                                <form method="post" class="inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" name="action" value="ban_user"
                                                            class="text-yellow-600 hover:text-yellow-900"
                                                            onclick="return confirm('Are you sure you want to ban this user?')">
                                                        Ban
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" class="inline">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" name="action" value="unban_user"
                                                            class="text-green-600 hover:text-green-900">
                                                        Unban
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <form method="post" class="inline">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" name="action" value="delete_user"
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
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
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&filter=<?php echo urlencode($filter); ?>" 
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