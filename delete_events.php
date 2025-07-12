<?php
header('Content-Type: application/json');

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$port = 3307;

$response = [];

if (!isset($_POST['from']) || !isset($_POST['to'])) {
    $response['success'] = false;
    $response['message'] = "❌ Missing date range.";
    echo json_encode($response);
    exit;
}

$from = $_POST['from'];
$to = $_POST['to'];

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = "❌ DB connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

// use prepared statements
$stmt = $conn->prepare("DELETE FROM events WHERE date BETWEEN ? AND ?");
$stmt->bind_param("ss", $from, $to);

if ($stmt->execute()) {
    $deletedRows = $stmt->affected_rows;
    $response['success'] = true;
    $response['message'] = "✅ Deleted $deletedRows events between $from and $to.";
} else {
    $response['success'] = false;
    $response['message'] = "❌ Deletion failed: " . $stmt->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
?>