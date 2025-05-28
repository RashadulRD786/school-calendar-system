<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
  $id = intval($_POST['id']);

  $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
  $stmt->bind_param("i", $id);

  if ($stmt->execute()) {
    header("Location: user-management.php");
    exit();
  } else {
    echo "Error deleting user: " . $stmt->error;
  }
} else {
  echo "Invalid request.";
}

$conn->close();
?>
