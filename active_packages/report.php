<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../admin'); 
    exit;
}

// Logout logic
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../admin"); 
    exit;
}

include_once '../db_connection.php'; 
include_once '../params.php';
$counts = [
    'today' => 0,
    'this_week' => 0,
    'this_month' => 0,
];
$error_message = null;

if (isset($conn) && !$conn->connect_error) {
    // Today's count
    $sql_today = "SELECT COUNT(*) as count FROM active_promo_sms_logs WHERE DATE(record_date) = CURDATE()";
    $result_today = $conn->query($sql_today);
    if ($result_today) {
        $counts['today'] = $result_today->fetch_assoc()['count'];
    } else {
        $error_message = "დღევანდელი რაოდენობის წამოღების შეცდომა: " . $conn->error;
    }

    // This week's count
    $sql_this_week = "SELECT COUNT(*) as count FROM active_promo_sms_logs WHERE YEARWEEK(record_date, 1) = YEARWEEK(CURDATE(), 1)";
    $result_this_week = $conn->query($sql_this_week);
    if ($result_this_week) {
        $counts['this_week'] = $result_this_week->fetch_assoc()['count'];
    } else {
        $error_message = ($error_message ? $error_message . "<br>" : "") . "ამ კვირის რაოდენობის წამოღების შეცდომა: " . $conn->error;
    }

    // This month's count
    $sql_this_month = "SELECT COUNT(*) as count FROM active_promo_sms_logs WHERE MONTH(record_date) = MONTH(CURDATE()) AND YEAR(record_date) = YEAR(CURDATE())";
    $result_this_month = $conn->query($sql_this_month);
    if ($result_this_month) {
        $counts['this_month'] = $result_this_month->fetch_assoc()['count'];
    } else {
        $error_message = ($error_message ? $error_message . "<br>" : "") . "ამ თვის რაოდენობის წამოღების შეცდომა: " . $conn->error;
    }
    
} else {
    $error_message = "მონაცემთა ბაზასთან კავშირი ვერ მოხერხდა. რეპორტების წამოღება შეუძლებელია.";
    if (isset($conn) && $conn->connect_error) { 
        $error_message .= " შეცდომა: " . $conn->connect_error;
    } else if (!isset($conn)){
        $error_message .= " MySQL კავშირის ობიექტი (\$conn) ვერ მოიძებნა db_connection.php-ში.";
    }
}

$navbarPath = '../components/adminNavbar.php'; 
?>
<!DOCTYPE html>
<html lang="ka">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS გაგზავნის რეპორტი</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { 
            padding-top: 4rem; 
            font-family: 'Inter', sans-serif; 
        }
        .report-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .modal-active { display: flex !important; }
        .modal-inactive { display: none !important; }
        #detailedLogsModalContent table th,
        #detailedLogsModalContent table td {
            padding: 0.75rem; 
            border-bottom-width: 1px; 
            border-color: #E5E7EB; 
        }
        #detailedLogsModalContent table th {
            background-color: #F9FAFB; 
        }
        .pagination-button {
            padding: 0.5rem 0.75rem;
            margin: 0 0.125rem; /* Reduced margin slightly */
            border-radius: 0.375rem;
            transition: background-color 0.2s ease-in-out, color 0.2s ease-in-out;
            font-size: 0.875rem; /* Slightly smaller font for page numbers */
        }
        .pagination-button.active {
            background-color: #3B82F6; /* bg-blue-500 */
            color: white;
            font-weight: 600;
        }
        .pagination-button:not(.active):not(:disabled):hover {
            background-color: #E5E7EB; /* bg-gray-200 */
        }
        .pagination-button:disabled {
            color: #9CA3AF; /* text-gray-400 */
            cursor: not-allowed;
        }
        .pagination-ellipsis {
            padding: 0.5rem 0.3rem;
            color: #6B7280; /* text-gray-500 */
            display: inline-flex;
            align-items: center;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 text-gray-800">
    <?php
    if (file_exists($navbarPath)) {
        include $navbarPath;
    } else {
        echo "<p class='text-red-600 bg-red-100 p-3 text-center fixed top-0 w-full z-10'>ნავიგაციის კომპონენტი ვერ მოიძებნა. გთხოვთ შეამოწმოთ მისამართი.</p>";
    }
    ?>

    <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">SMS გაგზავნის რეპორტი</h1>
            <p class="mt-1 text-sm text-gray-600">გაგზავნილი სარეკლამო SMS-ების მიმოხილვა.</p>
        </header>

        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md shadow-sm" role="alert">
                <div class="flex">
                    <div class="py-1"><svg class="fill-current h-6 w-6 text-red-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 5v6h2V5H9zm0 8h2v2H9v-2z"/></svg></div>
                    <div><p class="font-bold">შეცდომა</p><p><?= $error_message ?></p></div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="report-card bg-white p-6 rounded-xl shadow-lg cursor-pointer hover:shadow-xl" data-period="today" data-period-text="დღეს">
                <h2 class="text-xl font-semibold text-blue-600">დღეს</h2>
                <p class="text-4xl font-bold text-gray-800 mt-2"><?= htmlspecialchars($counts['today']) ?></p>
                <p class="text-sm text-gray-500 mt-1">SMS გაგზავნილია დღეს</p>
            </div>
            <div class="report-card bg-white p-6 rounded-xl shadow-lg cursor-pointer hover:shadow-xl" data-period="this_week" data-period-text="ამ კვირაში">
                <h2 class="text-xl font-semibold text-green-600">ამ კვირაში</h2>
                <p class="text-4xl font-bold text-gray-800 mt-2"><?= htmlspecialchars($counts['this_week']) ?></p>
                <p class="text-sm text-gray-500 mt-1">SMS გაგზავნილია ამ კვირაში (ორშ-კვი)</p>
            </div>
            <div class="report-card bg-white p-6 rounded-xl shadow-lg cursor-pointer hover:shadow-xl" data-period="this_month" data-period-text="ამ თვეში">
                <h2 class="text-xl font-semibold text-purple-600">ამ თვეში</h2>
                <p class="text-4xl font-bold text-gray-800 mt-2"><?= htmlspecialchars($counts['this_month']) ?></p>
                <p class="text-sm text-gray-500 mt-1">SMS გაგზავნილია ამ თვეში</p>
            </div>
        </div>
        <p class="text-sm text-gray-500 text-center italic">დააკლიკეთ ბარათს პერიოდის დეტალური ლოგების სანახავად.</p>
    </div>

    <div id="detailedLogsModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 items-center justify-center modal-inactive z-50 p-4"> 
        <div class="relative mx-auto p-5 sm:p-6 border w-full md:w-3/4 lg:max-w-5xl xl:max-w-6xl shadow-xl rounded-lg bg-white flex flex-col max-h-[90vh] sm:max-h-[85vh]">
            <div class="flex justify-between items-center pb-4 border-b border-gray-200">
                <h3 class="text-2xl leading-6 font-semibold text-gray-900" id="detailedLogsModalTitle">SMS ლოგების დეტალები</h3>
                <button id="closeDetailedLogsModalX" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors duration-150" aria-label="დახურვა">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div id="detailedLogsModalContentWrapper" class="mt-4 flex-grow overflow-y-auto">
                <div id="detailedLogsModalContent" class="text-sm text-gray-700">
                    <p class="text-center py-8 text-gray-500">ლოგები იტვირთება...</p>
                </div>
            </div>
             <div id="paginationControls" class="flex justify-center items-center space-x-1 sm:space-x-1 py-3 border-t border-gray-200">
                </div>
            <div class="flex flex-col sm:flex-row justify-between items-center px-1 pt-4 mt-1 sm:mt-0 border-t border-gray-200 gap-3"> 
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $admin_user_id): ?>
                <button id="exportLogsButton" class="w-full sm:w-auto px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-file-excel mr-2"></i>Excel ექსპორტი 
                </button>
                <?php endif; ?>
                <button id="closeDetailedLogsModalButton" class="w-full sm:w-auto px-6 py-2 bg-gray-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-150">
                    დახურვა
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var currentLogsData = []; 
            var currentPeriodTextForExport = ""; 
            var currentPage = 1;
            var itemsPerPage = 10;

            var $modal = $('#detailedLogsModal');
            var $modalTitle = $('#detailedLogsModalTitle');
            var $modalContent = $('#detailedLogsModalContent');
            var $exportButton = $('#exportLogsButton'); // MODIFIED: Changed ID selector
            var $paginationControls = $('#paginationControls');

            function escapeHtml(text) {
                if (text === null || typeof text === 'undefined') return '';
                var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            function renderTablePage(page) {
                currentPage = page;
                var logsHtml = '<p class="text-center py-8 text-gray-600">ლოგები ვერ მოიძებნა.</p>';
                if (!currentLogsData || currentLogsData.length === 0) {
                    $modalContent.html(logsHtml);
                    updatePaginationControls(); 
                    return;
                }

                var startIndex = (page - 1) * itemsPerPage;
                var endIndex = startIndex + itemsPerPage;
                var pageData = currentLogsData.slice(startIndex, endIndex);

                if (pageData.length > 0) {
                    logsHtml = '<div class="overflow-x-auto"><table class="min-w-full w-full divide-y divide-gray-200 border border-gray-300">';
                    logsHtml += '<thead class="bg-gray-100"><tr class="text-left">';
                    logsHtml += '<th scope="col" class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">კლიენტის ID</th>';
                    logsHtml += '<th scope="col" class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">სრული სახელი</th>';
                    logsHtml += '<th scope="col" class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">მობილურის ნომერი</th>';
                    logsHtml += '<th scope="col" class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">გაგზავნის თარიღი</th>';
                    logsHtml += '</tr></thead><tbody class="bg-white divide-y divide-gray-200">';
                    
                    pageData.forEach(function(log) {
                        logsHtml += '<tr class="hover:bg-gray-50 transition-colors duration-150">';
                        logsHtml += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">' + escapeHtml(log.client_id_num) + '</td>';
                        logsHtml += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">' + escapeHtml(log.full_name || 'N/A') + '</td>';
                        logsHtml += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(log.mobile_number || 'N/A') + '</td>';
                        logsHtml += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + escapeHtml(new Date(log.record_date).toLocaleString('ka-GE', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' })) + '</td>';
                        logsHtml += '</tr>';
                    });
                    logsHtml += '</tbody></table></div>';
                }
                $modalContent.html(logsHtml);
                updatePaginationControls();
            }

            function updatePaginationControls() {
                $paginationControls.empty();
                if (!currentLogsData || currentLogsData.length === 0) return;

                var totalPages = Math.ceil(currentLogsData.length / itemsPerPage);
                if (totalPages <= 1) return;

                var prevDisabled = (currentPage === 1) ? 'disabled' : '';
                var $prevButton = $('<button class="pagination-button" ' + prevDisabled + ' title="წინა"><i class="fas fa-chevron-left"></i></button>');
                $prevButton.on('click', function() { if (currentPage > 1) renderTablePage(currentPage - 1); });
                $paginationControls.append($prevButton);

                let pageSet = new Set();
                pageSet.add(1);
                const pagesAroundCurrent = 1; 
                for (let i = Math.max(1, currentPage - pagesAroundCurrent); i <= Math.min(totalPages, currentPage + pagesAroundCurrent); i++) {
                    pageSet.add(i);
                }
                pageSet.add(totalPages);
                let sortedPages = Array.from(pageSet).sort((a, b) => a - b);
                
                let lastPageAdded = 0;
                sortedPages.forEach(pageNumber => {
                    if (lastPageAdded > 0 && pageNumber - lastPageAdded > 1) {
                        $paginationControls.append('<span class="pagination-ellipsis">...</span>');
                    }
                    var activeClass = (pageNumber === currentPage) ? 'active' : '';
                    var $pageButton = $('<button class="pagination-button ' + activeClass + '">' + pageNumber + '</button>');
                    $pageButton.on('click', (function(pn) {
                        return function() { renderTablePage(pn); };
                    })(pageNumber));
                    $paginationControls.append($pageButton);
                    lastPageAdded = pageNumber;
                });

                var nextDisabled = (currentPage === totalPages) ? 'disabled' : '';
                var $nextButton = $('<button class="pagination-button" ' + nextDisabled + ' title="შემდეგი"><i class="fas fa-chevron-right"></i></button>');
                $nextButton.on('click', function() { if (currentPage < totalPages) renderTablePage(currentPage + 1); });
                $paginationControls.append($nextButton);
            }

            $('.report-card').on('click', function() {
                var period = $(this).data('period');
                var periodTextGeo = $(this).data('period-text'); 
                currentPeriodTextForExport = $(this).find('h2').text().replace(/\s+/g, '_'); 

                $modalTitle.text('SMS ლოგების დეტალები: ' + periodTextGeo);
                $modalContent.html('<p class="text-center py-8 text-gray-500 animate-pulse">ლოგები იტვირთება ' + periodTextGeo + '...</p>');
                $paginationControls.empty(); 
                $exportButton.prop('disabled', true); 
                currentLogsData = []; 
                currentPage = 1;
                $modal.removeClass('modal-inactive').addClass('modal-active');

                $.ajax({
                    url: 'fetch_detailed_sms_logs.php', 
                    type: 'POST', data: { period: period }, dataType: 'json',
                    success: function(response) {
                        if (response && response.status === 'success' && Array.isArray(response.logs)) {
                            currentLogsData = response.logs; 
                            if (response.logs.length > 0) {
                                renderTablePage(1); 
                                $exportButton.prop('disabled', false); 
                            } else {
                                $modalContent.html('<p class="text-center py-8 text-gray-600">SMS ლოგები ვერ მოიძებნა პერიოდისთვის: ' + periodTextGeo + '.</p>');
                                $exportButton.prop('disabled', true);
                                $paginationControls.empty();
                            }
                        } else {
                            $modalContent.html('<p class="text-center py-8 text-red-600">ლოგების წამოღების შეცდომა: ' + escapeHtml(response.message || 'უცნობი შეცდომა სერვერიდან') + '</p>');
                            $exportButton.prop('disabled', true);
                            $paginationControls.empty();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error fetching detailed SMS logs:", status, error, xhr.responseText);
                        $modalContent.html('<p class="text-center py-8 text-red-600">ლოგების წამოღება ვერ მოხერხდა. AJAX შეცდომა: ' + escapeHtml(error) + '</p>');
                        $exportButton.prop('disabled', true);
                        $paginationControls.empty();
                    }
                });
            });

            // MODIFIED EXPORT FUNCTION
            $exportButton.on('click', function() {
                if (currentLogsData.length === 0) {
                    alert("ექსპორტისთვის მონაცემები არ არის.");
                    return;
                }

                // Prepare data in HTML table format for Excel
                let excelData = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                excelData += '<head><meta charset="UTF-8"></head><body>';
                excelData += '<table>';
                
                // Headers
                excelData += '<thead><tr>';
                excelData += '<th>კლიენტის ID</th>';
                excelData += '<th>სრული სახელი</th>';
                excelData += '<th>მობილურის ნომერი</th>';
                excelData += '<th>გაგზავნის თარიღი</th>';
                excelData += '</tr></thead>';
                
                // Body
                excelData += '<tbody>';
                currentLogsData.forEach(function(log) {
                    excelData += '<tr>';
                    // Apply mso-number-format to treat these as text, especially for leading zeros
                    excelData += '<td style="mso-number-format:\'@\';">' + escapeHtml(log.client_id_num || '') + '</td>'; 
                    excelData += '<td>' + escapeHtml(log.full_name || 'N/A') + '</td>';
                    excelData += '<td style="mso-number-format:\'@\';">' + escapeHtml(log.mobile_number || 'N/A') + '</td>';
                    // Format date as YYYY-MM-DD HH:MM:SS for better Excel interpretation
                    var dateSent = log.record_date ? new Date(log.record_date).toISOString().slice(0, 19).replace('T', ' ') : 'N/A';
                    excelData += '<td>' + escapeHtml(dateSent) + '</td>';
                    excelData += '</tr>';
                });
                excelData += '</tbody></table></body></html>';

                var excelContentForLink = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent(excelData);
                
                var link = document.createElement("a");
                link.setAttribute("href", excelContentForLink);
                // Changed file extension to .xls
                var fileName = "sms_logs_" + currentPeriodTextForExport.toLowerCase().replace(/ /g,"_") + "_" + new Date().toISOString().slice(0,10) + ".xls";
                link.setAttribute("download", fileName);
                document.body.appendChild(link); 
                link.click();
                document.body.removeChild(link);
            });

            function closeModal() { 
                $modal.removeClass('modal-active').addClass('modal-inactive'); 
                $modalContent.html('<p class="text-center py-8 text-gray-500">ლოგები იტვირთება...</p>'); 
                $paginationControls.empty(); 
                currentLogsData = []; 
            }
            $('#closeDetailedLogsModalButton, #closeDetailedLogsModalX').on('click', closeModal);
            $(document).on('keydown', function(event) { if (event.key === "Escape" && $modal.hasClass('modal-active')) { closeModal(); } });
            $modal.on('click', function(event) { if ($(event.target).is($modal)) { closeModal(); } });
        });
    </script>
</body>
</html>