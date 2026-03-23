<?php

include_once 'mssql_connection.php';
include_once 'db_connection.php';
require 'email_sender.php';
include_once 'params.php';

if (!isset($_POST['mobile']) || !isset($_POST['idNumber'])) {
    die(json_encode(['status' => 'error', 'message' => 'Missing mobile number or ID number'])); // JSON error
}

$mobile = $_POST['mobile'];
$idNumber = $_POST['idNumber'];

// Database connections
if (!$mssqlconn || !$conn) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.'])); // JSON error
}

// Client existence check
$checkSql = "SELECT Email FROM Clients WHERE IdNumber = ?"; // Get the email address too
$checkStmt = sqlsrv_query($mssqlconn, $checkSql, array($idNumber));

if ($checkStmt === false) {
    die(json_encode(['status' => 'error', 'message' => "SQL Error: " . print_r(sqlsrv_errors(), true)])); // JSON error
}

if (!sqlsrv_has_rows($checkStmt)) {
    die(json_encode(['status' => 'error', 'message' => "Error: Client not found."])); // JSON error
}
//////////
$clientData = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC); // Fetch client data
$clientEmail = $clientData['Email'];

// SMS settings retrieval
$smsSql = "SELECT value FROM settings WHERE id = 1";
$smsSqlRes = $conn->query($smsSql);

if ($smsSqlRes->num_rows > 0) {
    $row = $smsSqlRes->fetch_assoc();
    $settingValue = $row['value'];
} else {
    $settingValue = 'Setting not found';
}

// Updated text and message
$text = "გიგზავნით პროგრამის ლინკს. დასაკლები /wp1.pdf მოსამატებლი /wp2.pdf \n\nYou can find the workout program at this link Lose Weight /wp1.pdf, Gain Weight /wp2.pdf";
$message = $text;

$apikey = 'SYNERGY_SMS_API_KEY'; // Your API key

// SMS sending (sender.ge)
$url = "https://sender.ge/api/send.php";
$fields = [
    'apikey' => $apikey,
    'smsno' => 2,
    'destination' => $mobile,
    'content' => $message
];
$fields_string = http_build_query($fields);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Be careful with this in production

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response, true);

// Email sending (PHPMailer)
$mail = new PHPMailer(true);
$emailSent = false; // Initialize

try {
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $user; // Your Zoho email
    $mail->Password = $pass; // **Zoho App Password!**
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Uncomment for debugging

    $mail->setFrom($user, 'SYNERGY_DOMAIN');
    $mail->addAddress($clientEmail); // Use the client's email
    $mail->Subject = 'Workout Plans';
    $mail->isHTML(true);
    $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Synergy Gym QR Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; text-align: center;">

    <!-- Main Wrapper -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" 
        style="width: 100%; min-height: 600px; background: url(\'/img/bg.png\') no-repeat center center; background-size: cover;">
        
        ';
        
        ob_start();
        include 'auto_emails/email_templates/header.php';
        $mail->Body .= ob_get_clean();
        
        $mail->Body .= '

        <!-- Welcome Message -->
        <tr>
            <td align="center" style="padding: 10px; background: rgba(0, 0, 0, 0.6);">
                <div style="max-width: 90%; margin: auto; padding: 10px;">
                    <p style="color: #FFD700; font-size: 16px; line-height: 1.5;">
                    <b>
                        Hi there! Welcome to our community. We’re here to support and motivate you to reach your goals.
                    </b>
                    </p>
                </div>
            </td>
        </tr>

        <!-- QR Code Section -->
        <tr>
            <td align="center" style="padding: 2px;">
                <br>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center" style="padding-top: 10px; padding-bottom: 20px;">
                            <a href="/wp2.pdf" target="_blank">
                                <img src="/img/gain.png" alt="Gain" style="width: 180px; height: auto;">
                            </a>
                        </td>
                        <td align="center" style="padding-top: 10px; padding-bottom: 20px; padding-left: 20px;">
                            <a href="/wp1.pdf" target="_blank">
                                <img src="/img/loss.png" alt="Loss" style="width: 180px; height: auto;">
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <!-- Second Message -->
        <tr>
            <td align="center" style="padding: 10px; background: rgba(0, 0, 0, 0.8);">
                <div style="max-width: 90%; margin: auto; padding: 10px;">
                    <p style="color: #FFD700; font-size: 16px; line-height: 1.5;">
                        <b> 
                        Whether your fitness goal is - weight loss, muscle gain or feeling alive - it’s up to you. Every step forward, every choice is a win. You don’t need perfection - just persistence. This isn’t just goal; it’s a promise to yourself. Keep it. You’re worth it. Start now - Let’s make it happen!
                        </b>
                    </p>
                </div>
            </td>
        </tr>';
        
        ob_start();
        include 'auto_emails/email_templates/footer.php';
        $mail->Body .= ob_get_clean(); 
        
        $mail->Body .= '</table>
</body>
</html>';

    $mail->send();
    $emailSent = true;
    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
    $content = 'workout program';
    $stmt->bind_param("ss", $clientEmail, $content);
    $stmt->execute();
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Email sending failed: " . $mail->ErrorInfo);
}

// JSON response
$responseArray = [];

if ($httpCode == 401) {
    $responseArray['status'] = 'error';
    $responseArray['message'] = "Unauthorized: API key is invalid or expired.";
} elseif ($httpCode == 200) {
    if ($responseData && isset($responseData['data'][0]['statusId'])) {
        $statusId = $responseData['data'][0]['statusId'];
        $messageId = $responseData['data'][0]['messageId'];

        if ($statusId == 1) {
            $responseArray['status'] = 'success';
            $responseArray['message'] = "SMS sent successfully. Message ID: " . $messageId;
            if ($emailSent) {
                $responseArray['message'] .= " and Email sent successfully.";
            } else {
                $responseArray['message'] .= " but Email sending failed.";
            }
        } else {
            $responseArray['status'] = 'error';
            $responseArray['message'] = "SMS not sent. Status ID: " . $statusId;
        }
    } else {
        $responseArray['status'] = 'error';
        $responseArray['message'] = "Unexpected API response format.";
    }
} elseif ($httpCode == 403) {
    // Handle 403 Forbidden specifically - usually means incorrect phone number
    $responseArray['status'] = 'error';
    $responseArray['message'] = "ტელეფონის ნომერი შეამოწმეთ, ნომერი 995ით ან +995ით ხომ ხომ არ იწყება, თუ კი წაუშალეთ და მხოლოდ ნომერი დატოვეთ, 558168916 ეს არი სწორი ფორმატი";
} else {
    $responseArray['status'] = 'error';
    $responseArray['message'] = "Error ($httpCode): " . $response;
}

header('Content-Type: application/json');
echo json_encode($responseArray);

?>