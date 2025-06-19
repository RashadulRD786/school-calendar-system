<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

function sendEventNotification($eventData) {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'school_system', 3307);
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
    $eventName = htmlspecialchars($eventData['name']); // Sanitize output
    $eventLocation = htmlspecialchars($eventData['location']);
    $personInCharge = htmlspecialchars($eventData['person_in_charge']);

    // --- START OF DATE FORMATTING CHANGE ---
    $rawEventDate = $eventData['date']; // e.g., "2025-05-13"
    $formattedEventDate = '';
    try {
        $dateObj = new DateTime($rawEventDate);
        $formattedEventDate = $dateObj->format('d-m-Y'); // Format to DD-MM-YYYY
    } catch (Exception $e) {
        // Fallback to raw date if formatting fails
        error_log("Failed to format date: " . $e->getMessage());
        $formattedEventDate = $rawEventDate;
    }
    // --- END OF DATE FORMATTING CHANGE ---

    // --- START OF TIME FORMATTING CHANGE ---
    $rawEventTime = $eventData['time']; // e.g., "09:45"
    $formattedEventTime = '';
    try {
        $timeObj = new DateTime($rawEventTime);
        $formattedEventTime = $timeObj->format('h:i A'); // Format to 12-hour with AM/PM
    } catch (Exception $e) {
        // Fallback to raw time if formatting fails
        error_log("Failed to format time: " . $e->getMessage());
        $formattedEventTime = $rawEventTime;
    }
    // --- END OF TIME FORMATTING CHANGE ---

    $subject = "New Event Added: $eventName";

    // --- EMAIL BODY HTML ---
    $body = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>New Event Notification</title>
        <style>
            /* Basic Reset & Body Styles */
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                -webkit-text-size-adjust: none; /* Prevent iOS text scaling */
                width: 100% !important; /* Force full width on mobile */
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            td {
                padding: 0;
            }
            a {
                text-decoration: none;
                color: #007bff; /* Default link color */
            }
            /* Media Queries for Responsiveness */
            @media screen and (max-width: 600px) {
                .content-table {
                    width: 100% !important;
                }
                .padding-box {
                    padding: 15px !important;
                }
                .header-text h1 {
                    font-size: 22px !important;
                }
                .event-detail-row td {
                    padding: 8px 0 !important;
                }
            }
        </style>
    </head>
    <body>
        <table role='presentation' width='100%' cellspacing='0' cellpadding='0' border='0' style='background-color: #f4f4f4;'>
            <tr>
                <td align='center' style='padding: 20px 0;'>
                    <table role='presentation' class='content-table' width='500' cellspacing='0' cellpadding='0' border='0' style='background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.05);'>
                        <tr>
                            <td class='padding-box' style='padding: 30px;'>
                                <table role='presentation' width='100%' cellspacing='0' cellpadding='0' border='0'>
                                    <tr>
                                        <td style='padding-bottom: 20px; text-align: center; border-bottom: 1px solid #eeeeee;'>
                                            <h1 class='header-text' style='font-size: 26px; color: #333333; margin: 0;'>New Event Scheduled</h1>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding-top: 25px; padding-bottom: 25px;'>
                                            <p style='font-size: 16px; line-height: 1.5; color: #555555; margin-bottom: 20px;'>Dear User,</p>
                                            <p style='font-size: 16px; line-height: 1.5; color: #555555; margin-bottom: 20px;'>A new event has been added to the school calendar. Please find the details below:</p>

                                            <table role='presentation' width='100%' cellspacing='0' cellpadding='0' border='0' style='font-size: 15px; color: #444444;'>
                                                <tr class='event-detail-row'>
                                                    <td width='30%' style='padding: 10px 0; font-weight: bold;'>Event Name:</td>
                                                    <td style='padding: 10px 0;'>$eventName</td>
                                                </tr>
                                                <tr class='event-detail-row'>
                                                    <td style='padding: 10px 0; font-weight: bold;'>Date:</td>
                                                    <td style='padding: 10px 0;'>$formattedEventDate</td> </tr>
                                                <tr class='event-detail-row'>
                                                    <td style='padding: 10px 0; font-weight: bold;'>Time:</td>
                                                    <td style='padding: 10px 0;'>$formattedEventTime</td>
                                                </tr>
                                                <tr class='event-detail-row'>
                                                    <td style='padding: 10px 0; font-weight: bold;'>Location:</td>
                                                    <td style='padding: 10px 0;'>$eventLocation</td>
                                                </tr>
                                                <tr class='event-detail-row'>
                                                    <td style='padding: 10px 0; font-weight: bold;'>Person in Charge:</td>
                                                    <td style='padding: 10px 0;'>$personInCharge</td>
                                                </tr>
                                            </table>

                                            <p style='font-size: 16px; line-height: 1.5; color: #555555; margin-top: 25px; text-align: center;'>
                                                <a href='http://localhost/school-calendar-system/index.html' style='display: inline-block; padding: 12px 25px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                                                    Login to View More Details
                                                </a>
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style='padding-top: 20px; text-align: center; border-top: 1px solid #eeeeee; font-size: 13px; color: #aaaaaa;'>
                                            <p>&copy; " . date("Y") . " SK Saujana Event Calendar. All rights reserved.</p>
                                            <p>This is an automated notification, please do not reply.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>
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
            $mail->setFrom('schoolsksajuna79@gmail.com', 'SK Saujana Calendar');
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