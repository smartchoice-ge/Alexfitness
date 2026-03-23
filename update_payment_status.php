<?php
// update_payment_status.php
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
$transaction_id = isset($data['transaction_id']) ? $data['transaction_id'] : null;
$status = isset($data['status']) ? $data['status'] : null;

if (!$transaction_id || !$status) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

// Update payment status in MSSQL PaymentsWebsite
$affectedRows = updatePaymentStatusByTransactionId($transaction_id, $status);

if ($affectedRows > 0) {
    echo json_encode(['success' => true, 'affected_rows' => $affectedRows]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'No payment found or database error']);
}
?>
