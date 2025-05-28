<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // default for XAMPP
$dbname = 'school_system';

$conn = new mysqli($host, $user, $pass, $dbname,3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'db.php';

$id = $_POST['id'];
$name = $_POST['name'];
$email = $_POST['email'];

$sql = "UPDATE users SET name='$name', email='$email' WHERE id=$id";
if ($conn->query($sql)) {
  echo "User updated successfully.";
} else {
  echo "Error: " . $conn->error;
}
?>