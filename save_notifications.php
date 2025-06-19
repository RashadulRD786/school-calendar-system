<?php
require 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
  http_response_code(400);
  echo "Invalid JSON";
  exit;
}

foreach ($data as $notif) {
  $id = intval($notif['id']);
  $read = $notif['read'] ? 1 : 0;

  $stmt = $conn->prepare("UPDATE notifications SET read_status = ? WHERE id = ?");
  $stmt->bind_param("ii", $read, $id);
  $stmt->execute();
}

echo "Notifications updated.";
?>
