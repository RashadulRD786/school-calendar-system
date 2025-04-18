<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newPass !== $confirm) {
        die("❌ Passwords do not match.");
    }

    // Verify token
    $stmt = $conn->prepare("SELECT reset_expires FROM users WHERE email = ? AND reset_token = ?");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && $result['reset_expires'] >= time()) {
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
        $stmt->bind_param("ss", $newPass, $email); // Add hashing if needed
        $stmt->execute();

        echo "✅ Password reset successful. You can now <a href='index.html'>login</a>.";
    } else {
        echo "❌ Invalid or expired token.";
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
  <div class="logo">
    <img src="https://i.pinimg.com/564x/31/50/bf/3150bf915dad0ce70b152fae9f13cd0f.jpg" alt="School Logo">
  </div>
  <div class="form">
    <h2>Set New Password</h2>
    <form action="reset_password.php" method="POST">
      <input type="password" name="new_password" placeholder="New password" required><br>
      <input type="password" name="confirm_password" placeholder="Confirm password" required><br>
      <button type="submit">Reset Password</button>
    </form>
  </div>
</div>
</body>
</html>
