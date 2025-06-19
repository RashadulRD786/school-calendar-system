<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

$host = 'localhost';
$db = 'school_system';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db,3307);
if ($conn->connect_error) die("DB error: " . $conn->connect_error);

$email = $_POST['email'];

// Check if email exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Generate token and expiry
    $token = bin2hex(random_bytes(32));
    $expires = date("U") + 1800; // 30 minutes

    // Store in DB
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expires, $email);
    $stmt->execute();

    // Build link
    $reset_link = "http://localhost/school-calendar-system/reset_password.php?token=$token&email=" . urlencode($email);

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username = 'schoolsksajuna79@gmail.com';
        $mail->Password = 'lubl wjba ayew cpip';   // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('schoolsksajuna79@gmail.com', 'SK Saujana System');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Click the link below to reset your password:<br><a href='$reset_link'>$reset_link</a><br>This link will expire in 30 minutes.";

        $mail->send();
        
    } catch (Exception $e) {
        echo "❌ Email failed: " . $mail->ErrorInfo;
    }
} else {
    echo "❌ Email not found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Reset Link Sent</title>
  <link rel="stylesheet" href="style.css"> <!-- reuse login page CSS -->
  <style>
    .success-message {
      color: green;
      font-weight: bold;
      margin-top: 20px;
    }
  </style>
</head>
<body>

<div class="login-box">
  <img src="https://i.pinimg.com/564x/31/50/bf/3150bf915dad0ce70b152fae9f13cd0f.jpg" alt="School Logo">
  <p class="success-message">✅ Reset link sent. Please check your email.</p>

  
</div>

</body>
</html>

