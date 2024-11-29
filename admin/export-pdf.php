<?php
session_start();

if(!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

require_once "../config/database.php";
require_once "../includes/Admin.php";
require_once '../vendor/autoload.php'; // Make sure you have TCPDF installed via composer

use TCPDF;

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

// Get export parameters
$type = isset($_GET['type']) ? $_GET['type'] : 'users';
$date_range = isset($_GET['range']) ? $_GET['range'] : 'last_30_days';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('SoulMingle');
$pdf->SetAuthor('SoulMingle Admin');
$pdf->SetTitle(ucfirst($type) . ' Report');

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'SoulMingle Report', 'Generated on ' . date('Y-m-d H:i:s'));

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 10);

// Get report data
switch($type) {
    case 'users':
        $data = $admin->getAllUsersForExport();
        $headers = ['ID', 'Username', 'Email', 'Join Date', 'Profile Status', 'Matches'];
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

// Create the table
$html = '<table border="1" cellpadding="4">';

// Add headers
$html .= '<tr>';
foreach($headers as $header) {
    $html .= '<th style="font-weight: bold; background-color: #f0f0f0;">' . $header . '</th>';
}
$html .= '</tr>';

// Add data rows
foreach($data as $row) {
    $html .= '<tr>';
    foreach($row as $value) {
        $html .= '<td>' . htmlspecialchars($value) . '</td>';
    }
    $html .= '</tr>';
}

$html .= '</table>';

// Output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('soulmingle_report_' . $type . '_' . date('Y-m-d') . '.pdf', 'D');
?> 