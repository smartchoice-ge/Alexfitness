<?php
// Check if this is an export request
$isExport = isset($_GET['export']) && $_GET['export'] === 'excel';

if (!$isExport) {
    header('Content-Type: application/json');
}

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    error_log("Unauthorized access attempt to back_active_packages.php."); 
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access. Please login.']);
    exit;
}

include_once '../mssql_connection.php';
include_once '../mssql_packages_payments_helper.php';
include_once '../db_connection.php'; 

if (!$mssqlconn) {
    error_log("CRITICAL: MS SQL Database connection failed in back_active_packages.php.");
    echo json_encode(['status' => 'error', 'message' => 'MS SQL Database connection failed.']);
    exit;
}

$mysql_connection_active = true;
if (!isset($conn) || $conn->connect_error) {
    $mysql_connection_active = false;
    error_log("WARNING: MySQL connection failed in back_active_packages.php. Error: " . ($conn ? $conn->connect_error : 'Unavailable conn variable'));
}

$params = $_REQUEST;

// --- Fetch Unique Active Package Names for Dropdown Filter ---
$unique_active_package_names = [];
$sql_active_package_types = "SELECT Name FROM Packages WHERE Active = 1 ORDER BY Name ASC;";
$stmt_active_package_types = sqlsrv_query($mssqlconn, $sql_active_package_types);
if ($stmt_active_package_types) {
    while ($pkg_row = sqlsrv_fetch_array($stmt_active_package_types, SQLSRV_FETCH_ASSOC)) {
        if (isset($pkg_row['Name'])) {
            $unique_active_package_names[] = $pkg_row['Name']; 
        }
    }
    sqlsrv_free_stmt($stmt_active_package_types);
} else {
    error_log("SQLSRV Error (Fetching Active Package Types for filter) in back_active_packages.php: " . print_r(sqlsrv_errors(), true));
}

// --- Main Data Fetching Logic ---
$searchable_db_columns = ['c.FullName', 'c.IdNumber', 'c.Phone'];
$column_map_for_sorting = [
    0 => 'c.FullName', 
    1 => 'c.IdNumber', 
    2 => 'c.Phone',
    3 => 'LatestActive.PackageName', // ActivePackage
    4 => 'LatestActive.EndDate',     // ExpiryDate
    5 => '(CASE WHEN LatestActive.EndDate IS NOT NULL THEN DATEDIFF(day, GETDATE(), LatestActive.EndDate) ELSE NULL END)', // DaysUntilExpiry
    6 => 'c.RecordDate',              // Registration Date
    7 => '_LastSmsSentDateSQL'        // Last Sent SMS
];

$cte_sql = "
WITH RankedPackages AS (
    SELECT sp.ClientID, p.Name AS PackageName, sp.RecordDate AS PackageSoldRecordDate,
           sp.EndDate, sp.Expired,
           ROW_NUMBER() OVER (PARTITION BY sp.ClientID ORDER BY CASE WHEN sp.Expired = 0 THEN 0 ELSE 1 END, sp.RecordDate DESC) as rn_active_priority,
           ROW_NUMBER() OVER (PARTITION BY sp.ClientID ORDER BY sp.EndDate DESC) as rn_by_end_date_desc
    FROM SoldPackages sp JOIN Packages p ON sp.PackageID = p.ID
)";

$main_select_sql = "
SELECT c.ID as ClientMsSqlID, c.FullName, c.IdNumber, c.Phone, c.SendSms, c.RecordDate,
       LatestActive.PackageName AS _ActivePackageNameSQL, 
       LatestActive.EndDate AS _ActivePackageEndDateSQL,
       (CASE 
            WHEN LatestActive.EndDate IS NOT NULL 
            THEN DATEDIFF(day, GETDATE(), LatestActive.EndDate) 
            ELSE NULL 
        END) AS _DaysUntilExpirySQL,
       LatestOverall.PackageName AS _OverallPackageNameSQL 
";

$from_join_sql = "
FROM Clients c
LEFT JOIN RankedPackages LatestActive ON c.ID = LatestActive.ClientID AND LatestActive.rn_active_priority = 1 AND LatestActive.Expired = 0
LEFT JOIN RankedPackages LatestOverall ON c.ID = LatestOverall.ClientID AND LatestOverall.rn_by_end_date_desc = 1";

// Core condition to include ONLY clients with active packages
$conditions = " WHERE c.IsActive = 1 AND LatestActive.PackageName IS NOT NULL";
$query_params = []; 

// Apply Dropdown Active Package Filter
if (isset($params['selectedActivePackage']) && $params['selectedActivePackage'] !== '' && $params['selectedActivePackage'] !== 'All') {
    $selectedPkg = $params['selectedActivePackage'];
    $conditions .= " AND LatestActive.PackageName = ?";
    $query_params[] = $selectedPkg;
}
// Apply Column Input Search for Active Package
if (isset($params['activePackageColumnSearch']) && !empty(trim($params['activePackageColumnSearch']))) {
    $activePackageSearchTerm = "%" . trim($params['activePackageColumnSearch']) . "%";
    $conditions .= " AND LatestActive.PackageName LIKE ?";
    $query_params[] = $activePackageSearchTerm;
}
// Apply "Days Until Expiry" Range Filter
if (isset($params['daysUntilExpiryMax'])) {
    if (is_array($params['daysUntilExpiryMax'])) {
        // Handle range filter (min and max)
        $minDays = isset($params['daysUntilExpiryMax']['min']) && trim($params['daysUntilExpiryMax']['min']) !== '' ? intval($params['daysUntilExpiryMax']['min']) : null;
        $maxDays = isset($params['daysUntilExpiryMax']['max']) && trim($params['daysUntilExpiryMax']['max']) !== '' ? intval($params['daysUntilExpiryMax']['max']) : null;
        
        if ($minDays !== null && $maxDays !== null) {
            // Both min and max provided - filter between
            if ($minDays >= 0 && $maxDays >= 0) {
                $conditions .= " AND (LatestActive.EndDate IS NOT NULL AND DATEDIFF(day, GETDATE(), LatestActive.EndDate) BETWEEN ? AND ?)";
                $query_params[] = $minDays;
                $query_params[] = $maxDays;
            }
        } else if ($minDays !== null && $minDays >= 0) {
            // Only min provided - filter >= min
            $conditions .= " AND (LatestActive.EndDate IS NOT NULL AND DATEDIFF(day, GETDATE(), LatestActive.EndDate) >= ?)";
            $query_params[] = $minDays;
        } else if ($maxDays !== null && $maxDays >= 0) {
            // Only max provided - filter exact value (matches table behavior)
            $conditions .= " AND (LatestActive.EndDate IS NOT NULL AND DATEDIFF(day, GETDATE(), LatestActive.EndDate) = ?)";
            $query_params[] = $maxDays;
        }
    } else if (trim($params['daysUntilExpiryMax']) !== '' && is_numeric($params['daysUntilExpiryMax'])) {
        // Backward compatibility - single value treated as max
        $maxDays = intval($params['daysUntilExpiryMax']);
        if ($maxDays >= 0) { 
            $conditions .= " AND (LatestActive.EndDate IS NOT NULL AND DATEDIFF(day, GETDATE(), LatestActive.EndDate) <= ?)";
            $query_params[] = $maxDays;
        }
    }
}
// Global Search functionality
if (!empty($params['search']['value'])) {
    $search_value = $params['search']['value'];
    $search_term = "%" . $search_value . "%";
    $search_conditions_array = [];
    foreach ($searchable_db_columns as $col) {
        $search_conditions_array[] = "$col LIKE ?";
        $query_params[] = $search_term; 
    }
    if(count($search_conditions_array) > 0){
        $conditions .= " AND (" . implode(" OR ", $search_conditions_array) . ")";
    }
}

// Total records query (only clients with active packages)
$sql_total_records = $cte_sql . "SELECT COUNT(DISTINCT c.ID) as total " . $from_join_sql . " WHERE c.IsActive = 1 AND LatestActive.PackageName IS NOT NULL";
$stmt_total_records = sqlsrv_query($mssqlconn, $sql_total_records);
$total_records = 0;
if ($stmt_total_records && ($row_total = sqlsrv_fetch_array($stmt_total_records, SQLSRV_FETCH_ASSOC))) {
    $total_records = $row_total['total'];
}
if ($stmt_total_records) sqlsrv_free_stmt($stmt_total_records);

// Filtered records count (already includes the base conditions via $conditions)
$sql_filtered_count = $cte_sql . "SELECT COUNT(DISTINCT c.ID) as total " . $from_join_sql . $conditions;
$stmt_filtered_count = sqlsrv_query($mssqlconn, $sql_filtered_count, $query_params);
$records_filtered = 0;
if ($stmt_filtered_count && ($row_filtered = sqlsrv_fetch_array($stmt_filtered_count, SQLSRV_FETCH_ASSOC))) {
    $records_filtered = $row_filtered['total'];
} else if (!$stmt_filtered_count) {
     error_log("SQLSRV Error (Filtered Count Query) in back_active_packages.php: " . print_r(sqlsrv_errors(), true) . "\nQuery: " . $sql_filtered_count . "\nParams: " . print_r($query_params, true));
}
if ($stmt_filtered_count) sqlsrv_free_stmt($stmt_filtered_count);

// Ordering
$order_clause = "";
$default_order_column_sql = 'c.RecordDate'; $default_order_direction_sql = 'DESC';
if (isset($params['order']) && count($params['order'])) {
    $order_column_index = intval($params['order'][0]['column']);
    $sent_order_direction = strtoupper($params['order'][0]['dir']);
    $order_direction_sql = ($sent_order_direction === 'ASC' || $sent_order_direction === 'DESC') ? $sent_order_direction : $default_order_direction_sql;
    
    if (isset($column_map_for_sorting[$order_column_index])) {
        $order_column_sql_expr = $column_map_for_sorting[$order_column_index];
        if ($order_column_sql_expr === '_LastSmsSentDateSQL') { 
            $order_clause = " ORDER BY " . $default_order_column_sql . " " . $default_order_direction_sql;
        } else {
            $order_clause = " ORDER BY " . $order_column_sql_expr . " " . $order_direction_sql;
        }
    } else { $order_clause = " ORDER BY " . $default_order_column_sql . " " . $default_order_direction_sql; }
} else { $order_clause = " ORDER BY " . $default_order_column_sql . " " . $default_order_direction_sql; }

$final_sql_query = $cte_sql . $main_select_sql . $from_join_sql . $conditions . $order_clause;
$start = isset($params['start']) ? intval($params['start']) : 0;
$length = isset($params['length']) ? intval($params['length']) : 10;

$data_query_params = $query_params;
// Only apply pagination for regular table requests, not for exports
if (!$isExport && $length != -1) {
    $final_sql_query .= " OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
    $data_query_params[] = $start; $data_query_params[] = $length;
}

$stmt_data = sqlsrv_query($mssqlconn, $final_sql_query, $data_query_params);
$data_output = [];
if ($stmt_data) {
    while ($row = sqlsrv_fetch_array($stmt_data, SQLSRV_FETCH_ASSOC)) {
        $processed_row = [];
        $processed_row['FullName'] = $row['FullName'];
        $ms_sql_id_number = $row['IdNumber']; 
        $processed_row['IdNumber'] = $ms_sql_id_number;
        $processed_row['Phone'] = $row['Phone'];
        $processed_row['SendSms'] = isset($row['SendSms']) ? (int)$row['SendSms'] : 0;
        $processed_row['ClientRecordDate'] = $row['RecordDate'] ? $row['RecordDate']->format('Y-m-d H:i:s') : null;
        
        $my_sql_client_id_for_link = null;
        if ($ms_sql_id_number) {
            // Use MSSQL ClientDetailsWebsite instead of MySQL client_details
            $client_details = getClientDetailsByIdNumber($ms_sql_id_number);
            if ($client_details) {
                $my_sql_client_id_for_link = $client_details['id'];
            }
        }
        $processed_row['MySqlClientId'] = $my_sql_client_id_for_link;

        $last_sms_sent_date = null;
        if ($mysql_connection_active && $ms_sql_id_number && $conn) {
            $stmt_last_sms = $conn->prepare("SELECT MAX(record_date) as LastSmsDate FROM active_promo_sms_logs WHERE client_id_num = ?");
            if ($stmt_last_sms) {
                $stmt_last_sms->bind_param("s", $ms_sql_id_number);
                if ($stmt_last_sms->execute()) {
                    $result_last_sms = $stmt_last_sms->get_result();
                    if ($result_last_sms->num_rows > 0) {
                        $row_last_sms = $result_last_sms->fetch_assoc();
                        if ($row_last_sms['LastSmsDate'] !== null) {
                            $last_sms_sent_date = $row_last_sms['LastSmsDate'];
                        }
                    }
                } else { error_log("MySQL Execute Error (promo_sms_logs) in back_active_packages.php: " . $stmt_last_sms->error); }
                $stmt_last_sms->close();
            } else { error_log("MySQL Prepare Error (promo_sms_logs) in back_active_packages.php: " . $conn->error); }
        }
        $processed_row['LastSmsSentDate'] = $last_sms_sent_date;
        $processed_row['DaysUntilExpiry'] = $row['_DaysUntilExpirySQL']; 

        $processed_row['ActivePackage'] = $row['_ActivePackageNameSQL'];
        $processed_row['ActivePackageExpiryDate'] = $row['_ActivePackageEndDateSQL'] ? $row['_ActivePackageEndDateSQL']->format('Y-m-d') : null;
        
        $processed_row['LastPackage'] = $row['_OverallPackageNameSQL']; // Last package (might be active or expired)
        
        $data_output[] = $processed_row;
    }
    sqlsrv_free_stmt($stmt_data);
} else {
    error_log("CRITICAL: SQLSRV Error (Main Data Query Execution) in back_active_packages.php: " . print_r(sqlsrv_errors(), true) . "\nQuery: " . $final_sql_query . "\nParams: " . print_r($data_query_params, true));
}

if ($records_filtered > 0 && empty($data_output) && !$stmt_data) { 
    error_log("WARNING: recordsFiltered was {$records_filtered} but data_output array is empty AND stmt_data was false. Main query failed.");
} elseif ($records_filtered > 0 && empty($data_output)) {
    error_log("WARNING: recordsFiltered was {$records_filtered} but data_output array is empty. Main query might have returned no rows despite count, or processing loop issue.");
}

if ($mssqlconn) sqlsrv_close($mssqlconn);
if ($mysql_connection_active && $conn) $conn->close();

if ($isExport) {
    // Prepare data in HTML table format for Excel
    $filename = 'active_packages_export_' . date('Y-m-d_H-i-s') . '.xls';
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $excelData = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    $excelData .= '<head><meta charset="UTF-8"></head><body>';
    $excelData .= '<table>';
    
    // Headers
    $excelData .= '<thead><tr>';
    $excelData .= '<th>სახელი</th>';
    $excelData .= '<th>ID</th>';
    $excelData .= '<th>ნომერი</th>';
    $excelData .= '<th>SMS</th>';
    $excelData .= '<th>პაკეტი</th>';
    $excelData .= '<th>ვადა მთავრდება</th>';
    $excelData .= '<th>დღეები ვადის ამოწურვამდე</th>';
    $excelData .= '<th>რეგისტრაცია</th>';
    $excelData .= '<th>ბოლო SMS</th>';
    $excelData .= '</tr></thead>';
    
    // Body
    $excelData .= '<tbody>';
    foreach ($data_output as $row) {
        $excelData .= '<tr>';
        // Apply mso-number-format to treat these as text, especially for leading zeros
        $excelData .= '<td style="mso-number-format:\'@\';">' . htmlspecialchars($row['FullName'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td style="mso-number-format:\'@\';">' . htmlspecialchars($row['IdNumber'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td style="mso-number-format:\'@\';">' . htmlspecialchars($row['Phone'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td>' . htmlspecialchars($row['SendSms'] ? 'Yes' : 'No', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td>' . htmlspecialchars($row['ActivePackage'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td>' . htmlspecialchars($row['ActivePackageExpiryDate'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td>' . htmlspecialchars($row['DaysUntilExpiry'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td>' . htmlspecialchars($row['ClientRecordDate'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '<td>' . htmlspecialchars($row['LastSmsSentDate'] ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
        $excelData .= '</tr>';
    }
    $excelData .= '</tbody></table></body></html>';

    echo $excelData;
    exit;
} else {
    $json_data = [
        "draw" => isset($params['draw']) ? intval($params['draw']) : 0,
        "recordsTotal" => intval($total_records),
        "recordsFiltered" => intval($records_filtered),
        "data" => $data_output,
        "uniqueActivePackageNames" => $unique_active_package_names
    ];

    echo json_encode($json_data);
    exit;
}
?>
