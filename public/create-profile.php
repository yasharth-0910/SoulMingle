<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Profile.php";

$database = new Database();
$db = $database->getConnection();
$profile = new Profile($db);

$message = "";

if($_POST) {
    // Handle file upload first
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = "../uploads/photos/";
        
        // Create directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadFile = $uploadDir . $newFileName;

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if(in_array($fileExtension, $allowedTypes)) {
            if(move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $profile->photo_url = $newFileName;
            } else {
                $message = "Failed to upload photo.";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG and GIF allowed.";
        }
    }

    // Set profile properties
    $profile->user_id = $_SESSION['user_id'];
    $profile->age = $_POST['age'];
    $profile->gender = $_POST['gender'];
    $profile->religion = $_POST['religion'];
    $profile->education = $_POST['education'];
    $profile->interests = $_POST['interests'];

    // Create profile
    if($profile->create()) {
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Unable to create profile.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile - SoulMingle</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-inter">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-indigo-600 text-white py-4 px-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-user-circle mr-2"></i> Create Your Profile</h2>
                <p class="text-indigo-200">Tell us more about yourself</p>
            </div>

            <div class="p-6">
                <?php if(!empty($message)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p><?php echo $message; ?></p>
                    </div>
                <?php endif; ?>

                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-camera mr-2"></i> Profile Photo
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*" required
                               class="mt-1 block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100">
                    </div>

                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-birthday-cake mr-2"></i> Age
                        </label>
                        <input type="number" name="age" id="age" required min="18" max="100"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-venus-mars mr-2"></i> Gender
                        </label>
                        <select name="gender" id="gender" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                       focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="religion" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-pray mr-2"></i> Religion
                        </label>
                        <input type="text" name="religion" id="religion" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="education" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-graduation-cap mr-2"></i> Education
                        </label>
                        <input type="text" name="education" id="education" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                      focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="interests" class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-heart mr-2"></i> Interests
                        </label>
                        <textarea name="interests" id="interests" required rows="4"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                         focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"></textarea>
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-save mr-2"></i> Create Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>