<?php
/**
 * Image Management Admin Panel
 * Use this to view and manage uploaded profile pictures
 * Uses MSSQL ClientDetailsWebsite table
 */

require_once '../image_manager.php';
// image_manager.php already includes mssql_connection.php and mssql_packages_payments_helper.php

// Simple authentication (you should improve this)
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// For now, we'll just check if accessing from localhost or with a simple password
if (!$isAdmin) {
    if (isset($_POST['admin_password']) && $_POST['admin_password'] === 'synergy_admin_2025') {
        $_SESSION['admin'] = true;
        $isAdmin = true;
    }
}

if (!$isAdmin) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Image Manager - Admin Access</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 50px; background: #f5f5f5; }
            .login-form { max-width: 400px; margin: 100px auto; padding: 30px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
            button { background: #007cba; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; width: 100%; }
            button:hover { background: #005a8b; }
        </style>
    </head>
    <body>
        <div class="login-form">
            <h2>Image Manager Access</h2>
            <form method="post">
                <input type="password" name="admin_password" placeholder="Admin Password" required>
                <button type="submit">Access Image Manager</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$imageManager = new ImageManager();

// Handle actions
$action = $_GET['action'] ?? '';
$message = '';

switch ($action) {
    case 'cleanup':
        $result = $imageManager->cleanupOrphanedImages();
        $message = "Cleanup completed: {$result['deleted_count']} files deleted, {$result['deleted_size']} freed.";
        break;
    case 'delete':
        $filename = $_GET['file'] ?? '';
        if ($filename && $imageManager->deleteImage($filename)) {
            $message = "Image deleted successfully.";
        } else {
            $message = "Failed to delete image.";
        }
        break;
}

// Get storage stats
$stats = $imageManager->getStorageStats();

// Get recent uploads from MSSQL ClientDetailsWebsite
global $mssqlconn;
$sql = "
    SELECT TOP 50 id_number, full_name, picurl, agreement_date 
    FROM ClientDetailsWebsite 
    WHERE picurl IS NOT NULL AND picurl != '' 
    ORDER BY agreement_date DESC
";
$stmt = sqlsrv_query($mssqlconn, $sql);
$recentUploads = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Format the date properly
        if ($row['agreement_date'] instanceof DateTime) {
            $row['agreement_date'] = $row['agreement_date']->format('Y-m-d H:i:s');
        }
        $recentUploads[] = $row;
    }
    sqlsrv_free_stmt($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Manager - Luka Qaliashvili, ID: 0172409681</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f5f5;
        }
        .header {
            background: #000;
            color: #ffdf06;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007cba;
        }
        .actions {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .btn {
            background: #007cba;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn:hover {
            background: #005a8b;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .image-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #f8f9fa;
        }
        .image-info {
            padding: 15px;
        }
        .image-name {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .image-details {
            font-size: 0.9em;
            color: #666;
            line-height: 1.4;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .no-image {
            width: 100%;
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏋️ Luka Qaliashvili, ID: 0172409681 - Image Manager</h1>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_files'] ?></div>
                <div>Total Images</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_size_formatted'] ?></div>
                <div>Storage Used</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($recentUploads) ?></div>
                <div>Users with Photos</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['upload_path'] ?></div>
                <div>Upload Directory</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <h3>Management Actions</h3>
            <a href="?action=cleanup" class="btn btn-danger" 
               onclick="return confirm('This will delete orphaned images not referenced in the database. Continue?')">
                🗑️ Cleanup Orphaned Images
            </a>
            <a href="?" class="btn">🔄 Refresh</a>
            <a href="?action=logout" class="btn">🚪 Logout</a>
        </div>

        <!-- Recent Uploads -->
        <div class="images-grid">
            <?php foreach ($recentUploads as $upload): ?>
                <?php $imageInfo = $imageManager->getImageInfo($upload['picurl']); ?>
                <div class="image-card">
                    <?php if ($imageInfo && $imageManager->imageExists($upload['picurl'])): ?>
                        <img src="<?= htmlspecialchars($imageInfo['url']) ?>" 
                             alt="Profile" class="image-preview"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="no-image" style="display: none;">❌ Image not found</div>
                    <?php else: ?>
                        <div class="no-image">📷 No image or file missing</div>
                    <?php endif; ?>
                    
                    <div class="image-info">
                        <div class="image-name"><?= htmlspecialchars($upload['full_name']) ?></div>
                        <div class="image-details">
                            <strong>ID:</strong> <?= htmlspecialchars($upload['id_number']) ?><br>
                            <strong>File:</strong> <?= htmlspecialchars(basename($upload['picurl'] ?? 'N/A')) ?><br>
                            <strong>Uploaded:</strong> <?= date('Y-m-d H:i', strtotime($upload['agreement_date'])) ?><br>
                            <?php if ($imageInfo): ?>
                                <strong>Size:</strong> <?= $imageInfo['size_formatted'] ?><br>
                                <strong>Dimensions:</strong> <?= $imageInfo['width'] ?>×<?= $imageInfo['height'] ?>px
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($imageInfo && $imageManager->imageExists($upload['picurl'])): ?>
                            <a href="?action=delete&file=<?= urlencode($upload['picurl']) ?>" 
                               class="btn btn-danger" style="margin-top: 10px; font-size: 0.8em;"
                               onclick="return confirm('Delete this image?')">
                                🗑️ Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($recentUploads)): ?>
            <div style="text-align: center; padding: 50px; color: #666;">
                📷 No profile pictures uploaded yet
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
// Handle logout
if ($action === 'logout') {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
