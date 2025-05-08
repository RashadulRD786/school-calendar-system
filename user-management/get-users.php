<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'school_system';

$conn = new mysqli($host, $user, $password, $db,3307);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

$sql = "SELECT id, email FROM user_management";
$result = $conn->query($sql);

$users = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($users);
$conn->close();
?>
