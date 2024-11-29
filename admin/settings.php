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

// Handle settings updates
if($_POST) {
    switch($_POST['setting_type']) {
        case 'site':
            if($admin->updateSiteSettings($_POST)) {
                $success_message = "Site settings updated successfully";
            } else {
                $error_message = "Failed to update site settings";
            }
            break;
            
        case 'email':
            if($admin->updateEmailSettings($_POST)) {
                $success_message = "Email settings updated successfully";
            } else {
                $error_message = "Failed to update email settings";
            }
            break;
            
        case 'security':
            if($admin->updateSecuritySettings($_POST)) {
                $success_message = "Security settings updated successfully";
            } else {
                $error_message = "Failed to update security settings";
            }
            break;
    }
}

// Get current settings
$site_settings = $admin->getSiteSettings();
$email_settings = $admin->getEmailSettings();
$security_settings = $admin->getSecuritySettings();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - SoulMingle Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include "includes/sidebar.php"; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
            <div class="container mx-auto px-6 py-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-6">System Settings</h1>

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

                <!-- Site Settings -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-globe mr-2 text-indigo-600"></i> Site Settings
                    </h2>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="setting_type" value="site">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Site Name</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars($site_settings['site_name']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Site Description</label>
                            <textarea name="site_description" rows="3" 
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            ><?php echo htmlspecialchars($site_settings['site_description']); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Maintenance Mode</label>
                            <select name="maintenance_mode" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="0" <?php echo !$site_settings['maintenance_mode'] ? 'selected' : ''; ?>>Off</option>
                                <option value="1" <?php echo $site_settings['maintenance_mode'] ? 'selected' : ''; ?>>On</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Save Site Settings
                        </button>
                    </form>
                </div>

                <!-- Email Settings -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-envelope mr-2 text-indigo-600"></i> Email Settings
                    </h2>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="setting_type" value="email">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($email_settings['smtp_host']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Port</label>
                            <input type="text" name="smtp_port" value="<?php echo htmlspecialchars($email_settings['smtp_port']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Username</label>
                            <input type="text" name="smtp_username" value="<?php echo htmlspecialchars($email_settings['smtp_username']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">SMTP Password</label>
                            <input type="password" name="smtp_password" value="<?php echo htmlspecialchars($email_settings['smtp_password']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Save Email Settings
                        </button>
                    </form>
                </div>

                <!-- Security Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">
                        <i class="fas fa-shield-alt mr-2 text-indigo-600"></i> Security Settings
                    </h2>
                    <form method="post" class="space-y-4">
                        <input type="hidden" name="setting_type" value="security">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Minimum Password Length</label>
                            <input type="number" name="min_password_length" value="<?php echo htmlspecialchars($security_settings['min_password_length']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Maximum Login Attempts</label>
                            <input type="number" name="max_login_attempts" value="<?php echo htmlspecialchars($security_settings['max_login_attempts']); ?>" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Enable Two-Factor Authentication</label>
                            <select name="enable_2fa" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="0" <?php echo !$security_settings['enable_2fa'] ? 'selected' : ''; ?>>Disabled</option>
                                <option value="1" <?php echo $security_settings['enable_2fa'] ? 'selected' : ''; ?>>Enabled</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Save Security Settings
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 