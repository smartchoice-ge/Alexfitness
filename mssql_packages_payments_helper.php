<?php
/**
 * Database Helper for PackagesWebsite and PaymentsWebsite tables
 * 
 * This file provides functions to interact with the MSSQL tables
 * (PackagesWebsite, PaymentsWebsite) that were migrated from MySQL.
 * 
 * Include this file and use these functions instead of direct MySQL queries.
 */

require_once __DIR__ . '/mssql_connection.php';

/**
 * Get all packages from MSSQL PackagesWebsite table
 * @param string $orderBy - ORDER BY clause (default: order_number ASC, id ASC)
 * @param string $where - WHERE clause conditions (without WHERE keyword)
 * @return array - Array of packages
 */
function getPackagesWebsite($orderBy = 'order_number ASC, id ASC', $where = null) {
    global $mssqlconn;
    
    $packages = [];
    if (!$mssqlconn) {
        error_log("MSSQL connection not available in getPackagesWebsite");
        return $packages;
    }
    
    $sql = "SELECT id, package_id, price, old_price, name_geo, name_eng, duration_month, description, description_geo, deal, order_number FROM PackagesWebsite";
    if ($where) {
        $sql .= " WHERE " . $where;
    }
    $sql .= " ORDER BY " . $orderBy;
    
    $stmt = sqlsrv_query($mssqlconn, $sql);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("getPackagesWebsite error: " . print_r($errors, true));
        return $packages;
    }
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $packages[] = $row;
    }
    sqlsrv_free_stmt($stmt);
    
    return $packages;
}

/**
 * Get a single package by ID
 * @param int $id - Package ID
 * @return array|null - Package data or null if not found
 */
function getPackageWebsiteById($id) {
    global $mssqlconn;
    
    if (!$mssqlconn) return null;
    
    $sql = "SELECT id, package_id, price, old_price, name_geo, name_eng, duration_month, description, description_geo, deal, order_number FROM PackagesWebsite WHERE id = ?";
    $params = [$id];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) return null;
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    return $row ?: null;
}

/**
 * Get package by package_id (MSSQL Packages.ID reference)
 * @param int $packageId - The package_id (foreign key to Packages.ID)
 * @return array|null - Package data or null if not found
 */
function getPackageWebsiteByPackageId($packageId) {
    global $mssqlconn;
    
    if (!$mssqlconn) return null;
    
    $sql = "SELECT id, package_id, price, old_price, name_geo, name_eng, duration_month, description, description_geo, deal, order_number FROM PackagesWebsite WHERE package_id = ?";
    $params = [$packageId];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) return null;
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    return $row ?: null;
}

/**
 * Insert a new package
 * @param array $data - Package data
 * @return int|false - Inserted ID or false on error
 */
function insertPackageWebsite($data) {
    global $mssqlconn;
    
    if (!$mssqlconn) return false;
    
    $sql = "INSERT INTO PackagesWebsite (package_id, price, old_price, name_geo, name_eng, duration_month, description, description_geo, deal, order_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
            SELECT SCOPE_IDENTITY() AS id;";
    
    $params = [
        $data['package_id'],
        $data['price'] ?? 0,
        $data['old_price'] ?? null,
        $data['name_geo'] ?? '',
        $data['name_eng'] ?? '',
        $data['duration_month'] ?? 1,
        $data['description'] ?? null,
        $data['description_geo'] ?? null,
        $data['deal'] ?? '',
        $data['order_number'] ?? 0
    ];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("insertPackageWebsite error: " . print_r($errors, true));
        return false;
    }
    
    // Get the inserted ID
    sqlsrv_next_result($stmt);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    return $row ? (int)$row['id'] : false;
}

/**
 * Update a package
 * @param int $id - Package ID
 * @param array $data - Package data to update
 * @return bool - Success status
 */
function updatePackageWebsite($id, $data) {
    global $mssqlconn;
    
    if (!$mssqlconn) return false;
    
    $sql = "UPDATE PackagesWebsite SET 
            package_id = ?, 
            price = ?, 
            old_price = ?, 
            name_geo = ?, 
            name_eng = ?, 
            duration_month = ?, 
            description = ?, 
            description_geo = ?, 
            deal = ?, 
            order_number = ?,
            updated_at = GETDATE()
            WHERE id = ?";
    
    $params = [
        $data['package_id'],
        $data['price'] ?? 0,
        $data['old_price'] ?? null,
        $data['name_geo'] ?? '',
        $data['name_eng'] ?? '',
        $data['duration_month'] ?? 1,
        $data['description'] ?? null,
        $data['description_geo'] ?? null,
        $data['deal'] ?? '',
        $data['order_number'] ?? 0,
        $id
    ];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("updatePackageWebsite error: " . print_r($errors, true));
        return false;
    }
    
    sqlsrv_free_stmt($stmt);
    return true;
}

/**
 * Delete a package
 * @param int $id - Package ID
 * @return bool - Success status
 */
function deletePackageWebsite($id) {
    global $mssqlconn;
    
    if (!$mssqlconn) return false;
    
    $sql = "DELETE FROM PackagesWebsite WHERE id = ?";
    $params = [$id];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("deletePackageWebsite error: " . print_r($errors, true));
        return false;
    }
    
    sqlsrv_free_stmt($stmt);
    return true;
}

// =====================================================
// PAYMENTS FUNCTIONS
// =====================================================

/**
 * Insert a new payment
 * @param array $data - Payment data
 * @return int|false - Inserted ID or false on error
 */
function insertPaymentWebsite($data) {
    global $mssqlconn;
    
    if (!$mssqlconn) return false;
    
    $sql = "INSERT INTO PaymentsWebsite (amount, status, client_mobile_number, transaction_id, time, package_id, user_id, processed) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?);
            SELECT SCOPE_IDENTITY() AS id;";
    
    $timeValue = $data['time'] ?? date('Y-m-d H:i:s');
    
    $params = [
        $data['amount'] ?? 0,
        $data['status'] ?? 'Pending',
        $data['client_mobile_number'] ?? '',
        $data['transaction_id'] ?? null,
        $timeValue,
        $data['package_id'] ?? null,
        $data['user_id'] ?? null,
        isset($data['processed']) ? ($data['processed'] ? 1 : 0) : 0
    ];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("insertPaymentWebsite error: " . print_r($errors, true));
        return false;
    }
    
    // Get the inserted ID
    sqlsrv_next_result($stmt);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    return $row ? (int)$row['id'] : false;
}

/**
 * Update payment status by transaction ID
 * @param string $transactionId - Transaction ID
 * @param string $status - New status
 * @return int - Number of affected rows
 */
function updatePaymentStatusByTransactionId($transactionId, $status) {
    global $mssqlconn;
    
    if (!$mssqlconn) return 0;
    
    // Only update if not yet processed; prevents a second Flitt callback from
    // triggering reprocessing when the first callback already completed.
    $sql = "UPDATE PaymentsWebsite SET status = ?, updated_at = GETDATE() WHERE transaction_id = ? AND processed = 0";
    $params = [$status, $transactionId];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("updatePaymentStatusByTransactionId error: " . print_r($errors, true));
        return 0;
    }
    
    $rowsAffected = sqlsrv_rows_affected($stmt);
    sqlsrv_free_stmt($stmt);
    
    return $rowsAffected ?: 0;
}

/**
 * Update payment status by mobile number (for pending payments)
 * @param string $mobileNumber - Client mobile number
 * @param string $status - New status
 * @param string|null $transactionId - Optional transaction ID to set
 * @return int - Number of affected rows
 */
function updatePaymentStatusByMobile($mobileNumber, $status, $transactionId = null) {
    global $mssqlconn;
    
    if (!$mssqlconn) return 0;
    
    if ($transactionId) {
        $sql = "UPDATE TOP (1) PaymentsWebsite SET status = ?, transaction_id = ?, updated_at = GETDATE() 
                WHERE client_mobile_number = ? AND status = 'Pending' ORDER BY time DESC";
        $params = [$status, $transactionId, $mobileNumber];
    } else {
        $sql = "UPDATE TOP (1) PaymentsWebsite SET status = ?, updated_at = GETDATE() 
                WHERE client_mobile_number = ? AND status = 'Pending' ORDER BY time DESC";
        $params = [$status, $mobileNumber];
    }
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("updatePaymentStatusByMobile error: " . print_r($errors, true));
        return 0;
    }
    
    $rowsAffected = sqlsrv_rows_affected($stmt);
    sqlsrv_free_stmt($stmt);
    
    return $rowsAffected ?: 0;
}

/**
 * Get payment by ID
 * @param int $id - Payment ID
 * @return array|null - Payment data or null
 */
function getPaymentWebsiteById($id) {
    global $mssqlconn;
    
    if (!$mssqlconn) return null;
    
    $sql = "SELECT id, amount, status, client_mobile_number, transaction_id, time, package_id, user_id, processed FROM PaymentsWebsite WHERE id = ?";
    $params = [$id];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) return null;
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    // Convert DateTime objects to strings
    if ($row && $row['time'] instanceof DateTime) {
        $row['time'] = $row['time']->format('Y-m-d H:i:s');
    }
    
    return $row ?: null;
}

/**
 * Get payment by transaction ID
 * @param string $transactionId - Transaction ID
 * @param string|null $status - Optional status filter
 * @return array|null - Payment data or null
 */
function getPaymentByTransactionId($transactionId, $status = null) {
    global $mssqlconn;
    
    if (!$mssqlconn) return null;
    
    $sql = "SELECT TOP 1 id, amount, status, client_mobile_number, transaction_id, time, package_id, user_id, processed FROM PaymentsWebsite WHERE transaction_id = ?";
    $params = [$transactionId];
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY time DESC";
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) return null;
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    if ($row && $row['time'] instanceof DateTime) {
        $row['time'] = $row['time']->format('Y-m-d H:i:s');
    }
    
    return $row ?: null;
}

/**
 * Get payment by mobile number
 * @param string $mobileNumber - Client mobile number
 * @param string|null $status - Optional status filter
 * @return array|null - Payment data or null
 */
function getPaymentByMobile($mobileNumber, $status = null) {
    global $mssqlconn;
    
    if (!$mssqlconn) return null;
    
    $sql = "SELECT TOP 1 id, amount, status, client_mobile_number, transaction_id, time, package_id, user_id, processed FROM PaymentsWebsite WHERE client_mobile_number = ?";
    $params = [$mobileNumber];
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY time DESC";
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) return null;
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    if ($row && $row['time'] instanceof DateTime) {
        $row['time'] = $row['time']->format('Y-m-d H:i:s');
    }
    
    return $row ?: null;
}

/**
 * Mark payment as processed
 * @param int $id - Payment ID
 * @return bool - Success status
 */
function markPaymentProcessed($id) {
    global $mssqlconn;
    
    if (!$mssqlconn) return false;
    
    $sql = "UPDATE PaymentsWebsite SET processed = 1, updated_at = GETDATE() WHERE id = ?";
    $params = [$id];
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("markPaymentProcessed error: " . print_r($errors, true));
        return false;
    }
    
    sqlsrv_free_stmt($stmt);
    return true;
}

/**
 * Get payments with pagination and filters
 * @param array $options - Options: limit, offset, status, search, orderBy
 * @return array - Array with 'payments' and 'total' keys
 */
function getPaymentsWebsite($options = []) {
    global $mssqlconn, $conn; // $conn is MySQL connection
    
    $result = ['payments' => [], 'total' => 0];
    if (!$mssqlconn) return $result;
    
    $limit = $options['limit'] ?? 30;
    $offset = $options['offset'] ?? 0;
    $status = $options['status'] ?? null;
    $search = $options['search'] ?? null;
    $orderBy = $options['orderBy'] ?? 'time DESC';
    
    // Build WHERE clause
    $where = [];
    $params = [];
    
    if ($status) {
        $where[] = "p.status = ?";
        $params[] = $status;
    }
    
    if ($search) {
        $where[] = "(p.client_mobile_number LIKE ? OR p.transaction_id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
    
    // Count total
    $countSql = "SELECT COUNT(*) as total FROM PaymentsWebsite p $whereClause";
    $countStmt = sqlsrv_query($mssqlconn, $countSql, $params);
    if ($countStmt) {
        $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
        $result['total'] = $countRow['total'];
        sqlsrv_free_stmt($countStmt);
    }
    
    // Get payments with package info
    $sql = "SELECT p.id, p.amount, p.status, p.client_mobile_number, p.transaction_id, p.time, 
                   p.package_id, p.user_id, p.processed,
                   pkg.name_geo as package_name
            FROM PaymentsWebsite p 
            LEFT JOIN PackagesWebsite pkg ON p.package_id = pkg.package_id
            $whereClause
            ORDER BY $orderBy
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
    
    $params[] = $offset;
    $params[] = $limit;
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        $errors = sqlsrv_errors();
        error_log("getPaymentsWebsite error: " . print_r($errors, true));
        return $result;
    }
    
    // Collect all payments first
    $payments = [];
    $userIds = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Convert DateTime objects
        if ($row['time'] instanceof DateTime) {
            $row['time'] = $row['time']->format('Y-m-d H:i:s');
        }
        $payments[] = $row;
        // Collect user_ids that need client info lookup
        if (!empty($row['user_id'])) {
            $userIds[] = (int)$row['user_id'];
        }
    }
    sqlsrv_free_stmt($stmt);
    
    // Fetch client details from MSSQL ClientDetailsWebsite table in batch
    $clientDetails = [];
    if (!empty($userIds)) {
        $userIds = array_values(array_unique($userIds)); // array_values to reindex
        $placeholders = implode(',', array_fill(0, count($userIds), '?'));
        
        $clientSql = "SELECT id, full_name, picurl FROM ClientDetailsWebsite WHERE id IN ($placeholders)";
        $clientStmt = sqlsrv_query($mssqlconn, $clientSql, $userIds);
        if ($clientStmt) {
            while ($clientRow = sqlsrv_fetch_array($clientStmt, SQLSRV_FETCH_ASSOC)) {
                $clientDetails[$clientRow['id']] = [
                    'full_name' => $clientRow['full_name'],
                    'profile_image' => $clientRow['picurl']
                ];
            }
            sqlsrv_free_stmt($clientStmt);
        }
    }
    
    // Merge client details into payments
    // Also collect phone numbers for fallback lookup from Clients table
    $phonesNeedingLookup = [];
    foreach ($payments as $idx => &$payment) {
        $payment['full_name'] = null;
        $payment['profile_image'] = null;
        if (!empty($payment['user_id']) && isset($clientDetails[$payment['user_id']])) {
            $payment['full_name'] = $clientDetails[$payment['user_id']]['full_name'];
            $payment['profile_image'] = $clientDetails[$payment['user_id']]['profile_image'];
        }
        // If still no name, collect phone for Clients table fallback
        if (empty($payment['full_name']) && !empty($payment['client_mobile_number'])) {
            $phone = $payment['client_mobile_number'];
            $phonesNeedingLookup[$phone] = true;
            // Also add the other format (with/without 995)
            if (strpos($phone, '995') === 0) {
                $phonesNeedingLookup[substr($phone, 3)] = true;
            } else {
                $phonesNeedingLookup['995' . $phone] = true;
            }
        }
    }
    unset($payment); // break reference
    
    // Fallback: look up names from MSSQL Clients table by phone number
    $clientsByPhone = [];
    if (!empty($phonesNeedingLookup)) {
        $phoneList = array_values(array_keys($phonesNeedingLookup));
        $phonePlaceholders = implode(',', array_fill(0, count($phoneList), '?'));
        $clientFallbackSql = "SELECT Phone, FullName, ProfilePicture FROM Clients WHERE Phone IN ($phonePlaceholders)";
        $clientFallbackStmt = sqlsrv_query($mssqlconn, $clientFallbackSql, $phoneList);
        if ($clientFallbackStmt) {
            while ($row = sqlsrv_fetch_array($clientFallbackStmt, SQLSRV_FETCH_ASSOC)) {
                $clientsByPhone[$row['Phone']] = [
                    'full_name' => $row['FullName'],
                    'profile_image' => $row['ProfilePicture']
                ];
            }
            sqlsrv_free_stmt($clientFallbackStmt);
        }
    }
    
    // Apply fallback names
    foreach ($payments as &$payment) {
        if (empty($payment['full_name']) && !empty($payment['client_mobile_number'])) {
            $phone = $payment['client_mobile_number'];
            if (isset($clientsByPhone[$phone])) {
                $payment['full_name'] = $clientsByPhone[$phone]['full_name'];
                $payment['profile_image'] = $clientsByPhone[$phone]['profile_image'];
            } else {
                // Try other phone format
                $altPhone = (strpos($phone, '995') === 0) ? substr($phone, 3) : '995' . $phone;
                if (isset($clientsByPhone[$altPhone])) {
                    $payment['full_name'] = $clientsByPhone[$altPhone]['full_name'];
                    $payment['profile_image'] = $clientsByPhone[$altPhone]['profile_image'];
                }
            }
        }
    }
    unset($payment); // break reference
    
    $result['payments'] = $payments;
    
    return $result;
}

/**
 * Get payment statistics
 * @return array - Statistics array
 */
function getPaymentStats() {
    global $mssqlconn;
    
    $stats = [
        'total_today' => 0,
        'total_week' => 0,
        'total_month' => 0,
        'count_today' => 0,
        'count_week' => 0,
        'count_month' => 0
    ];
    
    if (!$mssqlconn) return $stats;
    
    $sql = "SELECT 
                SUM(CASE WHEN CAST(time AS DATE) = CAST(GETDATE() AS DATE) THEN amount ELSE 0 END) as total_today,
                SUM(CASE WHEN time >= DATEADD(day, -7, GETDATE()) THEN amount ELSE 0 END) as total_week,
                SUM(CASE WHEN time >= DATEADD(day, -30, GETDATE()) THEN amount ELSE 0 END) as total_month,
                COUNT(CASE WHEN CAST(time AS DATE) = CAST(GETDATE() AS DATE) THEN 1 END) as count_today,
                COUNT(CASE WHEN time >= DATEADD(day, -7, GETDATE()) THEN 1 END) as count_week,
                COUNT(CASE WHEN time >= DATEADD(day, -30, GETDATE()) THEN 1 END) as count_month
            FROM PaymentsWebsite
            WHERE status = 'Success'";
    
    $stmt = sqlsrv_query($mssqlconn, $sql);
    if ($stmt) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        if ($row) {
            $stats = [
                'total_today' => $row['total_today'] ?? 0,
                'total_week' => $row['total_week'] ?? 0,
                'total_month' => $row['total_month'] ?? 0,
                'count_today' => $row['count_today'] ?? 0,
                'count_week' => $row['count_week'] ?? 0,
                'count_month' => $row['count_month'] ?? 0
            ];
        }
        sqlsrv_free_stmt($stmt);
    }
    
    return $stats;
}

// =====================================================
// ClientDetailsWebsite Functions (migrated from MySQL client_details)
// =====================================================

/**
 * Get client details by ID
 * @param int $id - Client ID
 * @return array|null - Client details or null if not found
 */
function getClientDetailsById($id) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$id) return null;
    
    $sql = "SELECT id, id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id, created_at, updated_at FROM ClientDetailsWebsite WHERE id = ?";
    $stmt = sqlsrv_query($mssqlconn, $sql, array($id));
    
    if ($stmt === false) {
        error_log("getClientDetailsById error: " . print_r(sqlsrv_errors(), true));
        return null;
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    if ($row) {
        // Convert DateTime objects
        if ($row['agreement_date'] instanceof DateTime) {
            $row['agreement_date'] = $row['agreement_date']->format('Y-m-d H:i:s');
        }
        if ($row['birth_date'] instanceof DateTime) {
            $row['birth_date'] = $row['birth_date']->format('Y-m-d');
        }
        if ($row['created_at'] instanceof DateTime) {
            $row['created_at'] = $row['created_at']->format('Y-m-d H:i:s');
        }
        if ($row['updated_at'] instanceof DateTime) {
            $row['updated_at'] = $row['updated_at']->format('Y-m-d H:i:s');
        }
    }
    
    return $row;
}

/**
 * Get client details by ID number (personal ID)
 * @param string $idNumber - Personal ID number
 * @return array|null - Client details or null if not found
 */
function getClientDetailsByIdNumber($idNumber) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$idNumber) return null;
    
    $sql = "SELECT id, id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id FROM ClientDetailsWebsite WHERE id_number = ?";
    $stmt = sqlsrv_query($mssqlconn, $sql, array($idNumber));
    
    if ($stmt === false) {
        error_log("getClientDetailsByIdNumber error: " . print_r(sqlsrv_errors(), true));
        return null;
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    if ($row) {
        // Convert DateTime objects
        if ($row['agreement_date'] instanceof DateTime) {
            $row['agreement_date'] = $row['agreement_date']->format('Y-m-d H:i:s');
        }
        if ($row['birth_date'] instanceof DateTime) {
            $row['birth_date'] = $row['birth_date']->format('Y-m-d');
        }
    }
    
    return $row;
}

/**
 * Get client details by mobile number (tries multiple formats)
 * @param string $mobileNumber - Mobile number
 * @return array|null - Client details or null if not found
 */
function getClientDetailsByMobile($mobileNumber) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$mobileNumber) return null;
    
    // Prepare phone variations
    $variations = array($mobileNumber);
    if (strpos($mobileNumber, '995') === 0) {
        $variations[] = substr($mobileNumber, 3); // Without 995
    } else {
        $variations[] = '995' . $mobileNumber; // With 995
    }
    
    $placeholders = implode(',', array_fill(0, count($variations), '?'));
    $sql = "SELECT TOP 1 id, id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id FROM ClientDetailsWebsite WHERE mobile_number IN ($placeholders)";
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $variations);
    
    if ($stmt === false) {
        error_log("getClientDetailsByMobile error: " . print_r(sqlsrv_errors(), true));
        return null;
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    if ($row) {
        if ($row['agreement_date'] instanceof DateTime) {
            $row['agreement_date'] = $row['agreement_date']->format('Y-m-d H:i:s');
        }
        if ($row['birth_date'] instanceof DateTime) {
            $row['birth_date'] = $row['birth_date']->format('Y-m-d');
        }
    }
    
    return $row;
}

/**
 * Get client details by email
 * @param string $email - Email address
 * @return array|null - Client details or null if not found
 */
function getClientDetailsByEmail($email) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$email) return null;
    
    $sql = "SELECT id, id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id FROM ClientDetailsWebsite WHERE email = ?";
    $stmt = sqlsrv_query($mssqlconn, $sql, array($email));
    
    if ($stmt === false) {
        error_log("getClientDetailsByEmail error: " . print_r(sqlsrv_errors(), true));
        return null;
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    if ($row) {
        if ($row['agreement_date'] instanceof DateTime) {
            $row['agreement_date'] = $row['agreement_date']->format('Y-m-d H:i:s');
        }
        if ($row['birth_date'] instanceof DateTime) {
            $row['birth_date'] = $row['birth_date']->format('Y-m-d');
        }
    }
    
    return $row;
}

/**
 * Insert new client details
 * @param array $data - Client data
 * @return int|false - Inserted ID or false on failure
 */
function insertClientDetails($data) {
    global $mssqlconn;
    
    if (!$mssqlconn) return false;
    
    $sql = "INSERT INTO ClientDetailsWebsite (id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);
            SELECT SCOPE_IDENTITY() AS id;";
    
    $params = array(
        $data['id_number'] ?? null,
        $data['full_name'] ?? null,
        $data['mobile_number'] ?? null,
        $data['email'] ?? null,
        ($data['agreed'] ?? 0) ? 1 : 0,
        $data['agreement_date'] ?? date('Y-m-d H:i:s'),
        $data['user_ip'] ?? null,
        $data['birth_date'] ?? null,
        $data['picurl'] ?? null,
        $data['home_address'] ?? null,
        $data['child_name'] ?? null,
        $data['child_id'] ?? null
    );
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    
    if ($stmt === false) {
        error_log("insertClientDetails error: " . print_r(sqlsrv_errors(), true));
        return false;
    }
    
    // Get the inserted ID
    sqlsrv_next_result($stmt);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    return $row ? (int)$row['id'] : false;
}

/**
 * Update client details by ID
 * @param int $id - Client ID
 * @param array $data - Data to update
 * @return bool - Success
 */
function updateClientDetailsById($id, $data) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$id) return false;
    
    $fields = [];
    $params = [];
    
    $allowedFields = ['id_number', 'full_name', 'mobile_number', 'email', 'agreed', 'agreement_date', 'user_ip', 'birth_date', 'picurl', 'home_address', 'child_name', 'child_id'];
    
    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $data)) {
            $fields[] = "$field = ?";
            if ($field === 'agreed') {
                $params[] = $data[$field] ? 1 : 0;
            } else {
                $params[] = $data[$field];
            }
        }
    }
    
    if (empty($fields)) return false;
    
    $fields[] = "updated_at = GETDATE()";
    $params[] = $id;
    
    $sql = "UPDATE ClientDetailsWebsite SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    
    if ($stmt === false) {
        error_log("updateClientDetailsById error: " . print_r(sqlsrv_errors(), true));
        return false;
    }
    
    sqlsrv_free_stmt($stmt);
    return true;
}

/**
 * Update client details by ID number
 * @param string $idNumber - Personal ID number
 * @param array $data - Data to update
 * @return bool - Success
 */
function updateClientDetailsByIdNumber($idNumber, $data) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$idNumber) return false;
    
    $fields = [];
    $params = [];
    
    $allowedFields = ['full_name', 'mobile_number', 'email', 'agreed', 'agreement_date', 'user_ip', 'birth_date', 'picurl', 'home_address', 'child_name', 'child_id'];
    
    foreach ($allowedFields as $field) {
        if (array_key_exists($field, $data)) {
            $fields[] = "$field = ?";
            if ($field === 'agreed') {
                $params[] = $data[$field] ? 1 : 0;
            } else {
                $params[] = $data[$field];
            }
        }
    }
    
    if (empty($fields)) return false;
    
    $fields[] = "updated_at = GETDATE()";
    $params[] = $idNumber;
    
    $sql = "UPDATE ClientDetailsWebsite SET " . implode(', ', $fields) . " WHERE id_number = ?";
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    
    if ($stmt === false) {
        error_log("updateClientDetailsByIdNumber error: " . print_r(sqlsrv_errors(), true));
        return false;
    }
    
    sqlsrv_free_stmt($stmt);
    return true;
}

/**
 * Delete client details by ID
 * @param int $id - Client ID
 * @return bool - Success
 */
function deleteClientDetailsById($id) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$id) return false;
    
    $sql = "DELETE FROM ClientDetailsWebsite WHERE id = ?";
    $stmt = sqlsrv_query($mssqlconn, $sql, array($id));
    
    if ($stmt === false) {
        error_log("deleteClientDetailsById error: " . print_r(sqlsrv_errors(), true));
        return false;
    }
    
    sqlsrv_free_stmt($stmt);
    return true;
}

/**
 * Search client details with pagination
 * @param array $options - Search options (search, limit, offset, orderBy, orderDir)
 * @return array - Array with 'clients' and 'total'
 */
function searchClientDetails($options = []) {
    global $mssqlconn;
    
    $result = ['clients' => [], 'total' => 0];
    if (!$mssqlconn) return $result;
    
    $search = $options['search'] ?? '';
    $limit = $options['limit'] ?? 30;
    $offset = $options['offset'] ?? 0;
    $orderBy = $options['orderBy'] ?? 'agreement_date';
    $orderDir = strtoupper($options['orderDir'] ?? 'DESC');
    
    // Validate order direction
    if (!in_array($orderDir, ['ASC', 'DESC'])) {
        $orderDir = 'DESC';
    }
    
    // Validate order column
    $validColumns = ['id', 'id_number', 'full_name', 'mobile_number', 'email', 'agreement_date', 'birth_date'];
    if (!in_array($orderBy, $validColumns)) {
        $orderBy = 'agreement_date';
    }
    
    // Build WHERE clause
    $where = "";
    $params = [];
    
    if ($search) {
        $searchTerm = "%$search%";
        $where = "WHERE (full_name LIKE ? OR id_number LIKE ? OR mobile_number LIKE ? OR email LIKE ?)";
        $params = [$searchTerm, $searchTerm, $searchTerm, $searchTerm];
    }
    
    // Count total
    $countSql = "SELECT COUNT(*) as total FROM ClientDetailsWebsite $where";
    $countStmt = sqlsrv_query($mssqlconn, $countSql, $params);
    if ($countStmt) {
        $countRow = sqlsrv_fetch_array($countStmt, SQLSRV_FETCH_ASSOC);
        $result['total'] = $countRow['total'];
        sqlsrv_free_stmt($countStmt);
    }
    
    // Get clients
    $sql = "SELECT id, id_number, full_name, mobile_number, email, agreed, agreement_date, user_ip, birth_date, picurl, home_address, child_name, child_id
            FROM ClientDetailsWebsite 
            $where
            ORDER BY $orderBy $orderDir
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY";
    
    $params[] = $offset;
    $params[] = $limit;
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $params);
    if ($stmt === false) {
        error_log("searchClientDetails error: " . print_r(sqlsrv_errors(), true));
        return $result;
    }
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Convert DateTime objects
        if ($row['agreement_date'] instanceof DateTime) {
            $row['agreement_date'] = $row['agreement_date']->format('Y-m-d H:i:s');
        }
        $row['formatted_agreement_date'] = $row['agreement_date'] ?? '';
        if ($row['birth_date'] instanceof DateTime) {
            $row['birth_date'] = $row['birth_date']->format('Y-m-d');
        }
        $result['clients'][] = $row;
    }
    sqlsrv_free_stmt($stmt);
    
    return $result;
}

/**
 * Check if client exists by mobile number (for phone validation)
 * @param string $mobileNumber - Mobile number to check
 * @return bool - True if exists
 */
function clientExistsByMobile($mobileNumber) {
    global $mssqlconn;
    
    if (!$mssqlconn || !$mobileNumber) return false;
    
    // Prepare phone variations
    $variations = array($mobileNumber);
    if (strpos($mobileNumber, '995') === 0) {
        $variations[] = substr($mobileNumber, 3);
    } else {
        $variations[] = '995' . $mobileNumber;
    }
    
    $placeholders = implode(',', array_fill(0, count($variations), '?'));
    $sql = "SELECT TOP 1 1 FROM ClientDetailsWebsite WHERE mobile_number IN ($placeholders)";
    
    $stmt = sqlsrv_query($mssqlconn, $sql, $variations);
    
    if ($stmt === false) {
        return false;
    }
    
    $exists = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC) !== null;
    sqlsrv_free_stmt($stmt);
    
    return $exists;
}

/**
 * Get all distinct profile images (for image manager)
 * @return array - Array of picurl values
 */
function getAllClientProfileImages() {
    global $mssqlconn;
    
    $images = [];
    if (!$mssqlconn) return $images;
    
    $sql = "SELECT DISTINCT picurl FROM ClientDetailsWebsite WHERE picurl IS NOT NULL AND picurl != ''";
    $stmt = sqlsrv_query($mssqlconn, $sql);
    
    if ($stmt === false) {
        return $images;
    }
    
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $images[] = $row['picurl'];
    }
    sqlsrv_free_stmt($stmt);
    
    return $images;
}

/**
 * Update client profile image
 * @param int $id - Client ID
 * @param string $picurl - New image URL
 * @return bool - Success
 */
function updateClientProfileImage($id, $picurl) {
    return updateClientDetailsById($id, ['picurl' => $picurl]);
}

/**
 * Get total client count
 * @return int - Total count
 */
function getClientDetailsCount() {
    global $mssqlconn;
    
    if (!$mssqlconn) return 0;
    
    $sql = "SELECT COUNT(*) as cnt FROM ClientDetailsWebsite";
    $stmt = sqlsrv_query($mssqlconn, $sql);
    
    if ($stmt === false) {
        return 0;
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    sqlsrv_free_stmt($stmt);
    
    return $row ? (int)$row['cnt'] : 0;
}
?>
