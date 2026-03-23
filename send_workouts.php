<?php

include_once 'generate_qr.php';
include_once 'db_connection.php';
require 'email_sender.php';
include_once 'params.php';

if (!isset($_POST['mobile'])) {
    die(json_encode(['status' => 'error', 'message' => 'Missing mobile number']));
}

$mobile = $_POST['mobile'];
$clientEmail = $_POST['email'] ?? null;


$image_geo = '/img/1.jpg';
$image_eng = '/img/2.jpg';

$text_eng_raw = 'English setting (ID 2) not found';
$text_geo_raw = 'Georgian setting (ID 3) not found';

$sql_eng = "SELECT value FROM settings WHERE id = ?";
$stmt_eng = $conn->prepare($sql_eng);

if ($stmt_eng) {
    $id_eng = 2;
    $stmt_eng->bind_param("i", $id_eng); 
    $stmt_eng->execute();
    $result_eng = $stmt_eng->get_result();

    if ($result_eng->num_rows > 0) {
        $row_eng = $result_eng->fetch_assoc();
        $text_eng_raw = $row_eng['value']; 
    }
    $stmt_eng->close();
} else {
    error_log("Error preparing statement for English setting: " . $conn->error);
    $text_eng_raw = 'Error fetching English setting';
}


$sql_geo = "SELECT value FROM settings WHERE id = ?";
$stmt_geo = $conn->prepare($sql_geo);

if ($stmt_geo) {
    $id_geo = 3;
    $stmt_geo->bind_param("i", $id_geo); //
    $stmt_geo->execute();
    $result_geo = $stmt_geo->get_result();

    if ($result_geo->num_rows > 0) {
        $row_geo = $result_geo->fetch_assoc();
        $text_geo_raw = $row_geo['value'];
    }
    $stmt_geo->close(); 
} else {
    error_log("Error preparing statement for Georgian setting: " . $conn->error);
    $text_geo_raw = 'Error fetching Georgian setting';
}

$text_eng_safe = htmlspecialchars($text_eng_raw);
$text_geo_safe = htmlspecialchars($text_geo_raw);

$message_geo = $text_geo_safe . ' - ' . $image_geo;
$message_eng = $text_eng_safe . ' - ' . $image_eng;


$message = $message_geo . "\n" . $message_eng;

$apikey = 'SYNERGY_SMS_API_KEY';

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
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$responseData = json_decode($response, true);

// Build email body HTML
$emailBody = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Synergy Gym Group Workouts</title>
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
                <p style="background-color: rgba(255, 255, 255, 0.60); color: #333333; padding: 15px 20px; font-size: 20px;"> 
                    ' . $text_geo_safe . '
                </p>
               <img src="' . $image_geo . '" alt="group workouts" style="max-width: 300px; width: 100%; height: auto; display: block; border-radius: 10px; margin-bottom: 15px;">
                <p style="background-color: rgba(255, 255, 255, 0.60); color: #333333; padding: 15px 20px; font-size: 20px;"> 
                    ' . $text_eng_safe . '
                </p>
                <img src="' . $image_eng . '" alt="group workouts" style="max-width: 300px; width: 100%; height: auto; display: block; border-radius: 10px; margin-bottom: 15px;">
                <br>
                <table role="presentation" cellspacing="0" cellpadding="0" border="0">
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

// Send email using unified API sender (with SMTP fallback)
$emailSent = false;
try {
    $result = sendUnifiedEmail($clientEmail, 'Group Workouts - synergy-gym.ge', $emailBody, $user);
    $emailSent = $result['success'];
    
    if ($emailSent) {
        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO emails (email, date, content) VALUES (?, NOW(), ?)");
        $content = 'group workouts';
        $stmt->bind_param("ss", $clientEmail, $content);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("Email sending failed: " . ($result['error'] ?? 'Unknown error'));
    }
} catch (Exception $e) {
    error_log("Email sending exception: " . $e->getMessage());
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
