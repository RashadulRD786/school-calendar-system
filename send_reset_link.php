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

// Connect to DB
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $token = bin2hex(random_bytes(50));
    $expires = date("U") + 1800;

    // Save token and expiry to DB
    $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $expires, $email);
    $stmt->execute();

    // Send email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'sksaujana.calendar@gmail.com';
        $mail->Password = 'sksajuna2025'; // Use Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('sksaujana.calendar@gmail.com', 'SK Saujana Calendar System');
        $mail->addAddress($email);

        $reset_link = "http://localhost/school-calendar-system/reset_password.html?token=$token";
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "Click the link to reset your password: $reset_link";

        $mail->send();
        echo "✅ Reset link sent to your email.";
    } catch (Exception $e) {
        echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "❌ Email not found.";
}

$conn->close();
?>
