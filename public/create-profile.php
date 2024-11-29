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
$success = false;

if($_POST) {
    $profile->user_id = $_SESSION['user_id'];
    $profile->age = $_POST['age'];
    $profile->gender = $_POST['gender'];
    $profile->religion = $_POST['religion'];
    $profile->education = $_POST['education'];
    $profile->interests = $_POST['interests'];
    
    // Handle photo upload
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = "../uploads/photos/";
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;
        
        if(move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $profile->photo_url = $file_name;
        } else {
            $message = "Failed to upload photo.";
        }
    }
    
    if($profile->create()) {
        $success = true;
        $message = "Profile created successfully!";
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Create Your Profile</h2>
        
        <?php if(!empty($message)): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" required class="form-control">
            </div>

            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required class="form-control">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Religion</label>
                <input type="text" name="religion" class="form-control">
            </div>

            <div class="form-group">
                <label>Education</label>
                <input type="text" name="education" class="form-control">
            </div>

            <div class="form-group">
                <label>Interests</label>
                <textarea name="interests" class="form-control"></textarea>
            </div>

            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" name="photo" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Create Profile</button>
        </form>
    </div>
</body>
</html> 