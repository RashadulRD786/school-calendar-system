<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db,3307);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$name = $_POST['name'];
$day = $_POST['day'];
$date = $_POST['date'];
$time = $_POST['time'];
$location = $_POST['location'];
$involvement = $_POST['involvement'];
$person_in_charge = $_POST['person_in_charge'];
$unit = $_POST['unit'];

$stmt = $conn->prepare("UPDATE events SET name=?, day=?, date=?, time=?, location=?, involvement=?, person_in_charge=?, unit=? WHERE id=?");
$stmt->bind_param("ssssssssi", $name, $day, $date, $time, $location, $involvement, $person_in_charge, $unit, $id);

if ($stmt->execute()) {
  echo "✅ Event updated.";
} else {
  echo "❌ Failed to update: " . $stmt->error;
}

$conn->close();
?>
