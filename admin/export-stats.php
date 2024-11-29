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

// Get the export type from query string
$type = isset($_GET['type']) ? $_GET['type'] : 'users';

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $type . '_report_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

switch($type) {
    case 'users':
        // Headers for users report
        fputcsv($output, ['ID', 'Username', 'Email', 'Join Date', 'Profile Status', 'Matches Count']);
        
        // Get all users data
        $users = $admin->getAllUsersForExport();
        foreach($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['username'],
                $user['email'],
                $user['created_at'],
                $user['has_profile'] ? 'Complete' : 'Incomplete',
                $user['matches_count']
            ]);
        }
        break;

    case 'matches':
        // Headers for matches report
        fputcsv($output, ['Match ID', 'User 1', 'User 2', 'Status', 'Created Date']);
        
        // Get all matches data
        $matches = $admin->getAllMatchesForExport();
        foreach($matches as $match) {
            fputcsv($output, [
                $match['id'],
                $match['user1_name'],
                $match['user2_name'],
                $match['status'],
                $match['created_at']
            ]);
        }
        break;

    case 'demographics':
        // Headers for demographics report
        fputcsv($output, ['Category', 'Value', 'Count', 'Percentage']);
        
        // Get demographics data
        $demographics = $admin->getDemographicsForExport();
        
        // Gender distribution
        foreach($demographics['gender'] as $gender => $data) {
            fputcsv($output, ['Gender', $gender, $data['count'], $data['percentage'] . '%']);
        }
        
        // Age distribution
        foreach($demographics['age'] as $range => $data) {
            fputcsv($output, ['Age Range', $range, $data['count'], $data['percentage'] . '%']);
        }
        
        // Religion distribution
        foreach($demographics['religion'] as $religion => $data) {
            fputcsv($output, ['Religion', $religion, $data['count'], $data['percentage'] . '%']);
        }
        break;

    case 'activity':
        // Headers for activity report
        fputcsv($output, ['Date', 'New Users', 'New Matches', 'Active Users']);
        
        // Get daily activity data for the last 30 days
        $activity = $admin->getActivityReportData();
        foreach($activity as $day) {
            fputcsv($output, [
                $day['date'],
                $day['new_users'],
                $day['new_matches'],
                $day['active_users']
            ]);
        }
        break;
}

fclose($output);
exit();
?> 