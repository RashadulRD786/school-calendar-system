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
    $email = $_POST['email'];
    $plainPassword = $_POST['password'];
    $role = $_POST['role'];

    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO user_management (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashedPassword, $role);

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
