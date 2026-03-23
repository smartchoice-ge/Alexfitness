<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);
header('Content-Type: application/json');

// Basic Authentication/Authorization Check
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please login.']);
    exit;
}

if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Include MySQL database connection
// This file should establish a mysqli connection object, typically named $conn
include_once '../db_connection.php';

if (!isset($conn) || $conn->connect_error) {
    $mysql_error = isset($conn) ? $conn->connect_error : 'MySQL connection object not found.';
    error_log("MySQL Connection Error in fetch_sms_logs.php: " . $mysql_error);
    echo json_encode(['status' => 'error', 'message' => 'Database connection error. Cannot fetch logs.']);
    exit;
}

$clientIdNum = isset($_POST['clientIdNum']) ? trim($_POST['clientIdNum']) : null;

if (empty($clientIdNum)) {
    echo json_encode(['status' => 'error', 'message' => 'Client ID Number is required.']);
    exit;
}

$logs = [];
$sql = "SELECT id, client_id_num, record_date FROM active_promo_sms_logs WHERE client_id_num = ? ORDER BY record_date DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $clientIdNum);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            // Format record_date if needed, or send as is if frontend handles formatting
            // Example: $row['record_date_formatted'] = date("Y-m-d H:i:s", strtotime($row['record_date']));
            $logs[] = $row;
        }
        $stmt->close();
        echo json_encode(['status' => 'success', 'logs' => $logs]);
    } else {
        error_log("Failed to execute statement for fetching SMS logs for client_id_num {$clientIdNum}: " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Error fetching logs: ' . $stmt->error]);
    }
} else {
    error_log("Failed to prepare statement for fetching SMS logs: " . $conn->error);
    echo json_encode(['status' => 'error', 'message' => 'Database prepare error for fetching logs: ' . $conn->error]);
}

$conn->close();
?>
