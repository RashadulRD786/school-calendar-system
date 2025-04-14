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

// Get data
$email = $_POST['email'];
$password = $_POST['password'];

// Look for the user
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($password === $user['password']) {
      echo "✅ Password matched";
      exit();
  } else {
      echo "❌ Password did not match";
      exit();
  }  
  
} else {
    echo "❌ No user found with that email.";
}

$conn->close();
?>
