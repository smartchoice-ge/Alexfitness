<?php
// unipay_payment.php
require_once 'mssql_connection.php';
require_once 'mssql_packages_payments_helper.php';
date_default_timezone_set('Asia/Tbilisi');
header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$amount = isset($data['amount']) ? $data['amount'] : null;
$status = isset($data['status']) ? $data['status'] : 'Pending';
$client_mobile_number = isset($data['client_mobile_number']) ? $data['client_mobile_number'] : null;
$transaction_id = isset($data['transaction_id']) ? $data['transaction_id'] : null;
$time = date('Y-m-d H:i:s');

if (!$amount || !$client_mobile_number || !$transaction_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Insert payment record to MSSQL PaymentsWebsite
$paymentId = insertPaymentWebsite([
    'amount' => $amount,
    'status' => $status,
    'client_mobile_number' => $client_mobile_number,
    'transaction_id' => $transaction_id,
    'time' => $time
]);

if ($paymentId) {
    echo json_encode(['success' => true, 'payment_id' => $paymentId]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
