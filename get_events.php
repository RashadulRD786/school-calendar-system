<?php
// Set the content type to JSON immediately
header('Content-Type: application/json');

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$port = 3307; // Make sure this port is correct for your MySQL setup

$response = []; // Initialize an array to hold the response

// Attempt to connect to the database
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    $response['error'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit; // Use exit instead of die after sending JSON
}

// Add status to the SELECT statement
$stmt = $conn->prepare("SELECT id, name, day, date, time, location, involvement, person_in_charge, unit, status FROM events");

if (!$stmt) {
    $response['error'] = "Error preparing statement: " . $conn->error; // Use $conn->error for prepare errors
    echo json_encode($response);
    $conn->close();
    exit;
}

if (!$stmt->execute()) {
    $response['error'] = "Error executing statement: " . $stmt->error;
    echo json_encode($response);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'day' => $row['day'],
        'date' => $row['date'],
        'time' => $row['time'],
        'location' => $row['location'],
        'involvement' => $row['involvement'],
        'person_in_charge' => $row['person_in_charge'],
        'unit' => $row['unit'],
        'status' => $row['status'] // <-- Add status here
    ];
}

// If successful, the response will be the events array
if (empty($response)) { // Check if an error was already set
    echo json_encode($events);
}

$stmt->close();
$conn->close();
?>