<?php
session_start();

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = ''; // Your database password
$port = 3307; // Your MySQL port

// Set content type to JSON as per previous requirements
header('Content-Type: application/json');

// Initialize response array
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    $response['message'] = "Database connection failed: " . $conn->connect_error;
    echo json_encode($response);
    exit();
}

// Get data from the form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Basic input validation: Empty fields
if (empty($email) || empty($password)) {
    $response['message'] = "Please enter both email and password.";
    echo json_encode($response);
    exit();
}

// Basic input validation: Email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = "Invalid email format.";
    echo json_encode($response);
    exit();
}

// Look for the user, fetching their ID, email, old plain 'password', new 'password_hash', and 'role'.
// Ensure 'password_hash' column exists in your 'users' table.
$sql = "SELECT id, email, password, password_hash, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    $response['message'] = "Database query preparation failed: " . $conn->error;
    echo json_encode($response);
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $authenticated = false; // Flag to track successful authentication

    // --- Password Verification Logic ---

    // 1. **PRIMARY CHECK:** Check if a hashed password exists AND verify it
    if (!empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
        $authenticated = true;
    }
    // 2. **FALLBACK & MIGRATION:** If no hashed password (empty or NULL password_hash),
    //    then check against the old plain-text 'password' column.
    //    If it matches, hash it and update the database for future logins.
    else if (empty($user['password_hash']) && $password === $user['password']) {
        $authenticated = true;

        // Hash the password for this user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($hashed_password) { // Ensure hashing was successful
            // Update the user's record with the new hashed password
            $update_sql = "UPDATE users SET password_hash = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param("si", $hashed_password, $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                // Optionally: You could consider setting the old 'password' column to NULL here
                // after successful hashing, but it's safer to leave it for now until confident.
                // $conn->query("UPDATE users SET password = NULL WHERE id = " . $user['id']);
            } else {
                // Log this error: failed to update password hash
                error_log("Failed to update password hash for user ID: " . $user['id'] . " - " . $conn->error);
            }
        } else {
            // Hashing failed, treat as authentication failure (or log more severely)
            $authenticated = false;
            error_log("Password hashing failed for user ID: " . $user['id']);
        }
    }

    // --- End Password Verification Logic ---

    if ($authenticated) {
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role']; // Store the user's role

        $response['success'] = true; // Indicate success
        if ($user['role'] === 'Admin') {
            $response['redirect'] = 'admin-dashboard1.html';
        } elseif ($user['role'] === 'Teacher') {
            $response['redirect'] = 'teacher-dashboard1.html'; // Redirect teachers to their specific dashboard
        } else {
            // Handle unknown roles or redirect to a default page
            $response['message'] = "Your role is not recognized. Please contact support.";
            $response['success'] = false; // Even if password correct, role is not handled
            session_unset(); // Clear session if role is unrecognized
            session_destroy();
        }
    } else {
        // Authentication failed (either password incorrect or no match found)
        $response['message'] = "Invalid email or password.";
    }
} else {
    // Email not found in database
    $response['message'] = "Invalid email or password.";
}

$conn->close();
echo json_encode($response);
exit();
?>