<?php
$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$port = 3307; // Ensure port is included if it was previously 3307
$conn = new mysqli($host, $user, $pass, $db, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$token = $_POST['token'] ?? $_GET['token'] ?? '';
$email = $_POST['email'] ?? $_GET['email'] ?? '';
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($newPass !== $confirm) {
        $errorMessage = "❌ Passwords do not match.";
    } else {
        // First, verify the token and its expiration
        $stmt = $conn->prepare("SELECT reset_expires FROM users WHERE email = ? AND reset_token = ?");
        if (!$stmt) {
            $errorMessage = "❌ Database query preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $email, $token);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close(); // Close this statement after use

            if ($result && $result['reset_expires'] >= time()) {
                // Token is valid and not expired, proceed to hash the new password
                $hashed_password = password_hash($newPass, PASSWORD_DEFAULT);

                if ($hashed_password === false) {
                    $errorMessage = "❌ Failed to hash password. Please try again.";
                } else {
                    // Update the user's password_hash column with the new hashed password
                    // Also clear the reset token and its expiration
                    // IMPORTANT: Ensure you have a 'password_hash' column in your 'users' table (VARCHAR 255)
                    $update_stmt = $conn->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
                    if (!$update_stmt) {
                        $errorMessage = "❌ Database update preparation failed: " . $conn->error;
                    } else {
                        $update_stmt->bind_param("ss", $hashed_password, $email);
                        $update_stmt->execute();

                        if ($update_stmt->affected_rows > 0) {
                            $successMessage = "✅ Password reset successful. <a href='index.html'>Login here</a>.";
                        } else {
                            $errorMessage = "❌ Failed to update password. Please try again.";
                        }
                        $update_stmt->close(); // Close this statement
                    }
                }
            } else {
                $errorMessage = "❌ Invalid or expired token.";
            }
        }
    }
}
$conn->close(); // Close the main connection
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-image: url('your-blurred-bg.jpg'); /* Your background image */
            background-size: cover;
            background-position: center;
            backdrop-filter: blur(5px);
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            width: 320px;
            text-align: center;
        }

        .login-box img {
            width: 100px;
            margin-bottom: 20px;
        }

        .login-box h2 {
            margin-bottom: 20px;
        }

        .login-box input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-box button {
            width: 100%;
            padding: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .login-box button:hover {
            background-color: #d32f2f;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border-radius: 5px;
        }

        .success {
            background-color: #e6f4ea;
            color: green;
            border: 1px solid green;
        }

        .error {
            background-color: #ffe6e6;
            color: red;
            border: 1px solid red;
        }
    </style>
</head>
<body>

<div class="login-box">
    <img src="https://i.pinimg.com/564x/31/50/bf/3150bf915dad0ce70b152fae9f13cd0f.jpg" alt="Logo" />

    <?php if (!$successMessage && !$errorMessage): ?>
        <h2>Set New Password</h2>
    <?php endif; ?>

    <?php if ($successMessage): ?>
        <div class="message success"><?= $successMessage ?></div>
    <?php elseif ($errorMessage): ?>
        <div class="message error"><?= $errorMessage ?></div>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

            <input type="password" name="new_password" placeholder="New password" required>
            <input type="password" name="confirm_password" placeholder="Confirm password" required>
            <button type="submit">Reset Password</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>