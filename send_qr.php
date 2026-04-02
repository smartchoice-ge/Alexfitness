<?php

include_once 'generate_qr.php';
include_once 'mssql_connection.php';
include_once 'db_connection.php';
require 'email_sender.php';
include_once 'params.php';

if (!isset($_POST['mobile']) || !isset($_POST['idNumber'])) {
    die(json_encode(['status' => 'error', 'message' => 'Missing mobile number or ID number'])); // JSON error
}

$mobile = $_POST['mobile'];
$idNumber = $_POST['idNumber'];
$clientEmail = $_POST['email'] ?? null;

// Clean mobile number for SMS API (remove country codes but keep original for QR)
$originalMobile = $mobile;
$cleanMobile = $mobile;

// Remove +995 prefix if present for SMS
if (str_starts_with($cleanMobile, '+995')) {
    $cleanMobile = substr($cleanMobile, 4);
}
// Remove 995 prefix if present for SMS
elseif (str_starts_with($cleanMobile, '995')) {
    $cleanMobile = substr($cleanMobile, 3);
}

// Log the mobile number processing
error_log("QR/SMS Processing - Original: {$originalMobile}, Cleaned for SMS: {$cleanMobile}");

// // Database connections
// if (!$mssqlconn || !$conn) {
//     die(json_encode(['status' => 'error', 'message' => 'Database connection failed.'])); // JSON error
// }

// // Client existence check
// $checkSql = "SELECT Email FROM Clients WHERE IdNumber = ?";
// $checkStmt = sqlsrv_query($mssqlconn, $checkSql, array($idNumber));

// if ($checkStmt === false) {
//     die(json_encode(['status' => 'error', 'message' => "SQL Error: " . print_r(sqlsrv_errors(), true)])); // JSON error
// }

// if (!sqlsrv_has_rows($checkStmt)) {
//     die(json_encode(['status' => 'error', 'message' => "Error: Client not found."])); // JSON error
// }

// $clientData = sqlsrv_fetch_array($checkStmt, SQLSRV_FETCH_ASSOC); // Fetch client data
// $clientEmail = $clientData['Email'];

// SMS settings retrieval
$smsSql = "SELECT value FROM settings WHERE id = 1";
$smsSqlRes = $conn->query($smsSql);

if ($smsSqlRes && $smsSqlRes->num_rows > 0) {
    $row = $smsSqlRes->fetch_assoc();
    $settingValue = $row['value'];
} else {
    $settingValue = '';
}

// Fetch secondary SMS text (settings id = 8) -> $text2
$smsSql2 = "SELECT value FROM settings WHERE id = 8";
$smsSqlRes2 = $conn->query($smsSql2);
if ($smsSqlRes2 && $smsSqlRes2->num_rows > 0) {
    $row2 = $smsSqlRes2->fetch_assoc();
    $settingValue2 = $row2['value'];
} else {
    $settingValue2 = '';
}


$idNumber = trim($idNumber);
$qrLink = generateQR($cleanMobile);
$text = htmlspecialchars($settingValue);
$text2 = htmlspecialchars($settingValue2);
$message = trim($text . ' ' . $qrLink . ' ' . $text2);
$apikey = '0f132d23f162ca06a769128a5e866cf1';

// SMS sending (sender.ge) - use cleaned mobile number
$url = "https://sender.ge/api/send.php";
$fields = [
    'apikey' => $apikey,
    'smsno' => 2,
    'destination' => $cleanMobile, // Use cleaned mobile for SMS API
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

// Log SMS response for debugging
error_log("SMS API Response - HTTP Code: {$httpCode}, Response: " . $response);


// Email sending (Unified Email API)
$emailSent = false; // Initialize

$emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Luka Qaliashvili, ID: 0172409681 QR Code</title>
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
        <!-- Welcome Message -->
        <tr>
            <td align="center" style="padding: 10px; background: rgba(0, 0, 0, 0.7);">
                <div style="max-width: 90%; margin: auto; padding: 10px;">
                    <p style="color: #FFD700; font-size: 16px; line-height: 1.5;">
                    <b>
                        Hi there! Welcome to our community. We\'re here to support and motivate you to reach your goals.
                    </b>
                    </p>
                </div>
            </td>
        </tr>

        <!-- QR Code Section -->
        <tr>
            <td align="center" style="padding: 20px;">
                <img src="' . $qrLink . '" alt="Scan Me QR Code" style="width: 180px; height: 180px;">
                <br>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center" style="background: black; color: white; padding: 8px 16px; border-radius: 5px;">
                            Scan me
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-top: 10px;">
                            <a href="https://synergy-gym.ge" style="color: #FFD700; text-decoration: none; font-size: 16px; font-weight: bold;">
                                WWW.synergy-gym.ge
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>';

ob_start();
include 'auto_emails/email_templates/footer.php';
$emailBody .= ob_get_clean();

$emailBody .= '</table>

</body>
</html>';

$result = sendUnifiedEmail($clientEmail, 'QR-code for gym entrance', $emailBody, $user);

if ($result['success']) {
    $emailSent = true;
    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
    $content = 'qr code';
    $stmt->bind_param("ss", $clientEmail, $content);
    $stmt->execute();
    $stmt->close();
} else {
    error_log("Email sending failed: " . $result['error']);
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