<?php
// Start output buffering to catch any unexpected output
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Increase memory limit for image processing
ini_set('memory_limit', '512M');

// Set maximum execution time
ini_set('max_execution_time', 120);

// Custom error handler to catch fatal errors
function fatalErrorHandler() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        // Clear any previous output
        if (ob_get_length()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error', 
            'message' => 'Fatal PHP error: ' . $error['message'],
            'debug' => $error
        ]);
        exit;
    }
}
register_shutdown_function('fatalErrorHandler');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Debug logging function
function logDebug($message) {
    error_log("[UPLOAD_DEBUG] " . $message);
}

// Helper function to convert memory limit to bytes
function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = intval($val);
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_FILES['profile_picture'])) {
    logDebug("No profile_picture in FILES array. Available: " . implode(', ', array_keys($_FILES)));
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
    exit;
}

$file = $_FILES['profile_picture'];
logDebug("File received: " . $file['name'] . " Size: " . $file['size'] . " Type: " . $file['type']);

if ($file['error'] !== UPLOAD_ERR_OK) {
    logDebug("Upload error: " . $file['error']);
    echo json_encode(['status' => 'error', 'message' => 'Upload error: ' . $file['error']]);
    exit;
}

// Validate file type
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

logDebug("Detected MIME type: " . $mimeType);

if (!in_array($mimeType, $allowedTypes)) {
    logDebug("Invalid file type. MIME: " . $mimeType);
    echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF and WebP are allowed.']);
    exit;
}

// Validate file size (15MB max)
$maxSize = 15 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    logDebug("File too large: " . $file['size'] . " bytes");
    echo json_encode(['status' => 'error', 'message' => 'File size too large. Maximum 15MB allowed.']);
    exit;
}

// Check available memory before processing
$memoryLimit = ini_get('memory_limit');
$memoryAvailable = return_bytes($memoryLimit) - memory_get_usage();
logDebug("Memory limit: " . $memoryLimit . ", Available: " . $memoryAvailable . " bytes");

// Create upload directory if it doesn't exist
$uploadDir = 'picupload/';
if (!is_dir($uploadDir)) {
    logDebug("Creating upload directory: " . $uploadDir);
    if (!mkdir($uploadDir, 0755, true)) {
        logDebug("Failed to create directory: " . $uploadDir);
        echo json_encode(['status' => 'error', 'message' => 'Failed to create upload directory']);
        exit;
    }
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'profile_' . uniqid() . '_' . time() . '.' . $extension;
$filePath = $uploadDir . $fileName;

logDebug("Generated filename: " . $fileName);
logDebug("Full file path: " . $filePath);
logDebug("Temp file: " . $file['tmp_name']);

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    logDebug("File moved successfully");
    
    // Resize image to a reasonable size for profile pictures
    try {
        if (resizeImage($filePath, 400, 400)) {
            logDebug("Image resized successfully");
        } else {
            logDebug("Image resize failed - but continuing anyway");
            // Don't fail the upload if resize fails, just log it
        }
    } catch (Exception $e) {
        logDebug("Image resize threw exception: " . $e->getMessage());
        // Don't fail the upload if resize fails, just log it
    } catch (Error $e) {
        logDebug("Image resize threw fatal error: " . $e->getMessage());
        // Don't fail the upload if resize fails, just log it
    }
    
    // Always return success if file was uploaded, even if resize failed
    $fullUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $filePath;
    
    // Clear any output buffer before sending JSON
    if (ob_get_length()) {
        ob_clean();
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'File uploaded successfully',
        'url' => $filePath,
        'full_url' => $fullUrl,
        'filename' => $fileName
    ]);
} else {
    logDebug("Failed to move uploaded file. PHP error: " . (error_get_last()['message'] ?? 'Unknown error'));
    
    // Clear any output buffer before sending JSON
    if (ob_get_length()) {
        ob_clean();
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Failed to save uploaded file']);
}

// Function to resize image with EXIF orientation correction
function resizeImage($imagePath, $maxWidth, $maxHeight) {
    try {
        logDebug("Starting image resize for: " . $imagePath);
        
        if (!file_exists($imagePath)) {
            logDebug("Image file does not exist: " . $imagePath);
            return false;
        }
        
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            logDebug("Failed to get image info for: " . $imagePath);
            return false;
        }
        
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];
        $imageType = $imageInfo[2];
        
        logDebug("Original dimensions: {$originalWidth}x{$originalHeight}, Type: {$imageType}");
        
        // Check if image is extremely large and needs pre-scaling for memory efficiency
        $maxDimension = max($originalWidth, $originalHeight);
        $preScaleNeeded = false;
        $preScaleFactor = 1;
        
        // If image is larger than 3000px in any dimension, pre-scale it
        if ($maxDimension > 3000) {
            $preScaleFactor = 3000 / $maxDimension;
            $preScaleNeeded = true;
            logDebug("Large image detected. Pre-scaling factor: " . $preScaleFactor);
        }
        
        // Estimate memory needed for image processing
        $estimatedMemory = $originalWidth * $originalHeight * 4 * 3; // RGB + Alpha, source + destination + temp
        $availableMemory = return_bytes(ini_get('memory_limit')) - memory_get_usage();
        
        logDebug("Estimated memory needed: " . $estimatedMemory . " bytes, Available: " . $availableMemory . " bytes");
        
        if ($estimatedMemory > $availableMemory * 0.8) {
            // Force pre-scaling to reduce memory usage
            $memoryScaleFactor = sqrt(($availableMemory * 0.6) / ($originalWidth * $originalHeight * 12));
            if ($memoryScaleFactor < $preScaleFactor || !$preScaleNeeded) {
                $preScaleFactor = $memoryScaleFactor;
                $preScaleNeeded = true;
                logDebug("Memory-based pre-scaling factor: " . $preScaleFactor);
            }
        }
        
        // Get EXIF orientation for JPEG images
        $exifOrientation = 1;
        if ($imageType == IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            try {
                $exifData = @exif_read_data($imagePath);
                if ($exifData && isset($exifData['Orientation'])) {
                    $exifOrientation = $exifData['Orientation'];
                    logDebug("EXIF Orientation detected: " . $exifOrientation . " for file: " . $imagePath);
                } else {
                    logDebug("No EXIF orientation data found");
                }
            } catch (Exception $e) {
                logDebug("EXIF reading failed: " . $e->getMessage());
                // Continue without EXIF correction
            }
        }
        
        // Create image resource from file
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $source = @imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_PNG:
                $source = @imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_GIF:
                $source = @imagecreatefromgif($imagePath);
                break;
            default:
                logDebug("Unsupported image type: " . $imageType);
                return false;
        }
        
        if (!$source) {
            logDebug("Failed to create image resource from: " . $imagePath);
            return false;
        }
        
        // Pre-scale if needed to save memory BEFORE orientation correction
        if ($preScaleNeeded) {
            $newPreWidth = (int)($originalWidth * $preScaleFactor);
            $newPreHeight = (int)($originalHeight * $preScaleFactor);
            
            logDebug("Pre-scaling from {$originalWidth}x{$originalHeight} to {$newPreWidth}x{$newPreHeight}");
            
            $preScaledImage = imagecreatetruecolor($newPreWidth, $newPreHeight);
            if (!$preScaledImage) {
                imagedestroy($source);
                throw new Exception("Failed to create pre-scaled image");
            }
            
            // Preserve transparency for PNG
            if ($imageType == IMAGETYPE_PNG) {
                imagealphablending($preScaledImage, false);
                imagesavealpha($preScaledImage, true);
                $transparent = imagecolorallocatealpha($preScaledImage, 255, 255, 255, 127);
                imagefill($preScaledImage, 0, 0, $transparent);
            }
            
            imagecopyresampled($preScaledImage, $source, 0, 0, 0, 0, 
                             $newPreWidth, $newPreHeight, $originalWidth, $originalHeight);
            
            // Replace source with pre-scaled version
            imagedestroy($source);
            $source = $preScaledImage;
            $originalWidth = $newPreWidth;
            $originalHeight = $newPreHeight;
            
            logDebug("Pre-scaling completed. New working dimensions: {$originalWidth}x{$originalHeight}");
        }
        
        // Apply EXIF orientation correction
        try {
            switch ($exifOrientation) {
                case 2: // Horizontal flip
                    if (function_exists('imageflip')) {
                        $source = imageflip($source, IMG_FLIP_HORIZONTAL);
                        logDebug("Applied horizontal flip");
                    }
                    break;
                case 3: // 180° rotation
                    $source = imagerotate($source, 180, 0);
                    logDebug("Applied 180° rotation");
                    break;
                case 4: // Vertical flip
                    if (function_exists('imageflip')) {
                        $source = imageflip($source, IMG_FLIP_VERTICAL);
                        logDebug("Applied vertical flip");
                    }
                    break;
                case 5: // Horizontal flip + 90° counter-clockwise
                    if (function_exists('imageflip')) {
                        $source = imageflip($source, IMG_FLIP_HORIZONTAL);
                    }
                    $source = imagerotate($source, 90, 0);
                    // Swap width and height for 90/270 degree rotations
                    $temp = $originalWidth;
                    $originalWidth = $originalHeight;
                    $originalHeight = $temp;
                    logDebug("Applied horizontal flip + 90° rotation");
                    break;
                case 6: // 90° clockwise (270° counter-clockwise)
                    $source = imagerotate($source, -90, 0);
                    // Swap width and height for 90/270 degree rotations
                    $temp = $originalWidth;
                    $originalWidth = $originalHeight;
                    $originalHeight = $temp;
                    logDebug("Applied 90° clockwise rotation");
                    break;
                case 7: // Horizontal flip + 90° clockwise
                    if (function_exists('imageflip')) {
                        $source = imageflip($source, IMG_FLIP_HORIZONTAL);
                    }
                    $source = imagerotate($source, -90, 0);
                    // Swap width and height for 90/270 degree rotations
                    $temp = $originalWidth;
                    $originalWidth = $originalHeight;
                    $originalHeight = $temp;
                    logDebug("Applied horizontal flip + 90° clockwise rotation");
                    break;
                case 8: // 90° counter-clockwise (270° clockwise)
                    $source = imagerotate($source, 90, 0);
                    // Swap width and height for 90/270 degree rotations
                    $temp = $originalWidth;
                    $originalWidth = $originalHeight;
                    $originalHeight = $temp;
                    logDebug("Applied 90° counter-clockwise rotation");
                    break;
                default:
                    logDebug("No orientation correction needed (orientation: " . $exifOrientation . ")");
            }
        } catch (Exception $e) {
            logDebug("EXIF orientation correction failed: " . $e->getMessage());
            // Continue without orientation correction
        }
        
        // Calculate new dimensions after orientation correction
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = intval($originalWidth * $ratio);
        $newHeight = intval($originalHeight * $ratio);
        
        logDebug("New dimensions: {$newWidth}x{$newHeight} (ratio: {$ratio})");
        
        // Create new image resource
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if (!$newImage) {
            logDebug("Failed to create new image resource");
            imagedestroy($source);
            return false;
        }
        
        // Handle transparency for PNG images
        if ($imageType == IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            logDebug("Enabled transparency for PNG");
        }
        
        // Resize
        if (!imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
            logDebug("Failed to resample image");
            imagedestroy($newImage);
            imagedestroy($source);
            return false;
        }
        
        // Save resized image
        $result = false;
        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($newImage, $imagePath, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($newImage, $imagePath);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($newImage, $imagePath);
                break;
        }
        
        // Clean up
        imagedestroy($newImage);
        imagedestroy($source);
        
        logDebug("Image resize completed. Result: " . ($result ? 'success' : 'failed'));
        return $result;
        
    } catch (Exception $e) {
        logDebug("Exception in resizeImage: " . $e->getMessage());
        return false;
    } catch (Error $e) {
        logDebug("Fatal error in resizeImage: " . $e->getMessage());
        return false;
    }
}
?>
