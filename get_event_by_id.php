<?php
// Set the content type to JSON immediately
header('Content-Type: application/json');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$port = 3307; // Make sure this port is correct for your MySQL setup

$response = []; // Initialize an array to hold the response data

// Get the event ID from the URL query parameter
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($eventId === null) {
    $response['error'] = 'Event ID not provided.';
    echo json_encode($response);
    exit;
}

// Attempt to connect to the database
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    $response['error'] = "Connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

// Prepare SQL statement to select a single event by ID
// Use a WHERE clause to filter by ID
$stmt = $conn->prepare("SELECT id, name, day, date, time, location, involvement, person_in_charge, unit, status FROM events WHERE id = ?");

if (!$stmt) {
    $response['error'] = "Error preparing statement: " . $conn->error;
    echo json_encode($response);
    $conn->close();
    exit;
}

// Bind the event ID parameter (assuming 'id' is an integer 'i')
$stmt->bind_param("i", $eventId);

if (!$stmt->execute()) {
    $response['error'] = "Error executing statement: " . $stmt->error;
    echo json_encode($response);
    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();
$eventDetails = [];

// Fetch the single row (if found)
if ($row = $result->fetch_assoc()) {
    $eventDetails[] = [ // Still return as an array containing one object for consistency with JS
        'id' => (int)$row['id'],
        'name' => $row['name'],
        'day' => $row['day'],
        'date' => $row['date'],
        'time' => $row['time'],
        'location' => $row['location'],
        'involvement' => $row['involvement'],
        'person_in_charge' => $row['person_in_charge'],
        'unit' => $row['unit'],
        'status' => $row['status']
    ];
}

// If successful, the response will be the eventDetails array (either with one event or empty)
echo json_encode($eventDetails);

$stmt->close();
$conn->close();
?>