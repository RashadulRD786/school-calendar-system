<?php
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);
require 'db_connection.php';
// Mark all notifications as read
$sql = "UPDATE notifications SET is_read = 1 WHERE is_read = 0";
if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
}
?>
