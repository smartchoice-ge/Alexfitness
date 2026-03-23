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

// Validate request method
if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Include MySQL database connection
include_once '../db_connection.php'; 

// Check database connection
if (!isset($conn) || $conn->connect_error) {
    $mysql_error = isset($conn) ? $conn->connect_error : 'MySQL connection object not found in db_connection.php.';
    error_log("MySQL Connection Error in fetch_detailed_sms_logs.php: " . $mysql_error);
    echo json_encode(['status' => 'error', 'message' => 'Database connection error. Cannot fetch logs.']);
    exit;
}

$period = isset($_POST['period']) ? trim($_POST['period']) : null;
$logs = [];
$base_sql_select = "SELECT psl.client_id_num, psl.record_date, cd.full_name, cd.mobile_number 
                    FROM active_promo_sms_logs psl
                    LEFT JOIN client_details cd ON psl.client_id_num = cd.id_number";
$sql_where = "";
$sql_order = "ORDER BY psl.record_date DESC";

// Construct SQL query based on the requested period
switch ($period) {
    case 'today':
        $sql_where = "WHERE DATE(psl.record_date) = CURDATE()";
        break;
    case 'this_week':
        $sql_where = "WHERE YEARWEEK(psl.record_date, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'this_month':
        $sql_where = "WHERE MONTH(psl.record_date) = MONTH(CURDATE()) AND YEAR(psl.record_date) = YEAR(CURDATE())";
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid period specified.']);
        if (isset($conn) && !$conn->connect_error) {
            $conn->close();
        }
        exit;
}

$sql = $base_sql_select . " " . $sql_where . " " . $sql_order;

// Execute the query
$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ensure full_name and mobile_number are set, default to 'N/A' if null from LEFT JOIN
        $row['full_name'] = $row['full_name'] ?? 'N/A';
        $row['mobile_number'] = $row['mobile_number'] ?? 'N/A';
        $logs[] = $row;
    }
    $result->free(); 
    echo json_encode(['status' => 'success', 'logs' => $logs]);
} else {
    error_log("Error fetching detailed SMS logs for period '{$period}': " . $conn->error . " SQL: " . $sql);
    echo json_encode(['status' => 'error', 'message' => 'Error fetching log details from database.']);
}

if (isset($conn) && !$conn->connect_error) {
    $conn->close();
}
?>
