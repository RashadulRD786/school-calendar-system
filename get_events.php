<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db,3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT id, name, day, date, time, location, involvement, person_in_charge, unit FROM events"); // Include id
if (!$stmt) {
    // Log the error (e.g., error_log(mysqli_error($conn));)
    die("Error preparing statement: " . mysqli_error($conn));
}

if (!$stmt->execute()) {
    // Log the error
    die("Error executing statement: " . $stmt->error);
}

$result = $stmt->get_result();
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => (int)$row['id'], // Explicitly cast to integer
        'name' => $row['name'],
        'day' => $row['day'],
        'date' => $row['date'],
        'time' => $row['time'],
        'location' => $row['location'],
        'involvement' => $row['involvement'],
        'person_in_charge' => $row['person_in_charge'],
        'unit' => $row['unit']
    ];
}

header('Content-Type: application/json');
echo json_encode($events);

$stmt->close();
$conn->close();
?>