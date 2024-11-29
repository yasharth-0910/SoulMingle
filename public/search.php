<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Search.php";

$database = new Database();
$db = $database->getConnection();
$search = new Search($db);

// Get filters from form submission or query string
$filters = [
    'min_age' => isset($_REQUEST['min_age']) ? (int)$_REQUEST['min_age'] : null,
    'max_age' => isset($_REQUEST['max_age']) ? (int)$_REQUEST['max_age'] : null,
    'gender' => isset($_REQUEST['gender']) ? $_REQUEST['gender'] : null,
    'religion' => isset($_REQUEST['religion']) ? $_REQUEST['religion'] : null,
    'education' => isset($_REQUEST['education']) ? $_REQUEST['education'] : null
];

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;

// Get search results
$results = $search->searchProfiles($filters, $_SESSION['user_id'], $page, $limit);
$total_results = $search->getTotalResults($filters, $_SESSION['user_id']);
$total_pages = ceil($total_results / $limit);

// Save search filters if form was submitted
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search->saveSearchFilters($_SESSION['user_id'], $filters);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Profiles - SoulMingle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-inter">
    <div class="bg-white shadow">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <?php include "../includes/notification-bar.php"; ?>
            <div class="nav-links">
                <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-1/3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4"><i class="fas fa-filter mr-2"></i> Search Filters</h2>
                    <form method="post" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Age Range</label>
                            <div class="flex items-center space-x-2">
                                <input type="number" name="min_age" placeholder="Min Age" 
                                       value="<?php echo $filters['min_age']; ?>" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <span class="text-gray-500">to</span>
                                <input type="number" name="max_age" placeholder="Max Age" 
                                       value="<?php echo $filters['max_age']; ?>" 
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">Any</option>
                                <option value="Male" <?php echo $filters['gender'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo $filters['gender'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo $filters['gender'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                            <input type="text" name="religion" 
                                   value="<?php echo htmlspecialchars($filters['religion']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Education</label>
                            <input type="text" name="education" 
                                   value="<?php echo htmlspecialchars($filters['education']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>

                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-search mr-2"></i> Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            <div class="w-full md:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-4">Search Results (<?php echo $total_results; ?>)</h2>
                    
                    <?php if(empty($results)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-search text-gray-400 text-5xl mb-4"></i>
                            <p class="text-gray-500">No profiles found matching your criteria.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach($results as $profile): ?>
                                <div class="bg-white rounded-lg shadow-md overflow-hidden transition duration-300 ease-in-out transform hover:scale-105">
                                    <img src="../uploads/photos/<?php echo htmlspecialchars($profile['photo_url']); ?>" 
                                         alt="Profile Photo" class="w-full h-48 object-cover">
                                    <div class="p-4">
                                        <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($profile['username']); ?></h3>
                                        <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($profile['age']); ?> years old</p>
                                        <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($profile['religion']); ?></p>
                                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($profile['education']); ?></p>
                                        <a href="view-profile.php?id=<?php echo $profile['user_id']; ?>" 
                                           class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 ease-in-out">View Profile</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($total_pages > 1): ?>
                        <div class="mt-8 flex justify-center">
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <a href="?page=<?php echo $i; ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 <?php echo $page === $i ? 'bg-indigo-50 text-indigo-600' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                <?php endfor; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>