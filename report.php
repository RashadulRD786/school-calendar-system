<?php
require 'vendor/autoload.php'; // PhpSpreadsheet autoloader

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
<<<<<<< HEAD
$conn = new mysqli($host, $user, $pass, $db,3307);
=======
$conn = new mysqli($host, $user, $pass, $db);
>>>>>>> 5ddfb305a158c9574ee477c7d14a6ed5ea099c2c

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get date range and status from the form
$from = $_POST['from'] ?? '';
$to = $_POST['to'] ?? '';
$status = $_POST['status'] ?? 'any';

if (!$from || !$to) {
    die("Please provide both 'from' and 'to' dates.");
}

// Query events based on date and status
if ($status === 'Complete') {
    $stmt = $conn->prepare("SELECT name, day, date, time, location, involvement, person_in_charge, unit, status FROM events WHERE date BETWEEN ? AND ? AND status = ?");
    $stmt->bind_param("sss", $from, $to, $status);
} elseif ($status === 'Non-Complete') {
    $excluded = 'Complete';
    $stmt = $conn->prepare("SELECT name, day, date, time, location, involvement, person_in_charge, unit, status FROM events WHERE date BETWEEN ? AND ? AND status != ?");
    $stmt->bind_param("sss", $from, $to, $excluded);
} else {
    $stmt = $conn->prepare("SELECT name, day, date, time, location, involvement, person_in_charge, unit, status FROM events WHERE date BETWEEN ? AND ?");
    $stmt->bind_param("ss", $from, $to);
}


$stmt->execute();
$result = $stmt->get_result();

// Create a new spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header row
$headers = ['Event Name', 'Day', 'Date', 'Time', 'Location', 'Involvement', 'Person In Charge', 'Unit', 'Status'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Fill in event data
$row = 2;
while ($event = $result->fetch_assoc()) {
    $sheet->setCellValue("A$row", $event['name']);
    $sheet->setCellValue("B$row", $event['day']);
    $sheet->setCellValue("C$row", $event['date']);
    $sheet->setCellValue("D$row", $event['time']);
    $sheet->setCellValue("E$row", $event['location']);
    $sheet->setCellValue("F$row", $event['involvement']);
    $sheet->setCellValue("G$row", $event['person_in_charge']);
    $sheet->setCellValue("H$row", $event['unit']);
    $sheet->setCellValue("I$row", $event['status']);
    $row++;
}

// Set filename
$filename = "event_report_" . str_replace('-', '', $from) . "_to_" . str_replace('-', '', $to) . ".xlsx";

// Clear output buffer if needed
if (ob_get_length()) {
    ob_end_clean();
}

// Send file to browser for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

<<<<<<< HEAD
?>
=======
?>
>>>>>>> 5ddfb305a158c9574ee477c7d14a6ed5ea099c2c
