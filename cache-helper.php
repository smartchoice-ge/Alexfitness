<?php
/**
 * Cache optimization helper for static assets
 * This file sets proper cache headers for static resources
 */

// Get the requested file path
$requestedFile = $_GET['file'] ?? '';
$allowedExtensions = ['js', 'css', 'woff', 'woff2', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'];

// Validate file extension
$extension = pathinfo($requestedFile, PATHINFO_EXTENSION);
if (!in_array(strtolower($extension), $allowedExtensions)) {
    http_response_code(404);
    exit('File not found');
}

// Build the full file path
$filePath = realpath($requestedFile);
if (!$filePath || !file_exists($filePath)) {
    http_response_code(404);
    exit('File not found');
}

// Security check - ensure file is in allowed directories
$allowedPaths = [
    realpath(__DIR__ . '/js/'),
    realpath(__DIR__ . '/css/'),
    realpath(__DIR__ . '/img/'),
    realpath(__DIR__ . '/css/webfonts/')
];

$isAllowed = false;
foreach ($allowedPaths as $allowedPath) {
    if ($allowedPath && strpos($filePath, $allowedPath) === 0) {
        $isAllowed = true;
        break;
    }
}

if (!$isAllowed) {
    http_response_code(403);
    exit('Access denied');
}

// Set cache headers based on file type
$maxAge = 2592000; // 30 days default
$immutable = false;

switch (strtolower($extension)) {
    case 'woff':
    case 'woff2':
        $maxAge = 31536000; // 1 year for fonts
        $immutable = true;
        header('Content-Type: application/font-woff' . ($extension === 'woff2' ? '2' : ''));
        header('Access-Control-Allow-Origin: *');
        break;
    
    case 'css':
        $maxAge = 2592000; // 30 days for CSS
        $immutable = true;
        header('Content-Type: text/css');
        break;
    
    case 'js':
        $maxAge = 2592000; // 30 days for JS
        $immutable = true;
        header('Content-Type: application/javascript');
        break;
    
    case 'png':
        $maxAge = 15552000; // 6 months for images
        header('Content-Type: image/png');
        break;
    
    case 'jpg':
    case 'jpeg':
        $maxAge = 15552000; // 6 months for images
        header('Content-Type: image/jpeg');
        break;
    
    case 'gif':
        $maxAge = 15552000; // 6 months for images
        header('Content-Type: image/gif');
        break;
    
    case 'webp':
        $maxAge = 15552000; // 6 months for images
        header('Content-Type: image/webp');
        break;
    
    case 'svg':
        $maxAge = 15552000; // 6 months for SVG
        header('Content-Type: image/svg+xml');
        break;
}

// Set cache control headers
$cacheControl = 'public, max-age=' . $maxAge;
if ($immutable) {
    $cacheControl .= ', immutable';
}

header('Cache-Control: ' . $cacheControl);
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
header('Vary: Accept-Encoding');

// Remove ETag
header_remove('ETag');

// Set content length
$fileSize = filesize($filePath);
header('Content-Length: ' . $fileSize);

// Set last modified
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');

// Enable compression if possible
if (function_exists('gzencode') && strpos($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip') !== false) {
    $content = file_get_contents($filePath);
    $compressed = gzencode($content, 9);
    
    if (strlen($compressed) < strlen($content)) {
        header('Content-Encoding: gzip');
        header('Content-Length: ' . strlen($compressed));
        echo $compressed;
        exit;
    }
}

// Output the file
readfile($filePath);
?>
