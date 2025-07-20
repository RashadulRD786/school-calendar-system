<?php
$host = 'localhost';
$user = 'root';
$password = ''; // Your database password
$db = 'school_system';
$port = 3307; // Ensure port is included if it was previously 3307

$conn = new mysqli($host, $user, $password, $db, $port);

// Check database connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? ''; // Added null coalescing for safety
    $email = $_POST['email'] ?? ''; // Added null coalescing for safety
    $plain_text_password = $_POST['password'] ?? ''; // Get the plain-text password
    $role = $_POST['role'] ?? ''; // Added null coalescing for safety

    // --- Hash the password before storing ---
    $hashed_password = password_hash($plain_text_password, PASSWORD_DEFAULT);

    // Check if hashing was successful
    if ($hashed_password === false) {
        // Handle error if hashing fails (e.g., log it, show a generic error to the user)
        echo 'Error: Failed to process password.';
        exit();
    }

    // Prepare the SQL statement
    // IMPORTANT: Insert into 'password_hash' column, not 'password'
    $stmt = $conn->prepare("INSERT INTO users (email, password_hash, role, name) VALUES (?, ?, ?, ?)");

    // Check if statement preparation was successful
    if (!$stmt) {
        echo 'Error preparing statement: ' . $conn->error;
        exit();
    }

    // Bind parameters: 's' for string (email), 's' for string (hashed_password),
    // 's' for string (role), 's' for string (name)
    $stmt->bind_param("ssss", $email, $hashed_password, $role, $name);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to user-management.php after successful insertion
        header("Location: user-management.php");
        exit();
    } else {
        echo 'Error executing statement: ' . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>