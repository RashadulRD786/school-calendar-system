<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendEventNotification($eventData) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'school_system', 3306);
    if ($conn->connect_error) {
        error_log("Database connection failed: " . $conn->connect_error);
        return;
    }

    // Get all user emails
    $result = $conn->query("SELECT email FROM users");
    if (!$result) {
        error_log("Query failed: " . $conn->error);
        return;
    }

    // Prepare event info
    $eventName = $eventData['name'];
    $eventDate = $eventData['date'];
    $eventTime = $eventData['time'];
    $eventLocation = $eventData['location'];
    $personInCharge = $eventData['person_in_charge'];

    $subject = "New Event Added: $eventName";
    $body = "
        <h2>A new event has been scheduled</h2>
        <p><strong>Event Name:</strong> $eventName</p>
        <p><strong>Date:</strong> $eventDate</p>
        <p><strong>Time:</strong> $eventTime</p>
        <p><strong>Location:</strong> $eventLocation</p>
        <p><strong>Person in Charge:</strong> $personInCharge</p>
        <p><a href='http://localhost/school-calendar-system/index.html'>Login to view more details</a></p>
    ";

    // Loop through users and send email
    while ($row = $result->fetch_assoc()) {
        $to = $row['email'];

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'schoolsksajuna79@gmail.com'; // Your system email
            $mail->Password = 'lubl wjba ayew cpip'; // Use an app-specific password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('hadiiisyafiqqq@gmail.com', 'SK Saujana Calendar');
            $mail->addAddress($to);


            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
        } catch (Exception $e) {
            error_log("Email could not be sent to $to. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    $conn->close();
}
?>