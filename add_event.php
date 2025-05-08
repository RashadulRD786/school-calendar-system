ini_set('display_errors', 1);
error_reporting(E_ALL);

<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db,3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$name = $_POST['name'];
$day = $_POST['day'];
$date = $_POST['date'];
$time = $_POST['time'];
$location = $_POST['location'];
$involvement = $_POST['involvement'];
$person_in_charge = $_POST['person_in_charge'];
$unit = $_POST['unit'];

// Insert into DB
$stmt = $conn->prepare("INSERT INTO events (name, day, date, time, location, involvement, person_in_charge, unit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $name, $day, $date, $time, $location, $involvement, $person_in_charge, $unit);

if ($stmt->execute()) {
    echo "✅ Event added successfully.";
} else {
    echo "❌ Failed to add event.";
}

$conn->close();
?>
