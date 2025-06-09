<?php
// Show all errors during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data safely
$name = $_POST['name'];
$day = $_POST['day'];
$date = $_POST['date'];
$time = $_POST['time'];
$location = $_POST['location'];
$involvement = $_POST['involvement'];
$person_in_charge = $_POST['person_in_charge'];
$unit = $_POST['unit'];
$status = $_POST['status'];

// Insert into DB
$stmt = $conn->prepare("INSERT INTO events (name, day, date, time, location, involvement, person_in_charge, unit, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssss", $name, $day, $date, $time, $location, $involvement, $person_in_charge, $unit, $status);

if ($stmt->execute()) {
    echo "✅ Event added successfully.";

    // Send email only after successful insert
    require 'send_event_notification.php';

    $eventData = [
        'name' => $name,
        'date' => $date,
        'time' => $time,
        'location' => $location,
        'person_in_charge' => $person_in_charge,
        'status' => $status // Optional: include status in notification
    ];

    sendEventNotification($eventData);

} else {
    echo "❌ Failed to add event: " . $stmt->error;
}

$conn->close();
?>