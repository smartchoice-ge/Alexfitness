<?php
session_start();
include_once '../db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Pagination settings
$records_per_page = 50;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build WHERE clause
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($status_filter)) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR mobile_number LIKE ? OR email LIKE ? OR id_number LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ssss';
}

if (!empty($date_from)) {
    $where_conditions[] = "DATE(created_at) >= ?";
    $params[] = $date_from;
    $param_types .= 's';
}

if (!empty($date_to)) {
    $where_conditions[] = "DATE(created_at) <= ?";
    $params[] = $date_to;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM mssql_save_logs {$where_clause}";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($param_types, ...$params);
}
$count_stmt->execute();
$total_records = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$count_stmt->close();

// Get logs
$sql = "SELECT * FROM mssql_save_logs {$where_clause} ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Add pagination params
$params[] = $records_per_page;
$params[] = $offset;
$param_types .= 'ii';

if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$logs = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get status counts
$stats_sql = "SELECT 
    status, 
    COUNT(*) as count,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as today_count
    FROM mssql_save_logs 
    GROUP BY status";
$stats_result = $conn->query($stats_sql);
$stats = [];
while ($row = $stats_result->fetch_assoc()) {
    $stats[$row['status']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSSQL Save Logs - Synergy Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-success { background-color: #D1FAE5; color: #065F46; }
        .status-failed { background-color: #FEE2E2; color: #991B1B; }
        .status-already_exists { background-color: #DBEAFE; color: #1E40AF; }
        .log-row:hover { background-color: #F9FAFB; }
        .error-details {
            max-width: 600px;
            overflow-x: auto;
            background: #1F2937;
            color: #F3F4F6;
            padding: 0.75rem;
            border-radius: 0.375rem;
            font-family: monospace;
            font-size: 0.75rem;
            white-space: pre-wrap;
            word-break: break-all;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <a href="users_list.php" class="text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">
                            <i class="fas fa-database mr-2"></i>MSSQL Save Logs
                        </h1>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Successful Saves</h3>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= isset($stats['success']) ? $stats['success']['count'] : 0 ?>
                            </p>
                            <p class="text-xs text-gray-500">Today: <?= isset($stats['success']) ? $stats['success']['today_count'] : 0 ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                            <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Failed Saves</h3>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= isset($stats['failed']) ? $stats['failed']['count'] : 0 ?>
                            </p>
                            <p class="text-xs text-gray-500">Today: <?= isset($stats['failed']) ? $stats['failed']['today_count'] : 0 ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Already Exists</h3>
                            <p class="text-2xl font-bold text-gray-900">
                                <?= isset($stats['already_exists']) ? $stats['already_exists']['count'] : 0 ?>
                            </p>
                            <p class="text-xs text-gray-500">Today: <?= isset($stats['already_exists']) ? $stats['already_exists']['today_count'] : 0 ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Statuses</option>
                            <option value="success" <?= $status_filter === 'success' ? 'selected' : '' ?>>Success</option>
                            <option value="failed" <?= $status_filter === 'failed' ? 'selected' : '' ?>>Failed</option>
                            <option value="already_exists" <?= $status_filter === 'already_exists' ? 'selected' : '' ?>>Already Exists</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Name, phone, email, ID..." 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" 
                               class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="flex items-end space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <a href="mssql_save_logs.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client Info</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MSSQL ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-2"></i>
                                        <p>No logs found</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr class="log-row">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($log['full_name'] ?? 'N/A') ?></div>
                                            <div class="text-gray-500 text-xs">ID: <?= htmlspecialchars($log['id_number'] ?? 'N/A') ?></div>
                                            <div class="text-gray-500 text-xs">MySQL ID: <?= $log['mysql_client_id'] ?? 'N/A' ?></div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div><?= htmlspecialchars($log['mobile_number'] ?? 'N/A') ?></div>
                                            <div class="text-xs text-gray-500"><?= htmlspecialchars($log['email'] ?? 'N/A') ?></div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="status-badge status-<?= $log['status'] ?>">
                                                <?php if ($log['status'] === 'success'): ?>
                                                    <i class="fas fa-check-circle mr-1"></i>Success
                                                <?php elseif ($log['status'] === 'failed'): ?>
                                                    <i class="fas fa-times-circle mr-1"></i>Failed
                                                <?php else: ?>
                                                    <i class="fas fa-info-circle mr-1"></i>Exists
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                            <?= $log['mssql_client_id'] ?? '-' ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <?php if (!empty($log['error_message'])): ?>
                                                <div class="mb-2">
                                                    <span class="text-red-600 font-medium">Error:</span>
                                                    <span class="text-gray-700"><?= htmlspecialchars($log['error_message']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($log['error_details'])): ?>
                                                <details class="cursor-pointer">
                                                    <summary class="text-blue-600 hover:text-blue-800 text-xs">
                                                        <i class="fas fa-code mr-1"></i>View Error Details
                                                    </summary>
                                                    <div class="error-details mt-2">
                                                        <?= htmlspecialchars($log['error_details']) ?>
                                                    </div>
                                                </details>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?= $offset + 1 ?></span> to 
                                <span class="font-medium"><?= min($offset + $records_per_page, $total_records) ?></span> of 
                                <span class="font-medium"><?= $total_records ?></span> results
                            </div>
                            <div class="flex space-x-2">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?= $page - 1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" 
                                       class="px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Previous
                                    </a>
                                <?php endif; ?>

                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <a href="?page=<?= $i ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" 
                                       class="px-3 py-1 border rounded-md <?= $i === $page ? 'bg-blue-600 text-white border-blue-600' : 'bg-white border-gray-300 hover:bg-gray-50' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?= $page + 1 ?>&status=<?= $status_filter ?>&search=<?= urlencode($search) ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>" 
                                       class="px-3 py-1 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
