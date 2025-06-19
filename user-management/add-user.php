<?php
$host = 'localhost';
$user = 'root';
$password = '';
$db = 'school_system';

$conn = new mysqli($host, $user, $password, $db,3307);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    

    $stmt = $conn->prepare("INSERT INTO users (email, password, role, name) VALUES (?, ?, ?,?)");
    $stmt->bind_param("ssss", $email, $password, $role,$name);

    if ($stmt->execute()) {
        // Redirect to user-management.php after successful insertion
        header("Location: user-management.php");
        exit();
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
