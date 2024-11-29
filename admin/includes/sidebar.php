<aside class="w-64 bg-white shadow-md">
    <div class="p-4 border-b">
        <h2 class="text-2xl font-bold text-purple-600"><i class="fas fa-heart"></i> SoulMingle</h2>
        <p class="text-sm text-gray-600">Admin Panel</p>
    </div>
    
    <nav class="mt-4">
        <a href="index.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'bg-purple-100 text-purple-600' : ''; ?>">
            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
        </a>
        <a href="users.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'bg-purple-100 text-purple-600' : ''; ?>">
            <i class="fas fa-users mr-2"></i> Users
        </a>
        <a href="profiles.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center <?php echo basename($_SERVER['PHP_SELF']) === 'profiles.php' ? 'bg-purple-100 text-purple-600' : ''; ?>">
            <i class="fas fa-user-circle mr-2"></i> Profiles
        </a>
        <a href="matches.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center <?php echo basename($_SERVER['PHP_SELF']) === 'matches.php' ? 'bg-purple-100 text-purple-600' : ''; ?>">
            <i class="fas fa-heart mr-2"></i> Matches
        </a>
        <a href="reports.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center <?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'bg-purple-100 text-purple-600' : ''; ?>">
            <i class="fas fa-flag mr-2"></i> Reports
        </a>
        <a href="settings.php" class="block py-2 px-4 text-gray-700 hover:bg-purple-100 hover:text-purple-600 transition-colors duration-200 flex items-center <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'bg-purple-100 text-purple-600' : ''; ?>">
            <i class="fas fa-cog mr-2"></i> Settings
        </a>
    </nav>

    <div class="absolute bottom-0 w-64 p-4 border-t">
        <a href="../public/logout.php" class="block w-full py-2 px-4 bg-red-500 text-white text-center rounded hover:bg-red-600 transition-colors duration-200">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
    </div>
</aside> 