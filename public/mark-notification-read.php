<?php
session_start();

if(!isset($_SESSION['user_id']) || !isset($_POST['notification_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

require_once "../config/database.php";
require_once "../includes/Notification.php";

$database = new Database();
$db = $database->getConnection();
$notification = new Notification($db);

$success = $notification->markAsRead($_POST['notification_id']);
echo json_encode(['success' => $success]); 