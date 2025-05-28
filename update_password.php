<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db,3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$token = $_POST['token'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

if ($newPassword !== $confirmPassword) {
    echo "❌ Passwords do not match.";
    exit();
}

$sql = "SELECT * FROM users WHERE reset_token = ? AND reset_expires > ?";
$stmt = $conn->prepare($sql);
$currentTime = date("U");
$stmt->bind_param("ss", $token, $currentTime);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $hashedPassword = $newPassword; // Or use password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
    $stmt->bind_param("ss", $hashedPassword, $token);
    $stmt->execute();
    echo "✅ Password updated successfully.";
} else {
    echo "❌ Invalid or expired token.";
}

$conn->close();
?>
