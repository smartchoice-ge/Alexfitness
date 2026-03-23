<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '/home/SYNERGY_DOMAIN/public_html/mssql_connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verify the token exists in the database
    $sql = "SELECT Email FROM Clients WHERE unsubscribe_token = ?";
    $stmt = sqlsrv_prepare($mssqlconn, $sql, array(&$token));
    if (!$stmt) {
        die("SQL prepare failed: " . print_r(sqlsrv_errors(), true));
    }

    if (sqlsrv_execute($stmt)) {
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($user) {
            // Unsubscribe the user
            $update_sql = "UPDATE Clients SET SendEmail = 0, unsubscribe_token = '' WHERE Email = ?";
            $update_stmt = sqlsrv_prepare($mssqlconn, $update_sql, array(&$user['Email']));
            if (!$update_stmt) {
                die("SQL prepare failed: " . print_r(sqlsrv_errors(), true));
            }

            if (sqlsrv_execute($update_stmt)) {
                echo "You have successfully unsubscribed from our emails.";
            } else {
                echo "Failed to update database: " . print_r(sqlsrv_errors(), true);
            }
        } else {
            echo "Invalid or expired unsubscribe link.";
        }
    } else {
        echo "SQL execution failed: " . print_r(sqlsrv_errors(), true);
    }
} else {
    echo "No token provided.";
}

sqlsrv_close($mssqlconn);
?>