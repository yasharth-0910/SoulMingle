<?php
session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Admin.php";
require_once '../vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed via composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Get export type from query string
$type = isset($_GET['type']) ? $_GET['type'] : 'users';
$date_range = isset($_GET['range']) ? $_GET['range'] : 'last_30_days';

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Get data based on type
switch($type) {
    case 'users':
        $data = $admin->getAllUsersForExport();
        $headers = ['ID', 'Username', 'Email', 'Join Date', 'Profile Status', 'Matches Count'];
        break;
    case 'matches':
        $data = $admin->getAllMatchesForExport();
        $headers = ['Match ID', 'User 1', 'User 2', 'Status', 'Created Date'];
        break;
    case 'demographics':
        $data = $admin->getDemographicsForExport();
        $headers = ['Category', 'Value', 'Count', 'Percentage'];
        break;
    case 'activity':
        $data = $admin->getActivityReportData();
        $headers = ['Date', 'New Users', 'New Matches', 'Active Users'];
        break;
}

// Set headers
$col = 'A';
foreach($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $sheet->getColumnDimension($col)->setAutoSize(true);
    $col++;
}

// Style headers
$headerStyle = [
    'font' => ['bold' => true],
    'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E0E0E0']
    ]
];
$sheet->getStyle('A1:' . $col . '1')->applyFromArray($headerStyle);

// Add data
$row = 2;
foreach($data as $rowData) {
    $col = 'A';
    foreach($rowData as $value) {
        $sheet->setCellValue($col . $row, $value);
        $col++;
    }
    $row++;
}

// Create Excel file
$writer = new Xlsx($spreadsheet);

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="soulmingle_report_' . $type . '_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

// Save to output
$writer->save('php://output'); 