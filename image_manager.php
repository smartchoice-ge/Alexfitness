<?php
/**
 * Image Management Utility
 * Handles profile picture operations and cleanup
 */

require_once 'mssql_connection.php';
require_once 'mssql_packages_payments_helper.php';

class ImageManager {
    
    private $uploadBasePath = 'picupload/';
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    private $maxFileSize = 15 * 1024 * 1024; // 15MB
    
    /**
     * Get the full path to an uploaded image
     */
    public function getImagePath($filename) {
        if (empty($filename)) return null;
        
        // If filename already contains the full path, return as is
        if (strpos($filename, $this->uploadBasePath) === 0) {
            return $filename;
        }
        
        // Otherwise, assume it's just the filename and construct the path
        return $this->uploadBasePath . $filename;
    }
    
    /**
     * Get the full URL to an uploaded image
     */
    public function getImageUrl($filename) {
        $path = $this->getImagePath($filename);
        if (!$path || !file_exists($path)) return null;
        
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}/{$path}";
    }
    
    /**
     * Check if image file exists
     */
    public function imageExists($filename) {
        $path = $this->getImagePath($filename);
        return $path && file_exists($path);
    }
    
    /**
     * Delete an image file
     */
    public function deleteImage($filename) {
        $path = $this->getImagePath($filename);
        if ($path && file_exists($path)) {
            return unlink($path);
        }
        return false;
    }
    
    /**
     * Get image information
     */
    public function getImageInfo($filename) {
        $path = $this->getImagePath($filename);
        if (!$path || !file_exists($path)) return null;
        
        $info = getimagesize($path);
        $fileSize = filesize($path);
        
        return [
            'filename' => basename($path),
            'path' => $path,
            'url' => $this->getImageUrl($filename),
            'width' => $info[0] ?? null,
            'height' => $info[1] ?? null,
            'mime_type' => $info['mime'] ?? null,
            'size' => $fileSize,
            'size_formatted' => $this->formatFileSize($fileSize)
        ];
    }
    
    /**
     * Clean up orphaned images (images not referenced in database)
     */
    public function cleanupOrphanedImages() {
        // Get all image filenames from MSSQL ClientDetailsWebsite
        $dbImages = getAllClientProfileImages();
        $dbImageBasenames = array_map('basename', $dbImages);
        
        // Scan upload directory for all images
        $uploadPath = $this->uploadBasePath;
        $deletedCount = 0;
        $deletedSize = 0;
        
        $this->scanDirectoryRecursive($uploadPath, function($filePath) use ($dbImageBasenames, &$deletedCount, &$deletedSize) {
            $filename = basename($filePath);
            
            // Check if this is a profile image and not in database
            if (strpos($filename, 'profile_') === 0 && !in_array($filename, $dbImageBasenames)) {
                $fileSize = filesize($filePath);
                if (unlink($filePath)) {
                    $deletedCount++;
                    $deletedSize += $fileSize;
                }
            }
        });
        
        return [
            'deleted_count' => $deletedCount,
            'deleted_size' => $this->formatFileSize($deletedSize)
        ];
    }
    
    /**
     * Get storage statistics
     */
    public function getStorageStats() {
        $totalSize = 0;
        $totalFiles = 0;
        
        $this->scanDirectoryRecursive($this->uploadBasePath, function($filePath) use (&$totalSize, &$totalFiles) {
            if (is_file($filePath)) {
                $totalSize += filesize($filePath);
                $totalFiles++;
            }
        });
        
        return [
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatFileSize($totalSize),
            'upload_path' => $this->uploadBasePath
        ];
    }
    
    /**
     * Create thumbnail version of image
     */
    public function createThumbnail($filename, $width = 150, $height = 150) {
        $sourcePath = $this->getImagePath($filename);
        if (!$sourcePath || !file_exists($sourcePath)) return false;
        
        $pathInfo = pathinfo($sourcePath);
        $thumbnailPath = $pathInfo['dirname'] . '/thumb_' . $pathInfo['basename'];
        
        return $this->resizeImage($sourcePath, $thumbnailPath, $width, $height);
    }
    
    // Private helper methods
    
    private function scanDirectoryRecursive($dir, $callback) {
        if (!is_dir($dir)) return;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            $callback($file->getPathname());
        }
    }
    
    private function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
    
    private function resizeImage($sourcePath, $destPath, $maxWidth, $maxHeight) {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) return false;
        
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        if ($ratio >= 1) {
            // No need to resize, just copy
            return copy($sourcePath, $destPath);
        }
        
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        
        // Create new image resource
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle transparency for PNG and GIF
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Load original image
        $sourceImage = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }
        
        if (!$sourceImage) return false;
        
        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save resized image
        $result = false;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($newImage, $destPath, 90);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($newImage, $destPath, 9);
                break;
            case IMAGETYPE_GIF:
                $result = imagegif($newImage, $destPath);
                break;
        }
        
        // Clean up
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        
        return $result;
    }
}

// Example usage functions

/**
 * Get user's profile picture URL
 * Uses MSSQL ClientDetailsWebsite table
 */
function getUserProfilePicture($userId) {
    $imageManager = new ImageManager();
    
    // Get client from MSSQL using helper function
    $client = getClientDetailsById($userId);
    
    if ($client && !empty($client['picurl'])) {
        return $imageManager->getImageUrl($client['picurl']);
    }
    
    return null;
}

/**
 * Update user's profile picture
 * Uses MSSQL ClientDetailsWebsite table
 */
function updateUserProfilePicture($userId, $newImagePath) {
    $imageManager = new ImageManager();
    
    // Get current image to delete old one from MSSQL
    $client = getClientDetailsById($userId);
    
    $oldImagePath = null;
    if ($client && !empty($client['picurl'])) {
        $oldImagePath = $client['picurl'];
    }
    
    // Update database with new image using helper
    $result = updateClientProfileImage($userId, $newImagePath);
    
    if ($result) {
        // Delete old image if exists
        if ($oldImagePath && $oldImagePath !== $newImagePath) {
            $imageManager->deleteImage($oldImagePath);
        }
        return true;
    }
    
    return false;
}
?>
