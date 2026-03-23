<?php
require 'vendor/autoload.php';
require 'db_connection.php';
require 'email_sender.php';
include_once 'params.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['email']) || !isset($_POST['code'])) {
        die(json_encode(['status' => 'error', 'message' => 'Missing email or code']));
    }

    $email = $_POST['email'];
    $code = $_POST['code'];

    try {
        // Build email body
        $emailBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Synergy Gym</title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; text-align: center;">
        
            <!-- Main Wrapper -->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" 
                style="width: 100%; min-height: 600px; background: url(\'/img/bg.png\') no-repeat center center; background-size: cover;">
        
        ';
        
        ob_start();
        include 'auto_emails/email_templates/header.php';
        $emailBody .= ob_get_clean();
        
        $emailBody .= '
                <!-- Verification Code Section -->
                <tr>
                    <td align="center" style="padding-left: 20px; padding-right: 20px; padding-top: 40px; padding-bottom: 40px; background: rgba(0, 0, 0, 0.6);">
                        <p style="color: white; font-size: 18px; line-height: 1.5; max-width: 90%; margin: auto;">
                            Your verification code is: <strong style="font-size: 30px;">' . $code . '</strong>
                        </p>
                    </td>
                </tr>
        ';
        
        ob_start();
        include 'auto_emails/email_templates/footer.php';
        $emailBody .= ob_get_clean(); 
        
        $emailBody .= '</table>
        
        </body>
        </html>';
        
        // Send email using unified sender (API first, SMTP fallback)
        file_put_contents('email_debug.log', date('Y-m-d H:i:s') . " - Attempting to send email to $email\n", FILE_APPEND);
        $startTime = microtime(true);
        
        $result = sendUnifiedEmail($email, 'Verification Code - SYNERGY_DOMAIN', $emailBody);
        
        $duration = microtime(true) - $startTime;
        file_put_contents('email_debug.log', date('Y-m-d H:i:s') . " - Result: " . json_encode($result) . " (Duration: {$duration}s)\n", FILE_APPEND);

        // Send Email
        if ($result['success']) {
            // Insert into the database
            $stmt = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
            $content = 'verification email';
            $stmt->bind_param("ss", $email, $content);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Email sent and saved successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Email sent but database insert failed.']);
            }

            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Email sending failed: ' . $result['error']]);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
}
?>