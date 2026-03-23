<?php
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Strict'
]);

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../admin');
    exit;
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../admin");
    exit;
}

$navbarPath = '../components/adminNavbar.php';

include_once '../params.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>აქტიური პაკეტები</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/vendor/datatables-dt/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@1.13.8/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_processing {
            position: absolute; top: 50%; left: 50%; width: 200px;
            margin-left: -100px; margin-top: -26px; text-align: center;
            padding: 1em 0; border: 1px solid #ddd; background-color: white;
            box-shadow: 0px 0px 5px #ccc; z-index: 1000;
        }
        .modal-active { display: flex !important; }
        .modal-inactive { display: none !important; }
        .filter-row .filter-input-group {
            display: flex;
            align-items: center;
            gap: 0.25rem; 
        }
        .filter-row input[type="text"], .filter-row input[type="number"] {
            flex-grow: 1; 
            padding: 0.25rem 0.5rem; 
            font-size: 0.75rem; 
            border: 1px solid #D1D5DB; 
            border-radius: 0.25rem; 
            min-width: 80px; 
        }
        .filter-row input[type="text"]:focus, .filter-row input[type="number"]:focus {
            outline: none;
            border-color: #4F46E5; 
            box-shadow: 0 0 0 1px #4F46E5; 
        }
        .filter-row .filter-apply-btn {
            padding: 0.25rem 0.5rem; 
            font-size: 0.75rem; 
            border-radius: 0.25rem; 
            white-space: nowrap; 
        }

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
    <?php
    if (file_exists($navbarPath)) {
        include $navbarPath;
    } else {
        echo "<p class='text-red-500 text-center p-4'>Admin navbar component not found.</p>";
    }
    ?>

        <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-gray-700">აქტიური პაკეტები</h1>

        <div class="flex"> 
            <div id="activePackageFiltersContainer" class="mb-4 p-4 bg-gray-50 rounded shadow w-1/2">
                <label for="packageFilterSelect" class="font-semibold text-gray-700">აქტიური პაკეტის მიხედვით გაფილტვრა:</label>
                <div id="activePackageFilterDropdownContainer" class="mt-1">
                </div>
            </div>
            <div class="w-1/2 flex justify-end items-start p-4 space-x-2">
                <a href="/active_packages/report.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    რეპორტი
                </a>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $admin_user_id): ?>
                <button id="exportExcelBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    ექსპორტი Excel
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="w-full overflow-x-auto bg-white p-6 rounded-lg shadow-md">
            <table id="clientListTable" class="min-w-full" style="width:100%">
                <thead class="bg-gray-100 border-b border-gray-300">
                    <tr>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">სახელი</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">ნომერი</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">SMS</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">პაკეტი</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">ვადა მთავრდება</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">დღეები ვადის ამოწურვამდე</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider">რეგისტრაცია</th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider"></th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider"></th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider"></th>
                        <th class="text-left px-4 py-3 text-sm font-medium text-gray-600 uppercase tracking-wider"></th>
                    </tr>
                    <tr class="filter-row bg-gray-50">
                        <th class="p-1"></th> 
                        <th class="p-1"></th> 
                        <th class="p-1"></th>
                        <th class="p-1"></th>
                        <th class="p-1">
                            <div class="filter-input-group">
                                <input type="text" id="activePackageColumnSearch" placeholder="Search Active Pkg..." title="Filter by Active Package name">
                                <button id="applyActivePackageSearch" class="filter-apply-btn bg-green-500 text-white hover:bg-green-600">OK</button>
                            </div>
                        </th>
                        <th class="p-1"></th> 
                        <th class="p-1">
                            <div class="filter-input-group">
                                <input type="number" id="daysUntilExpiryMin" placeholder="From" title="Filter by min days until expiry (e.g., 1)" min="0" class="w-16">
                                <span class="px-1">to</span>
                                <input type="number" id="daysUntilExpiryMax" placeholder="To" title="Filter by max days until expiry (e.g., 10)" min="0" class="w-16">
                                <button id="applyDaysUntilExpirySearch" class="filter-apply-btn bg-green-500 text-white hover:bg-green-600">OK</button>
                            </div>
                        </th>
                        <th class="p-1"></th> <th class="p-1"></th> <th class="p-1"></th> 
                        <th class="p-1"></th> <th class="p-1"></th> 
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div id="smsLogsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full items-center justify-center modal-inactive z-50">
        <div class="relative mx-auto p-5 border w-11/12 md:w-1/2 lg:w-2/5 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl leading-6 font-medium text-gray-900" id="smsLogsModalTitle">SMS Logs</h3>
                <button id="closeSmsLogsModalX" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            <div id="smsLogsModalContent" class="mt-2 px-2 py-3 max-h-80 overflow-y-auto text-sm text-gray-700"><p>Loading logs...</p></div>
            <div class="items-center px-4 py-3 mt-4 text-right">
                <button id="closeSmsLogsModalButton" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">Close</button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
    <script>
        // Ensure jQuery/DataTables load even if a CDN is down
        (function(){
            function loadScript(src){
                return new Promise((resolve,reject)=>{ const s=document.createElement('script'); s.src=src; s.async=true; s.onload=()=>resolve(true); s.onerror=()=>reject(new Error('Failed '+src)); document.head.appendChild(s); });
            }
            async function ensurejQuery(){ if(window.jQuery) return true; try{ await loadScript('/vendor/jquery/jquery-3.6.0.min.js'); }catch(_){} if(!window.jQuery){ try{ await loadScript('https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'); }catch(_){} } if(!window.jQuery){ try{ await loadScript('https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'); }catch(_){} } return !!window.jQuery; }
            async function ensureDataTables(){ if(window.jQuery && $.fn && $.fn.DataTable) return true; try{ await loadScript('/vendor/datatables/jquery.dataTables.min.js'); }catch(_){} if(!(window.jQuery && $.fn && $.fn.DataTable)){ try{ await loadScript('https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js'); }catch(_){} } return (window.jQuery && $.fn && $.fn.DataTable); }

            async function boot(){
                await ensurejQuery();
                await ensureDataTables();
                if(!(window.jQuery && $.fn && $.fn.DataTable)){
                    console.error('DataTables failed to load; table cannot initialize.');
                    return;
                }

                // Original init code follows
            var currentSelectedActivePackage = '';
            var activePackageColumnSearchValue = '';
            var daysUntilExpirySearchValue = '';

            // Handle Excel export
            $('#exportExcelBtn').on('click', function() {
                // Create URLSearchParams for more robust parameter handling
                const params = new URLSearchParams();
                params.append('export', 'excel');
                
                // Add all current filters
                if (currentSelectedActivePackage) {
                    params.append('selectedActivePackage', currentSelectedActivePackage);
                }
                if (activePackageColumnSearchValue) {
                    params.append('activePackageColumnSearch', activePackageColumnSearchValue);
                }
                if (daysUntilExpirySearchValue) {
                    if (typeof daysUntilExpirySearchValue === 'object') {
                        // For range filters, ensure both min and max are passed as an array
                        params.append('daysUntilExpiryMax[min]', daysUntilExpirySearchValue.min || '');
                        params.append('daysUntilExpiryMax[max]', daysUntilExpirySearchValue.max || '');
                    } else {
                        params.append('daysUntilExpiryMax', daysUntilExpirySearchValue);
                    }
                }
                
                window.location.href = 'back.php?' + params.toString();
            });

            function escapeHtml(text) {
                if (text === null || typeof text === 'undefined') return '';
                var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
                return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
            }

            var table = $('#clientListTable').DataTable({
                "processing": true,
                "serverSide": true,
                "autoWidth": false,
                "scrollX": true,
                "ajax": {
                    "url": "back.php",
                    "type": "POST",
                    "data": function(d) {
                        d.selectedActivePackage = currentSelectedActivePackage;
                        d.activePackageColumnSearch = activePackageColumnSearchValue;
                        d.daysUntilExpiryMax = daysUntilExpirySearchValue;
                    },
                    "dataSrc": function(json) {
                        var filterDropdownContainer = $('#activePackageFilterDropdownContainer');
                        if (!json || typeof json.data === 'undefined' || !Array.isArray(json.data)) {
                            console.error("AJAX response data is missing or malformed for table data.", json);
                            if (json && json.error) alert("Server error fetching table data: " + json.error);
                            return [];
                        }
                        if (filterDropdownContainer.find('select.package-filter-select').length === 0) {
                            var selectHtml = '<select id="packageFilterSelect" class="package-filter-select block w-full md:w-auto lg:w-1/3 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">';
                            selectHtml += '<option value="">All Packages</option>';
                            if (json.uniqueActivePackageNames && Array.isArray(json.uniqueActivePackageNames)) {
                                json.uniqueActivePackageNames.forEach(function(pkgName) {
                                    if (pkgName && String(pkgName).trim() !== '') {
                                        selectHtml += '<option value="' + escapeHtml(pkgName) + '">' + escapeHtml(pkgName) + '</option>';
                                    }
                                });
                            }
                            selectHtml += '</select>';
                            filterDropdownContainer.html(selectHtml);
                        }
                        return json.data;
                    },
                    "error": function(xhr, error, thrown) {
                        var errorMsg = "Error fetching table data. Status: " + xhr.status + " (" + thrown + "). ";
                        console.error("DataTables AJAX error (fetching table data):", error, "Type:", thrown, "XHR:", xhr);
                        if (xhr.responseJSON && xhr.responseJSON.error) { errorMsg += "Server error: " + xhr.responseJSON.error;
                        } else if (xhr.responseText) {
                            try { var response = JSON.parse(xhr.responseText); if (response && response.error) { errorMsg += "Server detail: " + response.error;} else { errorMsg += "Unexpected server response format.";}}
                            catch (e) { errorMsg += "Server returned a non-JSON response.";}
                            console.error("Raw Server Response Text (fetching table data):", xhr.responseText);
                        } else { errorMsg += "No further details from server.";}
                        alert(errorMsg);
                    }
                },
                "columns": [
                    { "data": "FullName", "render": function(data, type, row) { if (type === 'display' && row.MySqlClientId) { return '<a href="/admin/client.php?user_id=' + row.MySqlClientId + '" class="text-blue-600 hover:text-blue-800 hover:underline">' + escapeHtml(data) + '</a>'; } return escapeHtml(data); }},
                    { "data": "IdNumber" }, 
                    { "data": "Phone" }, 
                    { "data": "SendSms", "render": function(data) { return data ? 'Yes' : 'No'; } },
                    { "data": "ActivePackage" },
                    { "data": "ActivePackageExpiryDate", "render": function(data) { return data ? data : '-'; } },
                    { "data": "DaysUntilExpiry", "render": function(data) { return data !== null ? data : '-'; } },
                    { "data": "ClientRecordDate", "render": function(data) { if (data) { return new Date(data).toLocaleString(undefined, { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' }); } return '-'; } },
                    { "data": "LastSmsSentDate", "title": "ბოლო SMS", "orderable": true, "searchable": false, "render": function(data) { if (data) { return new Date(data).toLocaleString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }); } return 'N/A'; } },
                    { "data": null, "title": "პრომო ENG", "orderable": false, "searchable": false, "className": "text-center", "render": function(data, type, row) { return '<button class="send-promo-sms-btn bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded text-xs" data-phone="' + escapeHtml(row.Phone || '') + '" data-fullname="' + escapeHtml(row.FullName || 'Client') + '" data-idnum="' + escapeHtml(row.IdNumber || '') + '">Send ENG</button>'; } },
                    { "data": null, "title": "პრომო GEO", "orderable": false, "searchable": false, "className": "text-center", "render": function(data, type, row) { return '<button class="send-promo-sms-geo-btn bg-purple-500 hover:bg-purple-700 text-white font-bold py-1 px-2 rounded text-xs" data-phone="' + escapeHtml(row.Phone || '') + '" data-fullname="' + escapeHtml(row.FullName || 'Client') + '" data-idnum="' + escapeHtml(row.IdNumber || '') + '">Send GEO</button>'; } },
                    { "data": null, "title": "SMS ლოგები", "orderable": false, "searchable": false, "className": "text-center", "render": function(data, type, row) { return '<button class="view-sms-logs-btn bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-xs" data-idnum="' + escapeHtml(row.IdNumber || '') + '" data-fullname="' + escapeHtml(row.FullName || 'Client') +'">View Logs</button>'; } }
                ],
                "order": [ [4, 'asc'] ]
            });

            // ----- FILTER EVENT LISTENERS -----
            $('#activePackageFiltersContainer').on('change', 'select.package-filter-select', function() {
                currentSelectedActivePackage = $(this).val();
                table.ajax.reload(null, true); 
            });

            $('#applyActivePackageSearch').on('click', function() {
                activePackageColumnSearchValue = $('#activePackageColumnSearch').val();
                table.ajax.reload(null, true); 
            });
            $('#activePackageColumnSearch').on('keypress', function(e) { if (e.which == 13) { $('#applyActivePackageSearch').click(); } });

            $('#applyDaysUntilExpirySearch').on('click', function() {
                var minVal = $('#daysUntilExpiryMin').val();
                var maxVal = $('#daysUntilExpiryMax').val();
                daysUntilExpirySearchValue = (minVal || maxVal) ? {min: minVal, max: maxVal} : '';
                table.ajax.reload(null, true); 
            });
            $('#daysUntilExpiryMin, #daysUntilExpiryMax').on('keypress', function(e) { 
                if (e.which == 13) { $('#applyDaysUntilExpirySearch').click(); } 
            });

            // ----- SMS AND MODAL LOGIC -----
            function handleSmsSend($button, phoneFromRow, fullNameFromRow, idNumberFromRow, ajaxUrl) {
                if (!phoneFromRow || !fullNameFromRow || !idNumberFromRow) { alert('Error: Missing phone, name, or ID for this client.'); $button.prop('disabled', false); return; }
                var firstName = String(fullNameFromRow).split(' ')[0];
                var processedMobileNumber = String(phoneFromRow);
                if (processedMobileNumber.startsWith('995') && processedMobileNumber.length === 12) { processedMobileNumber = processedMobileNumber.substring(3); }
                var originalButtonText = $button.text();
                var originalButtonClasses = $button.attr('class');
                $button.prop('disabled', true).text('Sending...').removeClass('bg-purple-500 bg-teal-500 bg-green-500 bg-red-500').addClass('bg-gray-400');
                $.ajax({
                    url: ajaxUrl, type: 'POST', data: { firstName: firstName, mobileNumber: processedMobileNumber, clientIdNum: idNumberFromRow }, dataType: 'json',
                    success: function(response) {
                        var alertMessage = '';
                        if (response && (response.status === 'success' || response.status === 'api_logic_error')) {
                             alertMessage = (response.message || 'API call processed.') + '\nAPI HTTP: ' + (response.api_response_http_code || 'N/A') + '\nAPI Body: ' + (response.api_response_body || '(empty)');
                            if (response.status === 'success') { $button.text('Sent').removeClass('bg-gray-400').addClass('bg-green-500');
                            } else { $button.text('Retry').removeClass('bg-gray-400').addClass('bg-red-500'); }
                        } else if (response && response.message) { alertMessage = 'Error: ' + response.message; $button.text('Retry').removeClass('bg-gray-400').addClass('bg-red-500');
                        } else { alertMessage = 'An unknown response was received from the server.'; $button.text('Retry').removeClass('bg-gray-400').addClass('bg-red-500'); }
                        alert(alertMessage); console.log("Full response from " + ajaxUrl + ":", response);
                    },
                    error: function(xhr, status, error) {
                        alert('AJAX Error: Could not contact server to send SMS via ' + ajaxUrl + '. ' + error);
                        console.error("AJAX error for " + ajaxUrl + ":", status, error, xhr.responseText);
                        $button.text('Retry').removeClass('bg-gray-400').addClass('bg-red-500');
                    },
                    complete: function() {
                        if (!$button.hasClass('bg-green-500')) {
                            setTimeout(function() { $button.prop('disabled', false).attr('class', originalButtonClasses).text(originalButtonText); }, 3000);
                        }
                    }
                });
            }
            $('#clientListTable tbody').on('click', '.send-promo-sms-btn', function() { handleSmsSend($(this), $(this).data('phone'), $(this).data('fullname'), $(this).data('idnum'), 'send_promo_sms.php'); });
            $('#clientListTable tbody').on('click', '.send-promo-sms-geo-btn', function() { handleSmsSend($(this), $(this).data('phone'), $(this).data('fullname'), $(this).data('idnum'), 'send_promo_sms_geo.php'); });
            
            var $modal = $('#smsLogsModal'); var $modalTitle = $('#smsLogsModalTitle'); var $modalContent = $('#smsLogsModalContent');
            $('#clientListTable tbody').on('click', '.view-sms-logs-btn', function() {
                var idNum = $(this).data('idnum'); var clientName = $(this).data('fullname');
                $modalTitle.text('SMS ლოგები ' + escapeHtml(clientName) + ' (ID: ' + escapeHtml(idNum) + ')');
                $modalContent.html('<p class="text-center py-4">Loading logs...</p>');
                $modal.removeClass('modal-inactive').addClass('modal-active');
                $.ajax({
                    url: 'fetch_sms_logs.php', type: 'POST', data: { clientIdNum: idNum }, dataType: 'json',
                    success: function(response) {
                        if (response && response.status === 'success' && Array.isArray(response.logs)) {
                            if (response.logs.length > 0) {
                                var logsHtml = '<ul class="list-disc pl-5 space-y-1">';
                                response.logs.forEach(function(log) { logsHtml += '<li>' + escapeHtml(new Date(log.record_date).toLocaleString()) + '</li>'; });
                                logsHtml += '</ul>'; $modalContent.html(logsHtml);
                            } else { $modalContent.html('<p class="text-center py-4">ლოგები ცარიელია.</p>'); }
                        } else { $modalContent.html('<p class="text-center py-4 text-red-500">Error fetching logs: ' + escapeHtml(response.message || 'Unknown error') + '</p>'); }
                    },
                    error: function(xhr, status, error) { console.error("AJAX error fetching SMS logs:", status, error, xhr.responseText); $modalContent.html('<p class="text-center py-4 text-red-500">Could not fetch logs. AJAX Error: ' + escapeHtml(error) + '</p>'); }
                });
            });
            $('#closeSmsLogsModalButton, #closeSmsLogsModalX').on('click', function() { $modal.removeClass('modal-active').addClass('modal-inactive'); });
            $(document).on('keydown', function(event) { if (event.key === "Escape" && $modal.hasClass('modal-active')) { $modal.removeClass('modal-active').addClass('modal-inactive'); } });
            $modal.on('click', function(event) { if ($(event.target).is($modal)) { $modal.removeClass('modal-active').addClass('modal-inactive'); } });
            }

            // Start after DOM is ready
            document.addEventListener('DOMContentLoaded', boot);
        })();
    </script>

    </div> <!-- Close mainContent -->
</body>
</html>
