<?php
require 'db_connection.php';

// Only show notifications from the last 7 days
$seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
$query = "SELECT * FROM notifications WHERE created_at >= '$seven_days_ago' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$notifications = [];
$unread_count = 0;

while ($row = mysqli_fetch_assoc($result)) {
  $is_read = (bool) $row['is_read'];
  if (!$is_read) $unread_count++;
  $notifications[] = [
    'id' => (int) $row['id'],
    'eventName' => $row['event_name'],
    'eventDate' => $row['event_date'],
    'eventTime' => $row['event_time'],
    'read' => $is_read,
    'eventId' => (int) $row['event_id'],
    'created_at' => $row['created_at']
  ];
}

header('Content-Type: application/json');
echo json_encode([
  'notifications' => $notifications,
  'unread_count' => $unread_count
]);
?>
