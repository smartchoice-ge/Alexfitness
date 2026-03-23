<?php
// Simple upload script without image processing for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

function logSimple($message) {
    error_log("[SIMPLE_UPLOAD] " . $message);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['profile_picture'])) {
    logSimple("No profile_picture in FILES array");
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['profile_picture'];
logSimple("File received: " . $file['name'] . " Size: " . $file['size'] . " Type: " . $file['type'] . " Error: " . $file['error']);

if ($file['error'] !== UPLOAD_ERR_OK) {
    logSimple("Upload error: " . $file['error']);
    echo json_encode(['status' => 'error', 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

// Basic file type validation
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
$fileType = strtolower($file['type']);

if (!in_array($fileType, $allowedTypes)) {
    logSimple("Invalid file type: " . $fileType);
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF allowed.']);
    exit;
}

// File size check (15MB max)
if ($file['size'] > 15 * 1024 * 1024) {
    logSimple("File too large: " . $file['size']);
    echo json_encode(['status' => 'error', 'message' => 'File too large. Max 15MB allowed.']);
    exit;
}

// Create upload directory
$uploadDir = 'picupload/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        logSimple("Failed to create directory");
        echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
        exit;
    }
}

// Generate filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'simple_' . uniqid() . '_' . time() . '.' . $extension;
$filePath = $uploadDir . $fileName;

logSimple("Attempting to move file to: " . $filePath);

// Move file without any processing
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    logSimple("File uploaded successfully to: " . $filePath);
    
    $fullUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $filePath;
    
    echo json_encode([
        'status' => 'success',
        'message' => 'File uploaded successfully (no processing)',
        'url' => $filePath,
        'full_url' => $fullUrl,
        'filename' => $fileName
    ]);
} else {
    $lastError = error_get_last();
    logSimple("Failed to move file. Error: " . ($lastError['message'] ?? 'Unknown'));
    echo json_encode(['status' => 'error', 'message' => 'Failed to save file']);
}
?>
