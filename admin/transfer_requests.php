<?php
session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Strict']);

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: ../login.php');
    exit;
}

require_once '../mssql_connection.php';
require_once '../mssql_packages_payments_helper.php';

function sendSms(string $phone, string $message): bool {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // DB stores as 995XXXXXXXXX — sender.ge needs just the 9-digit local number
    if (strlen($phone) === 12 && substr($phone, 0, 3) === '995') {
        $phone = substr($phone, 3);
    }
    if (empty($phone)) return false;

    $fields = http_build_query([
        'apikey'      => 'e774aad67ecaba4ba90b86da65be10d9',
        'smsno'       => 2,
        'destination' => $phone,
        'content'     => $message,
    ]);
    $ch = curl_init('https://sender.ge/api/send.php');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $fields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($resp, true);
    return isset($data['data'][0]['statusId']) && $data['data'][0]['statusId'] == 1;
}

function assignSubscription(int $transferId, $conn, int $adminUserId): string {
    // Load transfer request + package info
    $st = sqlsrv_query($conn, "SELECT tr.*, pw.package_id AS crm_pkg_id, pw.duration_month, pw.duration_days, pw.duration_unit, pw.duration_value, pw.price AS pkg_price
        FROM TransferRequests tr
        LEFT JOIN PackagesWebsite pw ON pw.id = tr.package_id
        WHERE tr.id = ?", [$transferId]);
    if (!$st || !($tr = sqlsrv_fetch_array($st, SQLSRV_FETCH_ASSOC))) return 'Transfer request not found';
    if (empty($tr['crm_pkg_id'])) return 'No package linked to this request';

    // Find client in Clients by phone (try both formats)
    $phone = $tr['phone'];
    $p995  = (strpos($phone, '995') === 0) ? $phone : '995' . $phone;
    $pLocal = (strpos($phone, '995') === 0) ? substr($phone, 3) : $phone;
    $cs = sqlsrv_query($conn, "SELECT ID FROM Clients WHERE Phone=? OR Phone=?", [$p995, $pLocal]);
    if (!$cs || !($client = sqlsrv_fetch_array($cs, SQLSRV_FETCH_ASSOC))) return 'Client not found in CRM';
    $clientId = $client['ID'];

    // Duration: calendar-accurate end date from the structured duration
    // (day/week/month/year), falling back to legacy columns inside the helper.
    $pkgForDuration = [
        'duration_unit'  => $tr['duration_unit'] ?? null,
        'duration_value' => $tr['duration_value'] ?? null,
        'duration_days'  => $tr['duration_days'] ?? null,
        'duration_month' => $tr['duration_month'] ?? null,
    ];
    $endDate = addPackageDurationToDate(new DateTime(), $pkgForDuration)->format('Y-m-d H:i:s');

    // Check for existing active subscription
    $es = sqlsrv_query($conn, "SELECT ID, EndDate FROM SoldPackages WHERE ClientID=? AND Expired=0", [$clientId]);
    if ($es && ($existing = sqlsrv_fetch_array($es, SQLSRV_FETCH_ASSOC))) {
        // Expire old and carry over remaining days
        sqlsrv_query($conn, "UPDATE SoldPackages SET Expired=1 WHERE ID=?", [$existing['ID']]);
        $endObj = $existing['EndDate'] instanceof DateTime ? $existing['EndDate'] : new DateTime($existing['EndDate']);
        $remaining = max(0, (new DateTime())->diff($endObj)->days);
        $endObjNew = addPackageDurationToDate(new DateTime(), $pkgForDuration);
        $endObjNew->modify('+' . $remaining . ' day');
        $endDate = $endObjNew->format('Y-m-d H:i:s');
    }

    // Insert SoldPackages — PaymentTypeID 8 = bank transfer
    $ins = sqlsrv_query($conn,
        "INSERT INTO SoldPackages (ClientID,PackageID,SaleTypeID,Price,PayedAmount,PaymentTypeID,SaleParcent,CreatorUserID,VisitsCount,VisitsLeft,EndDate,Expired)
         VALUES (?,?,63,?,?,8,0,?,999,999,?,0)",
        [$clientId, $tr['crm_pkg_id'], $tr['pkg_price'] ?? $tr['amount'], $tr['amount'], $adminUserId, $endDate]
    );
    if ($ins === false) return 'Failed to insert SoldPackages: ' . (sqlsrv_errors()[0]['message'] ?? 'unknown');

    // Update or insert ClientCards
    $cardNum = $pLocal;
    $cc = sqlsrv_query($conn, "SELECT ID FROM ClientCards WHERE ClientID=?", [$clientId]);
    if ($cc && sqlsrv_fetch_array($cc, SQLSRV_FETCH_ASSOC)) {
        sqlsrv_query($conn, "UPDATE ClientCards SET CardNumber=?,ActiveIndevice=1,PaymentTypeID=8 WHERE ClientID=?", [$cardNum, $clientId]);
    } else {
        sqlsrv_query($conn, "INSERT INTO ClientCards (CardNumber,Price,ActiveIndevice,ClientID,PaymentTypeID) VALUES (?,0,1,?,8)", [$cardNum, $clientId]);
    }

    return 'ok';
}

// Handle AJAX actions — return JSON, no redirect
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'])) {
    header('Content-Type: application/json');
    $id     = (int)$_POST['id'];
    $action = $_POST['action'];

    if ($action === 'delete') {
        $st = sqlsrv_query($mssqlconn, "DELETE FROM TransferRequests WHERE id=?", [$id]);
        echo json_encode(['ok' => $st !== false]);
        exit;
    }

    $status = $action === 'confirm' ? 'confirmed' : 'rejected';

    // Fetch request details for SMS
    $row = null;
    $fetch = sqlsrv_query($mssqlconn, "SELECT phone, package_name, amount FROM TransferRequests WHERE id=?", [$id]);
    if ($fetch) { $row = sqlsrv_fetch_array($fetch, SQLSRV_FETCH_ASSOC); sqlsrv_free_stmt($fetch); }

    // On confirm: assign subscription in CRM
    $assignMsg = '';
    if ($status === 'confirmed') {
        $adminId   = $_SESSION['user_id'] ?? 1;
        $assignMsg = assignSubscription($id, $mssqlconn, (int)$adminId);
    }

    // Update status
    $st = sqlsrv_query($mssqlconn, "UPDATE TransferRequests SET status=? WHERE id=?", [$status, $id]);

    // Send SMS
    if ($row && !empty($row['phone'])) {
        $pkg = $row['package_name'] ? ' (' . $row['package_name'] . ' - ' . number_format((float)$row['amount'], 2, '.', '') . '₾)' : '';
        $sms = $status === 'confirmed'
            ? 'Alex Fitness: თქვენი გადარიცხვა დადასტურებულია' . $pkg . '. გმადლობთ!'
            : 'Alex Fitness: სამწუხაროდ, თქვენი გადარიცხვა ვერ დადასტურდა' . $pkg . '. დაგვიკავშირდით: +995 599 061 572';
        sendSms($row['phone'], $sms);
    }

    echo json_encode(['ok' => $st !== false, 'status' => $status, 'assign' => $assignMsg]);
    exit;
}

// Fetch all requests newest first, joined with client info
$rows = [];
$sql = "SELECT tr.id, tr.user_id, tr.phone, tr.package_name, tr.amount, tr.created_at, tr.status,
               c.full_name, c.picurl
        FROM TransferRequests tr
        LEFT JOIN ClientDetailsWebsite c ON c.id = tr.user_id
        ORDER BY tr.created_at DESC";
$result = sqlsrv_query($mssqlconn, $sql);
if ($result) {
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $rows[] = $row;
    }
    sqlsrv_free_stmt($result);
}

$pending = array_filter($rows, fn($r) => $r['status'] === 'pending');
?>
<!DOCTYPE html>
<html lang="ka">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>გადარიცხვის მოთხოვნები</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800">

<?php include '../components/adminNavbar.php'; ?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">გადარიცხვის მოთხოვნები</h1>
        <span id="liveIndicator" style="display:inline-flex;align-items:center;gap:5px;font-size:.72rem;color:#6b7280;margin-left:4px">
            <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;animation:pulse 2s infinite"></span>
            განახლება...
        </span>
        <?php if (count($pending) > 0): ?>
        <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-bold bg-red-100 text-red-700">
            <?= count($pending) ?>
        </span>
        <?php endif; ?>
    </div>

    <div id="requestsArea">
    <?php if (empty($rows)): ?>
    <div class="bg-white rounded-xl shadow-sm p-10 text-center text-gray-400">
        <i class="fas fa-inbox text-4xl mb-3 block"></i>
        მოთხოვნები არ არის
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">კლიენტი</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">ტელეფონი</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">პაკეტი</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">თანხა</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">თარიღი</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">სტატუსი</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">მოქმედება</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            <?php foreach ($rows as $r): ?>
                <?php
                $date = $r['created_at'] instanceof DateTime
                    ? $r['created_at']->format('d.m.Y H:i')
                    : (is_string($r['created_at']) ? substr($r['created_at'], 0, 16) : '—');
                $statusClass = match($r['status']) {
                    'confirmed' => 'bg-green-100 text-green-700',
                    'rejected'  => 'bg-red-100 text-red-700',
                    default     => 'bg-yellow-100 text-yellow-700',
                };
                $statusLabel = match($r['status']) {
                    'confirmed' => 'დადასტურებული',
                    'rejected'  => 'უარყოფილი',
                    default     => 'მოლოდინში',
                };
                ?>
                <tr class="hover:bg-gray-50 transition-colors <?= $r['status'] === 'pending' ? 'font-medium' : '' ?>">
                    <td class="px-4 py-3 text-sm text-gray-500"><?= $r['id'] ?></td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <?php
                            $pic = !empty($r['picurl']) ? '/' . ltrim($r['picurl'], '/') : '';
                            ?>
                            <?php if ($pic): ?>
                                <img src="<?= htmlspecialchars($pic, ENT_QUOTES, 'UTF-8') ?>"
                                     alt=""
                                     style="width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;flex-shrink:0;"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                <div style="display:none;width:44px;height:44px;border-radius:50%;background:#f3f4f6;border:2px solid #e5e7eb;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-user" style="color:#9ca3af;font-size:16px;"></i>
                                </div>
                            <?php else: ?>
                                <div style="width:44px;height:44px;border-radius:50%;background:#f3f4f6;border:2px solid #e5e7eb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-user" style="color:#9ca3af;font-size:16px;"></i>
                                </div>
                            <?php endif; ?>
                            <span class="text-sm font-medium text-gray-800">
                                <?= htmlspecialchars($r['full_name'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?= htmlspecialchars($r['phone'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <?= htmlspecialchars($r['package_name'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold">
                        <?= $r['amount'] ? number_format((float)$r['amount'], 2, '.', '') . ' ₾' : '—' ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= $date ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold <?= $statusClass ?>">
                            <?= $statusLabel ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2 flex-wrap" id="actions-<?= $r['id'] ?>">
                            <?php if ($r['status'] === 'pending'): ?>
                            <button onclick="doAction(<?= $r['id'] ?>,'confirm',this)"
                                class="px-3 py-1 text-xs font-semibold bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-check mr-1"></i>დადასტური
                            </button>
                            <button onclick="doAction(<?= $r['id'] ?>,'reject',this)"
                                class="px-3 py-1 text-xs font-semibold bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-times mr-1"></i>უარყო
                            </button>
                            <?php endif; ?>
                            <button onclick="doAction(<?= $r['id'] ?>,'delete',this)"
                                class="px-3 py-1 text-xs font-semibold bg-gray-200 hover:bg-red-100 text-gray-500 hover:text-red-600 rounded-lg transition-colors">
                                <i class="fas fa-trash mr-1"></i>წაშლა
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    </div><!-- /requestsArea -->

</div>

<style>
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.35}}
@keyframes fadeOut{to{opacity:0;transform:scaleY(0);max-height:0;padding:0;margin:0}}
</style>

<script>
(function () {
    var INTERVAL = 10000;
    function pad(n) { return n < 10 ? '0' + n : n; }
    function setIndicator(text) {
        var el = document.getElementById('liveIndicator');
        if (el) el.lastChild.textContent = ' ' + text;
    }
    function stampNow() {
        var now = new Date();
        setIndicator('განახლდა ' + pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds()));
    }

    // Silent background poll — replace the whole requests area
    function poll() {
        fetch(window.location.pathname, { credentials: 'same-origin' })
            .then(function (r) { return r.text(); })
            .then(function (html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newArea = doc.getElementById('requestsArea');
                var curArea = document.getElementById('requestsArea');
                if (newArea && curArea) curArea.innerHTML = newArea.innerHTML;
                stampNow();
            })
            .catch(function () {});
    }
    setInterval(poll, INTERVAL);
    stampNow();

    // AJAX action — no page reload
    window.doAction = function (id, action, btn) {
        if (action === 'delete' && !confirm('წაიშლება. დარწმუნებული ხართ?')) return;

        // Disable all buttons in this row while working
        var wrap = document.getElementById('actions-' + id);
        var btns = wrap ? wrap.querySelectorAll('button') : [];
        btns.forEach(function (b) { b.disabled = true; b.style.opacity = '.5'; });
        if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'; }

        var fd = new FormData();
        fd.append('id', id);
        fd.append('action', action);

        fetch(window.location.pathname, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.ok) {
                    btns.forEach(function (b) { b.disabled = false; b.style.opacity = ''; });
                    alert('შეცდომა');
                    return;
                }
                if (action === 'delete') {
                    // Fade out and remove row
                    var row = wrap ? wrap.closest('tr') : null;
                    if (row) {
                        row.style.transition = 'opacity .3s';
                        row.style.opacity = '0';
                        setTimeout(function () {
                            row.remove();
                            // If no rows left, show empty state
                            var tb = document.querySelector('tbody');
                            if (tb && tb.querySelectorAll('tr').length === 0) {
                                var tableWrap = document.querySelector('.bg-white.rounded-xl');
                                if (tableWrap) tableWrap.innerHTML = '<div class="p-10 text-center text-gray-400"><i class="fas fa-inbox text-4xl mb-3 block"></i>მოთხოვნები არ არის</div>';
                            }
                        }, 300);
                    }
                } else {
                    // Update status badge and hide confirm/reject buttons
                    var statusMap = { confirmed: ['დადასტურებული','bg-green-100 text-green-700'], rejected: ['უარყოფილი','bg-red-100 text-red-700'] };
                    var info = statusMap[data.status];
                    if (info && wrap) {
                        // Replace status cell
                        var cells = wrap.closest('tr').querySelectorAll('td');
                        var statusCell = cells[cells.length - 2]; // second-to-last = status
                        if (statusCell) statusCell.innerHTML = '<span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold ' + info[1] + '">' + info[0] + '</span>';
                        // Keep only the delete button
                        wrap.innerHTML = '<button onclick="doAction(' + id + ',\'delete\',this)" class="px-3 py-1 text-xs font-semibold bg-gray-200 hover:bg-red-100 text-gray-500 hover:text-red-600 rounded-lg transition-colors"><i class="fas fa-trash mr-1"></i>წაშლა</button>';
                    }
                }
                stampNow();
            })
            .catch(function () {
                btns.forEach(function (b) { b.disabled = false; b.style.opacity = ''; });
                alert('ქსელის შეცდომა');
            });
    };
})();
</script>
</body>
</html>
