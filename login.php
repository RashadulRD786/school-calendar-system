<?php
// Start a session to store user data like their role and ID
session_start();

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = ''; // Your database password
$port = 3307; // Your MySQL port

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from the form
$email = $_POST['email'] ?? ''; // Use null coalescing operator for safety
$password = $_POST['password'] ?? '';

// Basic input validation
if (empty($email) || empty($password)) {
    // It's generally better to redirect back to login with a generic error
    echo "❌ Please enter both email and password.";
    exit();
}

// Look for the user, fetching their password and role
// IMPORTANT: Ensure you have a 'role' column in your 'users' table.
$sql = "SELECT id, email, password, role FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo "❌ Database prepare statement failed: " . $conn->error;
    exit();
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // **WARNING: Comparing plain text passwords is INSECURE!**
    // This is done as per your request, but is NOT recommended for production.
    if ($password === $user['password']) {
        // Password is correct, set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role']; // Store the user's role

        // Redirect based on role
        if ($user['role'] === 'Admin') {
            header("Location: admin-dashboard1.html");
            exit();
        } elseif ($user['role'] === 'Teacher') {
            header("Location: teacher-dashboard1.html"); // Redirect teachers to their specific dashboard
            exit();
        } else {
            // Handle unknown roles or redirect to a default page
            echo "❌ Your role is not recognized. Please contact support.";
            // Optionally, destroy the session if the role is unrecognized
            session_unset();
            session_destroy();
            exit();
        }
    } else {
        // For security, provide a generic error message
        echo "❌ Invalid email or password.";
        exit();
    }
} else {
    // For security, provide a generic error message
    echo "❌ Invalid email or password.";
    exit();
}

$conn->close();
?>