<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db); // <-- FIXED HERE

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT id, full_name FROM teacher_list";
$result = $conn->query($sql);

$teachers = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($teachers);
$conn->close();
?>
