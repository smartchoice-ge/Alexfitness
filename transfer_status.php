<?php
require_once 'mssql_connection.php';
require_once 'generate_qr.php';

$lang    = isset($_GET['lang']) && in_array($_GET['lang'], ['en','ru']) ? $_GET['lang'] : 'ka';
$phone   = isset($_GET['phone'])   ? preg_replace('/[^0-9+]/', '', $_GET['phone']) : '';
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

$t = [
    'ka' => [
        'contact'      => 'დაგვიკავშირდით',
        'title'        => 'გადარიცხვის სტატუსი - Alex Fitness',
        'pending_h'    => 'მოთხოვნა გაიგზავნა!',
        'pending_s'    => 'ადმინისტრატორი ამოწმებს გადარიცხვას. გთხოვთ მოიცადოთ.',
        'confirmed_h'  => 'გამოწერა წარმატებით გააქტიურდა!',
        'confirmed_s'  => 'თქვენი გადახდა დადასტურებულია. კეთილი იყოს თქვენი მობრძანება!',
        'rejected_h'   => 'გადარიცხვა ვერ დადასტურდა',
        'rejected_s'   => 'დაგვიკავშირდით: +995 599 061 572',
        'pkg_label'    => 'პაკეტი',
        'until_label'  => 'მოქმედია',
        'qr_label'     => 'თქვენი QR კოდი',
        'qr_hint'      => 'წარმოადგინეთ QR კოდი სალაროსთან',
        'refresh'      => 'განახლება',
        'back'         => 'მთავარზე დაბრუნება',
        'notfound'     => 'მოთხოვნა ვერ მოიძებნა',
        'visits_title' => 'ვიზიტების შეჯამება (ბოლო 3 თვე)',
        'visits_unit'  => 'ვიზიტი',
        'months'       => ['','იანვარი','თებერვალი','მარტი','აპრილი','მაისი','ივნისი','ივლისი','აგვისტო','სექტემბერი','ოქტომბერი','ნოემბერი','დეკემბერი'],
    ],
    'en' => [
        'title'        => 'Transfer Status - Alex Fitness',
        'pending_h'    => 'Request Submitted!',
        'pending_s'    => 'The administrator is verifying your transfer. Please wait.',
        'confirmed_h'  => 'Subscription Activated Successfully!',
        'confirmed_s'  => 'Your payment has been confirmed. Welcome!',
        'rejected_h'   => 'Transfer Not Confirmed',
        'rejected_s'   => 'Contact us: +995 599 061 572',
        'pkg_label'    => 'Package',
        'until_label'  => 'Valid until',
        'qr_label'     => 'Your QR Code',
        'qr_hint'      => 'Show this QR code at the reception',
        'refresh'      => 'Refresh',
        'back'         => 'Back to Home',
        'contact'      => 'Contact us',
        'notfound'     => 'Request not found',
        'visits_title' => 'Visit Summary (Last 3 Months)',
        'visits_unit'  => 'visits',
        'months'       => ['','January','February','March','April','May','June','July','August','September','October','November','December'],
    ],
    'ru' => [
        'title'        => 'Статус перевода - Alex Fitness',
        'pending_h'    => 'Запрос отправлен!',
        'pending_s'    => 'Администратор проверяет перевод. Пожалуйста, подождите.',
        'confirmed_h'  => 'Абонемент успешно активирован!',
        'confirmed_s'  => 'Ваш платёж подтверждён. Добро пожаловать!',
        'rejected_h'   => 'Перевод не подтверждён',
        'rejected_s'   => 'Свяжитесь с нами: +995 599 061 572',
        'pkg_label'    => 'Абонемент',
        'until_label'  => 'Действует до',
        'qr_label'     => 'Ваш QR-код',
        'qr_hint'      => 'Покажите QR-код на ресепшене',
        'refresh'      => 'Обновить',
        'back'         => 'На главную',
        'contact'      => 'Свяжитесь с нами',
        'notfound'     => 'Запрос не найден',
        'visits_title' => 'Статистика посещений (последние 3 месяца)',
        'visits_unit'  => 'посещ.',
        'months'       => ['','Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
    ],
];
$c = $t[$lang];

// Load latest transfer request for this user
$request = null;
if ($mssqlconn && ($phone || $user_id)) {
    $sql = "SELECT TOP 1 id, phone, package_name, amount, status, created_at
            FROM TransferRequests
            WHERE " . ($user_id ? "user_id = ?" : "phone = ?") . "
            ORDER BY created_at DESC";
    $param = $user_id ? [$user_id] : [$phone];
    $st = sqlsrv_query($mssqlconn, $sql, $param);
    if ($st) $request = sqlsrv_fetch_array($st, SQLSRV_FETCH_ASSOC);
}

// Load subscription info and QR if confirmed
$subscription = null;
$qrUrl        = null;
if ($request && $request['status'] === 'confirmed' && $mssqlconn) {
    // Normalize: strip non-digits so '+995...' becomes '995...'
    $p = preg_replace('/[^0-9]/', '', $request['phone']);
    $p995   = strpos($p, '995') === 0 ? $p : '995' . $p;
    $pLocal = strpos($p, '995') === 0 ? substr($p, 3) : $p;

    $clientId = null;
    $cs = sqlsrv_query($mssqlconn, "SELECT ID FROM Clients WHERE Phone=? OR Phone=?", [$p995, $pLocal]);
    if ($cs && ($cr = sqlsrv_fetch_array($cs, SQLSRV_FETCH_ASSOC))) {
        $clientId = $cr['ID'];
        $ss = sqlsrv_query($mssqlconn,
            "SELECT TOP 1 sp.EndDate, pw.name_geo, pw.name_eng
             FROM SoldPackages sp
             LEFT JOIN PackagesWebsite pw ON pw.package_id = sp.PackageID
             WHERE sp.ClientID = ? AND sp.Expired = 0
             ORDER BY sp.ID DESC",
            [$clientId]);
        if ($ss) $subscription = sqlsrv_fetch_array($ss, SQLSRV_FETCH_ASSOC);
    }

    // Visit summary — last 3 calendar months
    $visitCounts = [];
    if ($clientId) {
        $vs = sqlsrv_query($mssqlconn,
            "SELECT YEAR(InDateTame) AS yr, MONTH(InDateTame) AS mo, COUNT(*) AS cnt
             FROM Visits
             WHERE ClientID = ? AND InDateTame >= DATEADD(MONTH, -3, GETDATE())
             GROUP BY YEAR(InDateTame), MONTH(InDateTame)",
            [$clientId]);
        if ($vs) {
            while ($vr = sqlsrv_fetch_array($vs, SQLSRV_FETCH_ASSOC)) {
                $visitCounts[$vr['yr'] . '-' . $vr['mo']] = (int)$vr['cnt'];
            }
        }
    }

    // Generate QR
    try {
        $qrUrl = generateQR($pLocal);
    } catch (Exception $e) {
        $qrUrl = null;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($c['title']) ?></title>
<link rel="shortcut icon" href="img/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<?php if ($request && $request['status'] === 'pending'): ?>
<meta http-equiv="refresh" content="15">
<?php endif; ?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;min-height:100vh;background:#0a0a0a;color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px}
.lang-bar{display:flex;gap:6px;margin-bottom:16px}
.lang-btn{padding:5px 14px;border-radius:6px;font-size:.8rem;font-weight:700;text-decoration:none;border:1px solid rgba(255,255,255,.15);color:#9ca3af;transition:all .2s}
.lang-btn:hover{color:#fff;border-color:rgba(255,255,255,.4)}
.lang-btn.active{background:rgba(34,197,94,.12);border-color:rgba(34,197,94,.4);color:#4ade80}
.card{background:#111;border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:40px 32px;max-width:480px;width:100%;text-align:center}

/* pending */
.icon-pending{width:80px;height:80px;margin:0 auto 24px;border-radius:50%;background:rgba(251,191,36,.12);border:2px solid rgba(251,191,36,.4);display:flex;align-items:center;justify-content:center;font-size:32px;color:#fbbf24}
.card.pending{border-color:rgba(251,191,36,.3)}

/* confirmed */
.icon-confirmed{width:80px;height:80px;margin:0 auto 24px;border-radius:50%;background:linear-gradient(135deg,#15803d,#22c55e,#4ade80);display:flex;align-items:center;justify-content:center;font-size:36px;color:#000;font-weight:900;box-shadow:0 12px 40px rgba(34,197,94,.45);animation:popIn .5s cubic-bezier(.34,1.56,.64,1) both}
@keyframes popIn{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
.card.confirmed{border-color:rgba(34,197,94,.35)}

/* rejected */
.icon-rejected{width:80px;height:80px;margin:0 auto 24px;border-radius:50%;background:rgba(239,68,68,.12);border:2px solid rgba(239,68,68,.4);display:flex;align-items:center;justify-content:center;font-size:32px;color:#f87171}
.card.rejected{border-color:rgba(239,68,68,.3)}

.heading{font-size:clamp(1.3rem,4vw,1.8rem);font-weight:800;margin-bottom:10px;line-height:1.2}
.sub{font-size:.95rem;color:#9ca3af;margin-bottom:28px}

/* Info rows */
.info-box{background:#0d0d0d;border:1px solid rgba(34,197,94,.2);border-radius:12px;padding:16px 20px;margin-bottom:20px;text-align:left}
.info-row{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.05)}
.info-row:last-child{border-bottom:none}
.info-label{font-size:.8rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px}
.info-value{font-size:.95rem;font-weight:700;color:#e5e7eb}

/* QR */
.qr-section{margin:24px 0}
.qr-label{font-size:.8rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.qr-img{width:200px;height:200px;border-radius:12px;border:3px solid rgba(34,197,94,.4);background:#fff;padding:8px;margin:0 auto}
.qr-hint{font-size:.8rem;color:#6b7280;margin-top:10px}

/* Spinner */
.spinner{display:inline-block;width:28px;height:28px;border:3px solid rgba(251,191,36,.3);border-top-color:#fbbf24;border-radius:50%;animation:spin 1s linear infinite;margin-bottom:20px}
@keyframes spin{to{transform:rotate(360deg)}}

/* Visit summary */
.visits-box{background:#0d0d0d;border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:16px 20px;margin-bottom:20px;text-align:left}
.visits-title{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.visit-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.05)}
.visit-row:last-child{border-bottom:none}
.visit-month{font-size:.9rem;color:#d1d5db;font-weight:500}
.visit-count{font-size:.9rem;font-weight:700;color:#e5e7eb}
.visit-count span{color:#4ade80;margin-right:4px}

/* Contact */
.contact-box{margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,.07)}
.contact-label{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.contact-row{display:flex;flex-wrap:wrap;justify-content:center;gap:8px}
.contact-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;font-size:.82rem;font-weight:600;text-decoration:none;border:1px solid transparent;transition:all .2s}
.contact-btn.phone{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);color:#4ade80}
.contact-btn.phone:hover{background:rgba(34,197,94,.18);border-color:#22c55e}
.contact-btn.tg{background:rgba(0,136,204,.1);border-color:rgba(0,136,204,.3);color:#38bdf8}
.contact-btn.tg:hover{background:rgba(0,136,204,.18);border-color:#0088cc}
.contact-btn.fb{background:rgba(24,119,242,.1);border-color:rgba(24,119,242,.3);color:#60a5fa}
.contact-btn.fb:hover{background:rgba(24,119,242,.2);border-color:#1877f2}
.contact-btn.ig{background:rgba(228,64,95,.1);border-color:rgba(228,64,95,.28);color:#f472b6}
.contact-btn.ig:hover{background:rgba(228,64,95,.2);border-color:#e4405f}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-size:.9rem;font-weight:700;text-decoration:none;border:none;cursor:pointer;transition:all .2s;margin:6px}
.btn-green{background:linear-gradient(135deg,#15803d,#22c55e);color:#000;box-shadow:0 4px 16px rgba(34,197,94,.35)}
.btn-green:hover{box-shadow:0 6px 24px rgba(34,197,94,.55);transform:translateY(-2px)}
.btn-outline{background:transparent;color:#9ca3af;border:1px solid rgba(255,255,255,.15)}
.btn-outline:hover{color:#fff;border-color:rgba(255,255,255,.4)}
</style>
</head>
<body>

<?php
$baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
$qsKa   = http_build_query(array_merge($_GET, ['lang' => 'ka']));
$qsEn   = http_build_query(array_merge($_GET, ['lang' => 'en']));
$qsRu   = http_build_query(array_merge($_GET, ['lang' => 'ru']));
$contactHtml =
    '<div class="contact-box">'
    . '<div class="contact-label">' . htmlspecialchars($c['contact']) . '</div>'
    . '<div class="contact-row">'
    . '<a href="tel:+995599061572" class="contact-btn phone"><i class="fas fa-phone-alt"></i>+995 599 061 572</a>'
    . '<a href="https://t.me/alex_fitness_kobuleti" target="_blank" rel="noopener" class="contact-btn tg"><i class="fab fa-telegram-plane"></i>Telegram</a>'
    . '<a href="https://www.facebook.com/Alexfitnesskobulrti/" target="_blank" rel="noopener" class="contact-btn fb"><i class="fab fa-facebook-f"></i>Facebook</a>'
    . '<a href="https://www.instagram.com/alex_fitness_kobuleti/" target="_blank" rel="noopener" class="contact-btn ig"><i class="fab fa-instagram"></i>Instagram</a>'
    . '</div></div>';
?>
<div class="lang-bar">
    <a href="<?= $baseUrl ?>?<?= $qsKa ?>" class="lang-btn <?= $lang==='ka'?'active':'' ?>">ქარ</a>
    <a href="<?= $baseUrl ?>?<?= $qsEn ?>" class="lang-btn <?= $lang==='en'?'active':'' ?>">ENG</a>
    <a href="<?= $baseUrl ?>?<?= $qsRu ?>" class="lang-btn <?= $lang==='ru'?'active':'' ?>">РУС</a>
</div>

<?php if (!$request): ?>
<div class="card">
    <div class="icon-pending"><i class="fas fa-search"></i></div>
    <h1 class="heading"><?= htmlspecialchars($c['notfound']) ?></h1>
    <a href="/" class="btn btn-outline"><i class="fas fa-home"></i><?= htmlspecialchars($c['back']) ?></a>
    <?= $contactHtml ?>
</div>

<?php elseif ($request['status'] === 'pending'): ?>
<div class="card pending">
    <div class="spinner"></div>
    <h1 class="heading"><?= htmlspecialchars($c['pending_h']) ?></h1>
    <p class="sub"><?= htmlspecialchars($c['pending_s']) ?></p>
    <a href="?<?= http_build_query(['phone' => $phone, 'user_id' => $user_id, 'lang' => $lang]) ?>" class="btn btn-outline"><i class="fas fa-sync-alt"></i><?= htmlspecialchars($c['refresh']) ?></a>
    <a href="/" class="btn btn-outline"><i class="fas fa-home"></i><?= htmlspecialchars($c['back']) ?></a>
    <?= $contactHtml ?>
</div>

<?php elseif ($request['status'] === 'confirmed'): ?>
<div class="card confirmed">
    <div class="icon-confirmed">✓</div>
    <h1 class="heading"><?= htmlspecialchars($c['confirmed_h']) ?></h1>
    <p class="sub"><?= htmlspecialchars($c['confirmed_s']) ?></p>

    <?php if ($subscription): ?>
    <div class="info-box">
        <div class="info-row">
            <span class="info-label"><?= htmlspecialchars($c['pkg_label']) ?></span>
            <span class="info-value">
                <?php
                $pkgName = $lang === 'ka' ? ($subscription['name_geo'] ?? '') : ($subscription['name_eng'] ?? '');
                echo htmlspecialchars($pkgName ?: $request['package_name'] ?: '—');
                ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label"><?= htmlspecialchars($c['until_label']) ?></span>
            <span class="info-value" style="color:#4ade80">
                <?php
                $ed = $subscription['EndDate'];
                echo $ed instanceof DateTime ? $ed->format('d.m.Y') : (is_string($ed) ? substr($ed, 0, 10) : '—');
                ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Tel</span>
            <span class="info-value"><?= htmlspecialchars($request['phone']) ?></span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($qrUrl): ?>
    <div class="qr-section">
        <div class="qr-label"><?= htmlspecialchars($c['qr_label']) ?></div>
        <img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR" class="qr-img">
    </div>
    <?php endif; ?>

    <?php if ($clientId !== null): ?>
    <div class="visits-box">
        <div class="visits-title"><?= htmlspecialchars($c['visits_title']) ?></div>
        <?php
        for ($i = 0; $i < 3; $i++) {
            $ts  = mktime(0, 0, 0, (int)date('n') - $i, 1);
            $mo  = (int)date('n', $ts);
            $yr  = (int)date('Y', $ts);
            $cnt = $visitCounts[$yr . '-' . $mo] ?? 0;
            $moName = $c['months'][$mo];
        ?>
        <div class="visit-row">
            <span class="visit-month"><?= htmlspecialchars($moName) ?></span>
            <span class="visit-count"><span><?= $cnt ?></span> <?= htmlspecialchars($c['visits_unit']) ?></span>
        </div>
        <?php } ?>
    </div>
    <?php endif; ?>

    <a href="/" class="btn btn-green"><i class="fas fa-home"></i><?= htmlspecialchars($c['back']) ?></a>
    <?= $contactHtml ?>
</div>

<?php else: /* rejected */ ?>
<div class="card rejected">
    <div class="icon-rejected"><i class="fas fa-times"></i></div>
    <h1 class="heading"><?= htmlspecialchars($c['rejected_h']) ?></h1>
    <p class="sub"><?= htmlspecialchars($c['rejected_s']) ?></p>
    <a href="/" class="btn btn-outline"><i class="fas fa-home"></i><?= htmlspecialchars($c['back']) ?></a>
    <?= $contactHtml ?>
</div>
<?php endif; ?>

</body>
</html>
