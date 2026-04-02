<?php
session_start([
    'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
    'cookie_samesite' => 'Strict'  // Prevent CSRF
]);

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

// Handle logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../login.php");
    exit;
}

include_once '../mssql_connection.php';
include_once '../params.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Local-first CSS (silent if missing) + non-Cloudflare CDNs -->
    <link rel="stylesheet" type="text/css" href="/vendor/datatables-dt/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="/vendor/datatables-buttons-dt/css/buttons.dataTables.min.css">
    <!-- Prefer non-Cloudflare CDNs for reliability -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@1.13.8/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/datatables.net-buttons-dt@2.4.2/css/buttons.dataTables.min.css">
    <style>
    /* Full-screen loading overlay */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
    }
    /* Avatar and placeholder styling */
    .avatar-thumb { width: 64px; height: 64px; border-radius: 9999px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f3f4f6; border: 2px solid #e5e7eb; }
    .profile-pic { width: 100%; height: 100%; object-fit: cover; display: block; image-orientation: from-image; transition: transform 0.2s ease; }
    .no-photo-placeholder { width: 64px; height: 64px; border-radius: 50%; background-color: #f3f4f6; display: flex; align-items: center; justify-content: center; border: 2px solid #e5e7eb; font-size: 10px; color: #6b7280; text-align: center; }
    .dataTables_wrapper table.dataTable thead th:nth-child(1),
    .dataTables_wrapper table.dataTable tbody td:nth-child(1) {
        width: 84px !important;
        max-width: 84px !important;
        text-align: center;
        white-space: nowrap;
    }
    
    /* Modal image rotation classes */
    #modalImage.rotate-90 {
        transform: rotate(90deg);
    }
    
    #modalImage.rotate-180 {
        transform: rotate(180deg);
    }
    
    #modalImage.rotate-270 {
        transform: rotate(270deg);
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
        include '../components/adminNavbar.php';
    ?>

    <div class="container mx-auto p-4">
        <!-- Export Button -->
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $admin_user_id): ?>
        <button id="exportButton" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Export to Excel</button>
        <?php endif; ?>

        <!-- Client Table -->
        <div class="w-full overflow-x-auto">
            <table id="userTable" class="min-w-full bg-white border border-gray-200 whitespace-nowrap">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-3">სურათი</th>
                        <th class="text-left px-6 py-3">სახელი</th>
                        <th class="text-left px-6 py-3">ID Num</th>
                        <th class="text-left px-6 py-3">ტელეფონი</th>
                        <th class="text-left px-6 py-3">ელ-ფოსტა</th>
                        <th class="text-left px-6 py-3">თარიღი</th>
                        <th class="text-left px-6 py-3">SMS თანხმობა</th>
                        <th class="text-left px-6 py-3">მშობლის თანხმობა</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <!-- jQuery primary (Fastly) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/exif-js@2.3.0/exif.js"></script>
    <!-- Primary script tags (may fail if a CDN is down); robust fallbacks below will handle it -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.7.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.68/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script>
        // Simple function to handle image load events
        function handleImageLoad(event) {
            // Images should now be properly oriented from the server
            // No client-side rotation needed
        }

        // Delegate clicks for dynamically rendered avatars
        document.addEventListener('click', function(e) {
            var trigger = e.target.closest('.modal-trigger');
            if (trigger) {
                var src = decodeURIComponent(trigger.dataset.src || '');
                var name = decodeURIComponent(trigger.dataset.name || '');
                showImageModal(src, name);
            }
        });

        // Ensure mobile menu works - backup implementation
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebar = document.getElementById('sidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');
            
            console.log('Mobile menu elements:', { mobileMenuToggle, sidebar, mobileOverlay });
            
            if (mobileMenuToggle && sidebar && mobileOverlay) {
                mobileMenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Mobile menu toggle clicked');
                    
                    // Use a custom class instead of Tailwind's translate classes for better control
                    const isVisible = sidebar.classList.contains('mobile-show');
                    
                    if (isVisible) {
                        // Hide sidebar
                        sidebar.classList.remove('mobile-show');
                        mobileOverlay.classList.add('hidden');
                        console.log('Hiding sidebar');
                    } else {
                        // Show sidebar
                        sidebar.classList.add('mobile-show');
                        mobileOverlay.classList.remove('hidden');
                        console.log('Showing sidebar');
                    }
                });
                
                mobileOverlay.addEventListener('click', function() {
                    console.log('Overlay clicked - hiding sidebar');
                    sidebar.classList.remove('mobile-show');
                    mobileOverlay.classList.add('hidden');
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024) {
                        sidebar.classList.remove('mobile-show');
                        mobileOverlay.classList.add('hidden');
                        console.log('Desktop view - hiding mobile elements');
                    }
                });
            } else {
                console.error('Mobile menu elements not found:', {
                    mobileMenuToggle: !!mobileMenuToggle,
                    sidebar: !!sidebar, 
                    mobileOverlay: !!mobileOverlay
                });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                "processing": true,
                "serverSide": true,
                "autoWidth": false,
                "scrollX": true,
                "ajax": {
                    "url": "../fetch_data.php",
                    "type": "POST"
                },
                "columns": [
                    { 
                        "data": "picurl",
                        "render": function(data, type, row) {
                            if (data && data.trim() !== '') {
                                var imageSrc = data;
                                if (!data.startsWith('http')) {
                                    imageSrc = '../' + data;
                                }
                                var encName = encodeURIComponent(row.full_name || '');
                                var encSrc = encodeURIComponent(imageSrc);
                                return '<div class="avatar-thumb modal-trigger" data-src="' + encSrc + '" data-name="' + encName + '" role="button" title="გადიდება">'
                                       + '<img src="' + imageSrc + '" alt="Profile" class="profile-pic" loading="lazy" onload="handleImageLoad(event)" onerror="this.parentElement.style.display=\'none\'; this.parentElement.nextSibling.style.display=\'flex\';">'
                                       + '</div>'
                                       + '<div class="no-photo-placeholder" style="display: none;">No Photo</div>';
                            } else {
                                return '<div class="no-photo-placeholder">No Photo</div>';
                            }
                        },
                        "orderable": false
                    },
                    { 
                        "data": "full_name",
                        "render": function(data, type, row) {
                            // Render as a clickable link
                            return type === 'display' ? '<a href="client.php?user_id=' + row.id + '" class="text-blue-500 hover:text-blue-700">' + data + '</a>' : data;
                        }
                    },
                    { "data": "id_number" },
                    { "data": "mobile_number" },
                    { "data": "email" },
                    { 
                        "data": "formatted_agreement_date",
                        "type": "date" // Tell DataTables this is a date column for proper sorting
                    },
                    { "data": "agreed" },
                    { 
                        "data": "parent_agreement",
                        "render": function(data, type, row) {
                            if (data === 'Yes') {
                                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>მშობლის თანხმობა</span>';
                            } else {
                                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">რეგულარული</span>';
                            }
                        },
                        "orderable": false
                    }
                ],
                "order": [[5, 'desc']], // Date column is now index 5
                "columnDefs": [
                    { "orderable": false, "targets": 0 }, // Profile picture column
                    { "orderable": false, "targets": 6 }, // SMS agreement column
                    { "orderable": false, "targets": 7 }  // Parent agreement column
                ]
            });
        });

        // Export to Excel
        $(document).on('click', '#exportButton', async function() {
            // Ensure export libs are present before exporting
            if (!(window.jQuery && $.fn && $.fn.dataTable && $.fn.dataTable.Buttons)) {
                try { await (async () => { const s=document.createElement('script'); s.src='https://cdn.jsdelivr.net/npm/datatables.net-buttons@2.4.2/js/dataTables.buttons.min.js'; document.head.appendChild(s); await new Promise(r=>s.onload=r);} )(); } catch(e) {}
            }
            if (typeof window.XLSX === 'undefined') {
                try { await (async () => { const s=document.createElement('script'); s.src='https://cdn.jsdelivr.net/npm/xlsx@0.17.0/dist/xlsx.full.min.js'; document.head.appendChild(s); await new Promise(r=>s.onload=r);} )(); } catch(e) {}
            }

            var table = $('#userTable').DataTable();
            var data = (table.buttons && table.buttons.exportData) ? table.buttons.exportData({
                modifier: {
                    order: 'index',
                    page: 'all'
                }
            }) : { body: table.rows({ search: 'applied' }).data().toArray() };

            var wb = XLSX.utils.book_new();
            wb.Props = {
                Title: "Client List",
                Subject: "List of Clients",
                Author: "Your Name",
                CreatedDate: new Date()
            };
            wb.SheetNames.push("Clients");
            var ws = XLSX.utils.json_to_sheet(data.body);
            wb.Sheets["Clients"] = ws;
            var wbout = XLSX.write(wb, { bookType: 'xlsx', type: 'binary' });

            saveAs(new Blob([s2ab(wbout)], { type: "application/octet-stream" }), 'client_list.xlsx');
        });

        function s2ab(s) {
            var buf = new ArrayBuffer(s.length);
            var view = new Uint8Array(buf);
            for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
            return buf;
        }

        // Image Modal Functions
        function showImageModal(imageSrc, fullName) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalName = document.getElementById('modalName');
            
            modalImg.src = imageSrc;
            modalName.textContent = fullName;
            modal.style.display = 'flex';
            
            // Images should now be properly oriented from the server
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal when clicking outside the image - wait for DOM to load
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');
            if (imageModal) {
                imageModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeImageModal();
                    }
                });
            }
        });
    </script>

    </div> <!-- Close mainContent -->

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none;">
        <div class="relative max-w-2xl max-h-full p-4">
            <button onclick="closeImageModal()" class="absolute top-2 right-2 text-white text-2xl font-bold bg-black bg-opacity-50 rounded-full w-8 h-8 flex items-center justify-center hover:bg-opacity-75">×</button>
            <img id="modalImage" src="" alt="Profile Picture" class="max-w-full max-h-full rounded-lg" style="image-orientation: from-image;">
            <div class="text-center mt-4">
                <h3 id="modalName" class="text-white text-lg font-semibold"></h3>
            </div>
        </div>
    </div>
</body>

</html>