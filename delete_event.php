<?php
// Set the content type to JSON immediately
header('Content-Type: application/json');

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = ''; // Ensure this is your correct MySQL password
$port = 3307; // Correct port

$response = [];

// Check if ID is provided
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $response['success'] = false;
    $response['message'] = "❌ Event ID is missing.";
    echo json_encode($response);
    exit;
}

$id = $_POST['id'];

// Attempt to connect to the database
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = "Database connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

$stmt = $conn->prepare("DELETE FROM events WHERE id=?");
if ($stmt) {
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = "✅ Event deleted successfully.";
        } else {
            $response['success'] = false; // Or true, if not finding the event to delete isn't an error
            $response['message'] = "ℹ Event not found or already deleted.";
        }
    } else {
        $response['success'] = false;
        $response['message'] = "❌ Delete failed: " . $stmt->error;
    }
    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = "❌ Error preparing statement: " . $conn->error;
}

$conn->close();
echo json_encode($response);
?>