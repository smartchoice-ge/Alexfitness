<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to prevent JSON corruption

// Set content type for JSON response
header('Content-Type: application/json');

try {
    include_once 'mssql_connection.php';
    include_once 'mssql_packages_payments_helper.php';

    // Get DataTables parameters
    $limit = $_POST['length']; // Number of rows per page
    $offset = $_POST['start']; // Offset for pagination
    $search = $_POST['search']['value']; // Search term
    $orderColumnIndex = $_POST['order'][0]['column']; // Column index to sort by
    $orderDirection = $_POST['order'][0]['dir']; // Sort direction (asc or desc)

    // Map DataTables column index to database column name - updated for profile picture column
    $columns = [
        0 => 'picurl',  // Profile picture column
        1 => 'full_name',
        2 => 'id_number',
        3 => 'mobile_number',
        4 => 'email',
        5 => 'agreement_date',
        6 => 'birth_date',
        7 => 'agreed',
        8 => 'home_address',  // Parent agreement related
        9 => 'child_name',
        10 => 'child_id'
    ];

    // Get the column name to sort by
    $orderColumn = isset($columns[$orderColumnIndex]) ? $columns[$orderColumnIndex] : 'agreement_date';

    // Use MSSQL helper to search clients
    $result = searchClientDetails([
        'search' => $search,
        'limit' => (int)$limit,
        'offset' => (int)$offset,
        'orderBy' => $orderColumn,
        'orderDir' => $orderDirection
    ]);
    
    $clients = $result['clients'];
    $totalRecords = $result['total'];
    
    // Add parent agreement detection based on file existence and child data
    foreach ($clients as &$client) {
        // Check PDF path
        $docsDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/docs/';
        $parentPdfPath = $docsDir . $client['id_number'] . 'parent.pdf';
        
        $hasParentPDF = file_exists($parentPdfPath);
        
        $hasChildData = !empty($client['child_name']) && !empty($client['child_id']) && 
                       trim($client['child_name']) !== '' && trim($client['child_id']) !== '';
        
        $client['parent_agreement'] = ($hasParentPDF || $hasChildData) ? 'Yes' : 'No';
    }

    // Prepare response
    $response = [
        "draw" => intval($_POST['draw']),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecords,
        "data" => $clients
    ];

    echo json_encode($response);
    
} catch (Exception $e) {
    // Return error response in JSON format
    http_response_code(500);
    echo json_encode([
        "error" => "Database error: " . $e->getMessage(),
        "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
        "recordsTotal" => 0,
        "recordsFiltered" => 0,
        "data" => []
    ]);
}
?>