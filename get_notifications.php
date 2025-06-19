<?php
require 'db_connection.php';

$result = mysqli_query($conn, "SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = [];

while ($row = mysqli_fetch_assoc($result)) {
  $notifications[] = [
    'id' => (int) $row['id'],
    'message' => $row['message'],
    'read' => (bool) $row['read_status'],
    'eventId' => (int) $row['event_id']
  ];
}

header('Content-Type: application/json');
echo json_encode($notifications);
?>
