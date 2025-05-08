<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default for XAMPP
$dbname = 'school_system';

$conn = new mysqli($host, $user, $pass, $dbname,3307);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Get user ID from the URL
    $stmt = $conn->prepare("DELETE FROM user_management WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redirect to user-management.php after successful deletion
        header("Location: user-management.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request. User ID is required.";
}
$conn->close();
?>
