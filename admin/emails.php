<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

include('../db_connection.php');

// Pagination settings
$limit = 30; // Number of emails per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;

// Search filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// SQL query with search filter
$sql = "SELECT `email`, `date`, `content` FROM emails";
if (!empty($search)) {
    $sql .= " WHERE email LIKE ? OR content LIKE ?";
}
$sql .= " ORDER BY `date` DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
} else {
    $stmt->bind_param("ii", $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$emails = [];
while ($row = $result->fetch_assoc()) {
    $emails[] = $row;
}
$stmt->close();

// Total emails count for pagination
$countQuery = "SELECT COUNT(*) AS total FROM emails";
if (!empty($search)) {
    $countQuery .= " WHERE email LIKE ? OR content LIKE ?";
}
$countStmt = $conn->prepare($countQuery);

if (!empty($search)) {
    $countStmt->bind_param("ss", $searchParam, $searchParam);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalEmails = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalEmails / $limit);
$countStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sent Emails</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Mobile menu button positioning fix */
        @media (max-width: 1023px) {
            #mobileMenuToggle {
                position: fixed !important;
                top: 1rem !important;
                left: 1rem !important;
                z-index: 50 !important;
                display: block !important;
            }
            
            /* Ensure main content has proper padding on mobile */
            .container {
                padding-top: 4rem !important;
            }
            
            /* Ensure sidebar is properly positioned on mobile */
            #sidebar {
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                height: 100vh !important;
                width: 16rem !important;
                z-index: 30 !important;
                transition: transform 0.3s ease-in-out !important;
                transform: translateX(-100%) !important; /* Hide by default */
            }
            
            /* When sidebar should be visible on mobile */
            #sidebar.mobile-show {
                transform: translateX(0) !important;
            }
            
            /* Show overlay when sidebar is open */
            #mobileOverlay {
                position: fixed !important;
                inset: 0 !important;
                background-color: rgba(0, 0, 0, 0.5) !important;
                z-index: 20 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include '../components/adminNavbar.php'; ?>

        <div class="container mx-auto p-4">
        <h1 class="text-xl font-bold mb-4">Sent Emails</h1>

        <!-- Search Form -->
        <form method="GET" class="mb-4">
            <input type="text" name="search" placeholder="Search by email or content" value="<?= htmlspecialchars($search) ?>"
                   class="p-2 border rounded-md w-64">
            <button type="submit" class="p-2 bg-blue-500 text-white rounded-md">Search</button>
        </form>

        <!-- Emails Table -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Date</th>
                        <th class="p-3 text-left">Content</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($emails)): ?>
                        <?php foreach ($emails as $email): ?>
                            <tr class="border-t">
                                <td class="p-3"><?= htmlspecialchars($email['email']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($email['date']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($email['content']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="p-3 text-center">No emails found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            <?php if ($totalPages > 1): ?>
                <nav class="flex justify-center">
                    <ul class="inline-flex items-center -space-x-px text-sm">

                        <li>
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
                            <?php else: ?>
                                <span class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-400 bg-white border border-e-0 border-gray-300 rounded-s-lg cursor-not-allowed">Previous</span>
                            <?php endif; ?>
                        </li>

                        <?php
                        $range = 2; // How many page links to show around the current page
                        $showEllipsisStart = false;
                        $showEllipsisEnd = false;

                        // Define the range of page numbers to display
                        $start = max(1, $page - $range);
                        $end = min($totalPages, $page + $range);

                        // Decide if ellipses are needed
                        if ($start > 2) { // Need ellipsis after page 1?
                            $showEllipsisStart = true;
                        }
                        if ($end < $totalPages - 1) { // Need ellipsis before last page?
                            $showEllipsisEnd = true;
                        }

                        // Always show page 1 unless it's the only page
                        if ($totalPages > 1) {
                             $activeClass = ($page == 1) ? 'text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700';
                             echo '<li><a href="?page=1&search=' . urlencode($search) . '" class="flex items-center justify-center px-3 h-8 leading-tight border ' . $activeClass . '">1</a></li>';
                        }


                        // Show starting ellipsis if needed
                        if ($showEllipsisStart) {
                            echo '<li><span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300">...</span></li>';
                        }

                        // Show page numbers within the calculated range, avoiding duplicates of 1 and last page if ellipsis are shown
                        $loopStart = $showEllipsisStart ? max(2, $start) : max(2, $start);
                        $loopEnd = $showEllipsisEnd ? min($totalPages - 1, $end) : min($totalPages - 1, $end);

                        for ($i = $loopStart; $i <= $loopEnd; $i++) {
                            $activeClass = ($page == $i) ? 'text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700';
                            echo '<li><a href="?page=' . $i . '&search=' . urlencode($search) . '" class="flex items-center justify-center px-3 h-8 leading-tight border ' . $activeClass . '">' . $i . '</a></li>';
                        }

                        // Show ending ellipsis if needed
                        if ($showEllipsisEnd) {
                             echo '<li><span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300">...</span></li>';
                        }

                        // Always show the last page, unless it's page 1 or already shown in the loop
                        if ($totalPages > 1 && $end < $totalPages) {
                             $activeClass = ($page == $totalPages) ? 'text-blue-600 border-blue-300 bg-blue-50 hover:bg-blue-100 hover:text-blue-700' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700';
                            echo '<li><a href="?page=' . $totalPages . '&search=' . urlencode($search) . '" class="flex items-center justify-center px-3 h-8 leading-tight border ' . $activeClass . '">' . $totalPages . '</a></li>';
                        }
                        ?>

                        <li>
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
                            <?php else: ?>
                                 <span class="flex items-center justify-center px-3 h-8 leading-tight text-gray-400 bg-white border border-gray-300 rounded-e-lg cursor-not-allowed">Next</span>
                            <?php endif; ?>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            if (mobileMenuToggle && sidebar && mobileOverlay) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const isVisible = sidebar.classList.contains('mobile-show');
                    
                    if (isVisible) {
                        sidebar.classList.remove('mobile-show');
                        mobileOverlay.classList.add('hidden');
                    } else {
                        sidebar.classList.add('mobile-show');
                        mobileOverlay.classList.remove('hidden');
                    }
                });
                
                mobileOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('mobile-show');
                    mobileOverlay.classList.add('hidden');
                });
                
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024) {
                        sidebar.classList.remove('mobile-show');
                        mobileOverlay.classList.add('hidden');
                    }
                });
            }
        });
    </script>

    </div> <!-- Close mainContent -->
</body>
</html>
