<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$email = $_POST['email'];
$newPassword = $_POST['Npsw'];
$confirmPassword = $_POST['Cpsw'];

// Check if passwords match
if ($newPassword !== $confirmPassword) {
    die("❌ Passwords do not match.");
}

// Hash the new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

// Update password in database
$sql = "UPDATE users SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $hashedPassword, $email);
$stmt->execute();

if ($stmt->affected_rows === 1) {
    header("Location: password-reset-success.html");
    exit();
} else {
    echo "❌ Failed to update password or user not found.";
}

$conn->close();
?>
