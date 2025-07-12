<?php
// Show all errors during development
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, 3307);
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

    // Add notification to DB for admin dropdown
    $notif_event_id = $conn->insert_id;
    $notif_sql = "INSERT INTO notifications (event_id, event_name, event_date, event_time, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
    $notif_stmt = $conn->prepare($notif_sql);
    if ($notif_stmt) {
        $notif_stmt->bind_param("isss", $notif_event_id, $name, $date, $time);
        if (!$notif_stmt->execute()) {
            echo "❌ Notification insert error: " . $notif_stmt->error;
        }
        $notif_stmt->close();
    } else {
        echo "❌ Notification prepare error: " . $conn->error;
    }

} else {
    echo "❌ Failed to add event: " . $stmt->error;
}

$conn->close();
?>