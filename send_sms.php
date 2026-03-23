<?php
if (!isset($_POST['mobile']) || !isset($_POST['code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$mobile = $_POST['mobile'];
$verificationCode = $_POST['code'];

$apikey = 'SYNERGY_SMS_API_KEY';
$url = "https://sender.ge/api/send.php";
$message = $verificationCode;

$fields = [
    'apikey'      => $apikey,
    'smsno'       => 2,
    'destination' => $mobile,
    'content'     => $message
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

if ($httpCode == 200 && isset($responseData['data'][0]['statusId']) && $responseData['data'][0]['statusId'] == 1) {
    echo json_encode(['status' => 'success', 'message' => 'SMS sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send SMS']);
}
?>
