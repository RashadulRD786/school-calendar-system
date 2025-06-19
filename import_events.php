<?php
ob_clean(); // Clear any previous output
header('Content-Type: application/json');

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'school_system';

$conn = new mysqli($host, $username, $password, $dbname,3307);
if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

if ($_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
    try {
        $spreadsheet = IOFactory::load($_FILES['excel_file']['tmp_name']);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            if (count($row) < 8) continue;

            list($name, $day, $date, $time, $location, $involvement, $person_in_charge, $unit) = $row;

            $stmt = $conn->prepare("INSERT INTO events (name, day, date, time, location, involvement, person_in_charge, unit, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Non-Complete')");
            $stmt->bind_param("ssssssss", $name, $day, $date, $time, $location, $involvement, $person_in_charge, $unit);
            $stmt->execute();
        }

        echo json_encode([
            'status' => 'success',
            'message' => '✅ Import successful!'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '❌ Failed to process file: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '❌ File upload error.'
    ]);
}

$conn->close();
?>