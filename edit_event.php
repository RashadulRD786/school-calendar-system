<?php
// Set the content type to JSON immediately
header('Content-Type: application/json');

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = ''; // Ensure this is your correct MySQL password
$port = 3306; // Correct port

$response = []; // Initialize an array to hold the response

// Attempt to connect to the database
$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    $response['success'] = false;
    $response['message'] = "Database connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit;
}

// Check if all required POST data is set (add 'status')
if (
    isset(
        $_POST['id'],
        $_POST['name'],
        $_POST['day'],
        $_POST['date'],
        $_POST['time'],
        $_POST['location'],
        $_POST['involvement'],
        $_POST['person_in_charge'],
        $_POST['unit'],
        $_POST['status'] // <-- Add status
    )
) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $day = $_POST['day'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $involvement = $_POST['involvement'];
    $person_in_charge = $_POST['person_in_charge'];
    $unit = $_POST['unit'];
    $status = $_POST['status']; // <-- Add status

    // Update query now includes status
    $stmt = $conn->prepare("UPDATE events SET name=?, day=?, date=?, time=?, location=?, involvement=?, person_in_charge=?, unit=?, status=? WHERE id=?");
    if ($stmt) {
        // 9 strings (s) and 1 integer (i) for the id
        $stmt->bind_param("sssssssssi", $name, $day, $date, $time, $location, $involvement, $person_in_charge, $unit, $status, $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = "✅ Event updated successfully.";
            } else {
                $response['success'] = false; // Or true, depending on how you want to handle no actual change
                $response['message'] = "ℹ Event data was the same, no rows updated, or event not found.";
            }
        } else {
            $response['success'] = false;
            $response['message'] = "❌ Failed to update event: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "❌ Error preparing statement: " . $conn->error;
    }
} else {
    $response['success'] = false;
    $response['message'] = "❌ Required form data for editing is missing.";
}

$conn->close();
echo json_encode($response);
?>