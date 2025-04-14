<?php
// Database connection
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';

$conn = new mysqli("localhost", "root", "", "school_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['Email'];
    $newPassword = $_POST['Npsw'];
    $confirmPassword = $_POST['Cpsw'];

    // Sanitize input
    $email = $conn->real_escape_string($email);

    // 1. Check if user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // 2. Passwords must match
        if ($newPassword !== $confirmPassword) {
            echo "❌ Passwords do not match.";
            exit();
        }

        // 3. Hash the password
        

        // 4. Update password in DB
        $update = "UPDATE users SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("ss", $hashedPassword, $email);

        if ($stmt->execute()) {
            // 5. Redirect to success page
            header("Location: password-reset-success.html");
            exit();
        } else {
            echo "❌ Failed to update password.";
        }
    } else {
        echo "❌ Email not found.";
    }
}
?>
