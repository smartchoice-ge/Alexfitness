<?php
require_once 'mssql_connection.php';
require_once 'mssql_packages_payments_helper.php';

$lang    = isset($_GET['lang']) && in_array(strtolower($_GET['lang']), ['en','ru']) ? strtolower($_GET['lang']) : 'ka';
$phone   = isset($_GET['phone'])   ? htmlspecialchars($_GET['phone'],   ENT_QUOTES, 'UTF-8') : '';
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

$packages = getPackagesWebsite();

$t = [
    'ka' => [
        'title'         => 'გადახდა - Alex Fitness',
        'heading'       => 'რეგისტრაცია წარმატებით დასრულდა!',
        'sub'           => 'შეარჩიეთ პაკეტი და გადაიხადეთ ქვემოთ მოცემულ ანგარიშზე.',
        'select_pkg'    => 'აირჩიეთ პაკეტი...',
        'bank_title'    => 'საბანკო გადარიცხვა',
        'bank_sub'      => 'BOG, TBC, Credo',
        'account_label' => 'ანგარიშის ნომერი',
        'amount_label'  => 'გადასახდელი თანხა',
        'select_to_see' => 'შეარჩიეთ პაკეტი გადასახდელი თანხის სანახავად',
        'copied'        => 'დაკოპირდა!',
        'confirm_btn'   => 'გადარიცხვის შემდეგ დააჭირეთ ღილაკს',
        'help_q'        => 'გჭირდებათ დახმარება ან გსურთ განსაკუთრებული შეთავაზება?',
        'help_sub'      => 'დაგვიკავშირდით ნებისმიერ დროს',
        'select_first'  => 'გთხოვთ, ჯერ შეარჩიოთ პაკეტი',
        'month'         => 'თვე',
        'day'           => 'დღე',
        'week'          => 'კვირა',
        'year'          => 'წელი',
        'gel'           => '₾',
    ],
    'en' => [
        'title'         => 'Payment - Alex Fitness',
        'heading'       => 'Registration Completed Successfully!',
        'sub'           => 'Select a package and transfer the amount to our account below.',
        'select_pkg'    => 'Select a package...',
        'bank_title'    => 'Bank Transfer',
        'bank_sub'      => 'BOG, TBC, Credo',
        'account_label' => 'Account number',
        'amount_label'  => 'Amount to transfer',
        'select_to_see' => 'Select a package above to see the transfer amount',
        'copied'        => 'Copied!',
        'confirm_btn'   => 'After transferring, press the button',
        'help_q'        => 'Need help or want a special offer?',
        'help_sub'      => 'Contact us anytime',
        'select_first'  => 'Please select a package first',
        'month'         => 'mo',
        'day'           => 'd',
        'week'          => 'wk',
        'year'          => 'yr',
        'gel'           => '₾',
    ],
    'ru' => [
        'title'         => 'Оплата - Alex Fitness',
        'heading'       => 'Регистрация успешно завершена!',
        'sub'           => 'Выберите абонемент и переведите сумму на счёт ниже.',
        'select_pkg'    => 'Выберите абонемент...',
        'bank_title'    => 'Банковский перевод',
        'bank_sub'      => 'BOG, TBC, Credo',
        'account_label' => 'Номер счёта',
        'amount_label'  => 'Сумма перевода',
        'select_to_see' => 'Выберите абонемент, чтобы увидеть сумму',
        'copied'        => 'Скопировано!',
        'confirm_btn'   => 'После перевода нажмите кнопку',
        'help_q'        => 'Нужна помощь или специальное предложение?',
        'help_sub'      => 'Свяжитесь с нами в любое время',
        'select_first'  => 'Пожалуйста, сначала выберите абонемент',
        'month'         => 'мес',
        'day'           => 'дн',
        'week'          => 'нед',
        'year'          => 'г',
        'gel'           => '₾',
    ],
];
$c = $t[$lang];

$wa_number = '995599061572';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title><?= htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8') ?></title>
<link rel="preload" href="/img/gym.webp" as="image" type="image/webp">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
<link rel="icon"          href="img/favicon.ico" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" defer></script>
<script src="js/language.js" defer></script>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

body{
    font-family:'Inter',sans-serif;
    min-height:100vh;
    background:#0a0a0a;
    color:#fff;
}

/* ── Navbar ── */
.site-navbar{position:fixed;top:0;left:0;width:100%;z-index:9999;background:rgba(8,14,10,0.96);backdrop-filter:blur(14px);border-bottom:1px solid rgba(34,197,94,0.15);box-shadow:0 2px 20px rgba(0,0,0,.5)}
.site-navbar-inner{display:flex;align-items:center;height:72px;padding:0 24px;max-width:1400px;margin:0 auto}
.navbar-logo-link{flex-shrink:0;display:flex;align-items:center;text-decoration:none}
.navbar-logo-img{height:56px;width:auto;display:block;filter:drop-shadow(0 2px 10px rgba(34,197,94,.35))}
.navbar-right-group{flex-shrink:0;display:flex;align-items:center;gap:12px;margin-left:auto}
.navbar-lang-btn{color:#d0d0d0;font-size:.88rem;font-weight:600;padding:7px 14px;border-radius:6px;border:1px solid rgba(74,222,128,.38);background:rgba(74,222,128,.08);cursor:pointer;white-space:nowrap}
.navbar-lang-btn:hover{color:#4ade80;border-color:#4ade80;background:rgba(74,222,128,.16)}
.navbar-hamburger{display:none;flex-direction:column;justify-content:center;gap:5px;width:36px;height:36px;background:none;border:none;cursor:pointer}
.navbar-hamburger span{display:block;width:24px;height:2px;background:#c8c8c8;border-radius:2px}
.navbar-hamburger:hover span{background:#22c55e}
.navbar-mobile-menu{display:none;flex-direction:column;padding:12px 20px 18px;border-top:1px solid rgba(34,197,94,.12);background:rgba(6,12,8,.98);gap:4px}
.navbar-mobile-menu.open{display:flex}
.nm-link{color:#c8c8c8;font-size:1rem;font-weight:600;padding:11px 16px;border-radius:6px;text-decoration:none}
.nm-link:hover{color:#fff;background:rgba(34,197,94,.10)}
.nm-lang{color:#a0a0a0;font-size:.88rem;font-weight:600;padding:10px 16px;border-radius:6px;border:1px solid rgba(34,197,94,.22);text-align:center;margin-top:6px;cursor:pointer}
.nm-lang:hover{color:#22c55e;border-color:#22c55e}
@media(max-width:900px){
    .navbar-hamburger{display:flex}
    .site-navbar-inner{height:60px;padding:0 max(16px,env(safe-area-inset-right,16px)) 0 max(16px,env(safe-area-inset-left,16px))}
    .navbar-logo-img{height:44px}
}

/* ── Page layout ── */
.page-wrap{
    padding-top:72px;
    min-height:100vh;
    background:
        radial-gradient(ellipse 80% 40% at 50% 0%, rgba(34,197,94,0.12) 0%, transparent 70%),
        #0a0a0a;
}
@media(max-width:900px){.page-wrap{padding-top:60px}}

.container{max-width:860px;margin:0 auto;padding:40px 20px 60px}

/* ── Success header ── */
.success-header{text-align:center;padding:40px 0 32px}
.check-circle{
    width:80px;height:80px;margin:0 auto 24px;border-radius:50%;
    background:linear-gradient(135deg,#15803d 0%,#22c55e 50%,#4ade80 100%);
    display:flex;align-items:center;justify-content:center;
    font-size:36px;color:#000;font-weight:900;
    box-shadow:0 12px 40px rgba(34,197,94,0.45);
    animation:popIn .5s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes popIn{from{transform:scale(0);opacity:0}to{transform:scale(1);opacity:1}}
.success-heading{font-size:clamp(1.5rem,4vw,2.2rem);font-weight:800;margin-bottom:10px;line-height:1.15}
.success-sub{color:#9ca3af;font-size:1rem;font-weight:400;max-width:480px;margin:0 auto}

/* ── Package selector ── */
.pkg-select-wrap{margin:0 auto 32px;max-width:480px}
.pkg-select{
    width:100%;padding:14px 18px;
    background:#1a1a1a;border:1px solid rgba(74,222,128,.35);
    color:#fff;border-radius:10px;font-size:1rem;font-weight:500;
    font-family:'Inter',sans-serif;cursor:pointer;
    appearance:none;-webkit-appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2322c55e' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 16px center;padding-right:44px;
    transition:border-color .2s;
}
.pkg-select:focus{outline:none;border-color:#22c55e;box-shadow:0 0 0 3px rgba(34,197,94,.18)}
.pkg-select option{background:#1a1a1a;color:#fff}
.pkg-select.error{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.25);animation:shake .4s cubic-bezier(.36,.07,.19,.97) both}
@keyframes shake{10%,90%{transform:translateX(-3px)}20%,80%{transform:translateX(5px)}30%,50%,70%{transform:translateX(-5px)}40%,60%{transform:translateX(5px)}}

/* ── Bank transfer card ── */
.bank-card{
    background:#111;border:1px solid rgba(34,197,94,.25);
    border-radius:16px;padding:32px 28px;
    max-width:520px;margin:0 auto 28px;
    display:flex;flex-direction:column;gap:22px;
    box-shadow:0 8px 32px rgba(34,197,94,.08);
}
.bank-card-header{display:flex;align-items:center;gap:14px}
.bank-card-icon{
    width:52px;height:52px;border-radius:14px;flex-shrink:0;
    background:rgba(34,197,94,.12);border:1px solid rgba(34,197,94,.25);
    display:flex;align-items:center;justify-content:center;
    font-size:22px;color:#22c55e;
}
.bank-card-title{font-size:1.1rem;font-weight:700;color:#fff}
.bank-card-sub{font-size:.8rem;color:#6b7280;margin-top:2px}

/* Bank logos */
.bank-logos{display:flex;gap:8px;flex-wrap:wrap}
.bank-badge{padding:4px 10px;border-radius:6px;font-size:.72rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase}
.bank-badge.tbc{background:rgba(0,87,184,.25);color:#60a5fa;border:1px solid rgba(0,87,184,.4)}
.bank-badge.bog{background:rgba(231,62,62,.18);color:#f87171;border:1px solid rgba(231,62,62,.35)}
.bank-badge.lib{background:rgba(255,165,0,.15);color:#fbbf24;border:1px solid rgba(255,165,0,.3)}

/* Account number row */
.account-row{background:#0d0d0d;border:1px solid rgba(34,197,94,.2);border-radius:10px;padding:14px 16px}
.account-label{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px}
.account-num-wrap{display:flex;align-items:center;justify-content:space-between;gap:10px}
.account-num{font-size:1.15rem;font-weight:700;color:#4ade80;letter-spacing:1.5px;font-family:monospace}
.copy-btn{
    display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:6px;
    background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);
    color:#22c55e;font-size:.78rem;font-weight:600;cursor:pointer;
    transition:all .2s;white-space:nowrap;
}
.copy-btn:hover{background:rgba(34,197,94,.2);border-color:#22c55e}

/* Amount row */
.amount-row{background:#0d0d0d;border:1px solid rgba(34,197,94,.35);border-radius:10px;padding:14px 16px;display:none}
.amount-row.visible{display:block}
.amount-label{font-size:.75rem;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
.amount-val{font-size:1.6rem;font-weight:800;color:#22c55e}

/* Instruction */
.bank-instruction{font-size:.85rem;color:#6b7280;text-align:center;padding:4px 0}

/* Confirm button */
.confirm-btn{
    display:flex;align-items:center;justify-content:center;gap:10px;
    width:100%;max-width:520px;margin:0 auto 28px;
    padding:15px 20px;border-radius:12px;
    background:linear-gradient(135deg,#15803d 0%,#22c55e 50%,#4ade80 100%);
    color:#000;font-size:1rem;font-weight:800;letter-spacing:.3px;
    border:none;cursor:pointer;
    box-shadow:0 6px 24px rgba(34,197,94,.35);
    transition:all .22s;
}
.confirm-btn:hover:not(:disabled){box-shadow:0 10px 32px rgba(34,197,94,.55);transform:translateY(-2px)}
.confirm-btn:disabled{background:#2a2a2a;color:#4b5563;box-shadow:none;cursor:not-allowed;}

/* ── Help section ── */
.help-box{
    background:#111;border:1px solid rgba(255,255,255,.07);
    border-radius:16px;padding:28px 24px;text-align:center;
}
.help-q{font-size:1rem;font-weight:600;color:#e5e7eb;margin-bottom:6px;line-height:1.4}
.help-sub{font-size:.85rem;color:#6b7280;margin-bottom:20px}
.contact-row{display:flex;flex-wrap:wrap;justify-content:center;gap:10px;margin-bottom:16px}
.contact-btn{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 18px;border-radius:8px;font-size:.88rem;font-weight:600;
    text-decoration:none;border:1px solid transparent;transition:all .22s;
}
.contact-btn.phone{background:rgba(34,197,94,.1);border-color:rgba(34,197,94,.3);color:#4ade80}
.contact-btn.phone:hover{background:rgba(34,197,94,.18);border-color:#22c55e}
.contact-btn.wa{background:rgba(37,211,102,.1);border-color:rgba(37,211,102,.3);color:#25d366}
.contact-btn.wa:hover{background:rgba(37,211,102,.18);border-color:#25d366}
.social-row{display:flex;justify-content:center;gap:10px}
.social-btn{
    display:inline-flex;align-items:center;gap:7px;
    padding:9px 20px;border-radius:8px;font-size:.88rem;font-weight:700;
    text-decoration:none;border:1px solid transparent;transition:all .22s;
}
.social-btn.fb{background:rgba(24,119,242,.12);border-color:rgba(24,119,242,.3);color:#60a5fa}
.social-btn.fb:hover{background:rgba(24,119,242,.22);border-color:#1877f2}
.social-btn.ig{background:rgba(228,64,95,.1);border-color:rgba(228,64,95,.28);color:#f472b6}
.social-btn.ig:hover{background:rgba(228,64,95,.20);border-color:#e4405f}

/* ── Toast ── */
.toast{
    position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(80px);
    background:#1a1a1a;border:1px solid rgba(34,197,94,.4);color:#fff;
    padding:12px 24px;border-radius:10px;font-size:.9rem;font-weight:600;
    z-index:9999;transition:transform .3s;pointer-events:none;
    box-shadow:0 8px 24px rgba(0,0,0,.5);
}
.toast.show{transform:translateX(-50%) translateY(0)}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="site-navbar" id="siteNavbar">
    <div class="site-navbar-inner">
        <a href="/" class="navbar-logo-link">
            <picture>
                <source srcset="img/gym.webp" type="image/webp">
                <img src="img/gym.png" alt="Alex Fitness" class="navbar-logo-img" fetchpriority="high"
                     onerror="this.onerror=null;this.src='https://placehold.co/55x55/0d1a11/22c55e?text=AF';">
            </picture>
        </a>
        <div class="navbar-right-group">
            <div class="navbar-lang-btn" onclick="toggleNavLang();">
                <span name="key_lang">key_lang</span>
            </div>
            <button class="navbar-hamburger" id="navToggle"
                    onclick="document.getElementById('navMobileMenu').classList.toggle('open')">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
    <div class="navbar-mobile-menu" id="navMobileMenu">
        <a href="/" class="nm-link"><i class="fas fa-home" style="margin-right:8px;color:#22c55e"></i> <?= $lang==='ka' ? 'მთავარი' : 'Home' ?></a>
        <div class="nm-lang" onclick="toggleNavLang();">
            <span name="key_lang">key_lang</span>
        </div>
    </div>
</nav>

<div class="page-wrap">
<div class="container">

    <!-- Success header -->
    <div class="success-header">
        <div class="check-circle">✓</div>
        <h1 class="success-heading"><?= htmlspecialchars($c['heading'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="success-sub"><?= htmlspecialchars($c['sub'], ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <!-- Package selector -->
    <div class="pkg-select-wrap">
        <select class="pkg-select" id="pkgSelect">
            <option value=""><?= htmlspecialchars($c['select_pkg'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php foreach ($packages as $pkg):
                $name  = $lang === 'ka' ? htmlspecialchars($pkg['name_geo'] ?? '', ENT_QUOTES, 'UTF-8') : htmlspecialchars($pkg['name_eng'] ?? '', ENT_QUOTES, 'UTF-8');
                $price = htmlspecialchars($pkg['price'] ?? '', ENT_QUOTES, 'UTF-8');
                $durParts = websitePackageDurationParts($pkg);
                $durUnit = $c[$durParts['type']] ?? $c['month'];
                $dur = htmlspecialchars((string)$durParts['value'], ENT_QUOTES, 'UTF-8') . ' ' . $durUnit;
                $label = $name . ' — ' . $price . $c['gel'] . ($dur ? ' / ' . $dur : '');
            ?>
            <option value="<?= $pkg['id'] ?>" data-price="<?= $price ?>" data-name="<?= $name ?>">
                <?= $label ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Bank transfer card -->
    <div class="bank-card">
        <div class="bank-card-header">
            <div class="bank-card-icon"><i class="fas fa-university"></i></div>
            <div>
                <div class="bank-card-title"><?= htmlspecialchars($c['bank_title'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="bank-card-sub"><?= htmlspecialchars($c['bank_sub'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>

        <?php $copyLabel = $lang === 'ka' ? 'კოპირება' : ($lang === 'ru' ? 'Копировать' : 'Copy'); ?>

        <div class="account-row">
            <div class="account-label"><span class="bank-badge bog" style="margin-right:6px">BOG</span><?= htmlspecialchars($c['account_label'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="account-num-wrap">
                <span class="account-num" style="font-size:.95rem">GE66BG0000000538106637</span>
                <button class="copy-btn" onclick="copyText('GE66BG0000000538106637')">
                    <i class="fas fa-copy"></i> <?= $copyLabel ?>
                </button>
            </div>
        </div>

        <div class="account-row">
            <div class="account-label"><span class="bank-badge tbc" style="margin-right:6px">TBC</span><?= htmlspecialchars($c['account_label'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="account-num-wrap">
                <span class="account-num" style="font-size:.95rem">GE67TB7801645164400002</span>
                <button class="copy-btn" onclick="copyText('GE67TB7801645164400002')">
                    <i class="fas fa-copy"></i> <?= $copyLabel ?>
                </button>
            </div>
        </div>

        <div class="account-row">
            <div class="account-label"><span class="bank-badge" style="margin-right:6px;background:rgba(255,140,0,.18);color:#fb923c;border:1px solid rgba(255,140,0,.35)">Credo</span><?= htmlspecialchars($c['account_label'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="account-num-wrap">
                <span class="account-num" style="font-size:.95rem">GE21CD0360000029901791</span>
                <button class="copy-btn" onclick="copyText('GE21CD0360000029901791')">
                    <i class="fas fa-copy"></i> <?= $copyLabel ?>
                </button>
            </div>
        </div>

        <div class="amount-row" id="amountRow">
            <div class="amount-label"><?= htmlspecialchars($c['amount_label'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="amount-val" id="amountVal"></div>
        </div>

        <div class="bank-instruction" id="bankInstruction">
            <?= htmlspecialchars($c['select_to_see'], ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>

    <!-- Confirm transfer button -->
    <button class="confirm-btn" id="confirmBtn" onclick="confirmTransfer()" disabled>
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($c['confirm_btn'], ENT_QUOTES, 'UTF-8') ?>
    </button>

    <!-- Help / contact section -->
    <div class="help-box">
        <div class="help-q"><?= htmlspecialchars($c['help_q'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="help-sub"><?= htmlspecialchars($c['help_sub'], ENT_QUOTES, 'UTF-8') ?></div>
        <div class="contact-row">
            <a href="tel:+995599061572" class="contact-btn phone">
                <i class="fas fa-phone-alt"></i> +995 599 061 572
            </a>
            <a href="https://t.me/alex_fitness_kobuleti"
               target="_blank" rel="noopener" class="contact-btn wa">
                <i class="fab fa-telegram-plane"></i> Telegram
            </a>
        </div>
        <div class="social-row">
            <a href="https://www.facebook.com/Alexfitnesskobulrti/" target="_blank" rel="noopener" class="social-btn fb">
                <i class="fab fa-facebook-f"></i> Facebook
            </a>
            <a href="https://www.instagram.com/alex_fitness_kobuleti/" target="_blank" rel="noopener" class="social-btn ig">
                <i class="fab fa-instagram"></i> Instagram
            </a>
        </div>
    </div>

</div><!-- /container -->
</div><!-- /page-wrap -->

<div class="toast" id="toast"></div>

<script>
const LANG = <?= json_encode($lang) ?>;
const COPIED_MSG = <?= json_encode($c['copied']) ?>;

document.getElementById('pkgSelect').addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    const amountRow = document.getElementById('amountRow');
    const amountVal = document.getElementById('amountVal');
    const instruction = document.getElementById('bankInstruction');
    const confirmBtn = document.getElementById('confirmBtn');

    if (this.value) {
        const price = opt.dataset.price;
        amountVal.textContent = price + ' ₾';
        amountRow.classList.add('visible');
        instruction.style.display = 'none';
        confirmBtn.disabled = false;
    } else {
        amountRow.classList.remove('visible');
        instruction.style.display = '';
        confirmBtn.disabled = true;
    }
});

function confirmTransfer() {
    const sel = document.getElementById('pkgSelect');
    const opt  = sel.value ? sel.options[sel.selectedIndex] : null;
    const btn  = document.querySelector('.confirm-btn');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('transfer_notify.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id:      <?= json_encode($user_id) ?>,
            phone:        <?= json_encode($phone) ?>,
            package_id:   opt ? parseInt(sel.value) : null,
            package_name: opt ? opt.dataset.name  : '',
            amount:       opt ? parseFloat(opt.dataset.price) : 0,
        })
    })
    .then(r => r.json())
    .then(function(data) {
        if (data.status === 'ok') {
            window.location.href = 'transfer_status.php'
                + '?phone='   + encodeURIComponent(<?= json_encode($phone) ?>)
                + '&user_id=' + <?= json_encode($user_id) ?>
                + '&lang='    + LANG;
        } else {
            btn.disabled = false;
            const label = LANG === 'ka' ? 'გადარიცხვის შემდეგ დააჭირეთ ღილაკს'
                        : LANG === 'ru' ? 'После перевода нажмите кнопку'
                        : 'After transferring, press the button';
            btn.innerHTML = '<i class="fas fa-check-circle"></i> ' + label;
            showToast('Error: ' + (data.message || 'unknown'));
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check-circle"></i>';
        showToast('Network error');
    });
}

function copyText(text) {
    navigator.clipboard.writeText(text).then(function () {
        showToast(COPIED_MSG);
    }).catch(function () {
        showToast(text);
    });
}

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

const LANG_LABELS = { ka: 'ქართული', en: 'English', ru: 'Русский' };

function toggleNavLang() {
    const langs = ['ka', 'en', 'ru'];
    const next = langs[(langs.indexOf(LANG) + 1) % langs.length];
    const url = new URL(window.location.href);
    url.searchParams.set('lang', next);
    window.location.href = url.toString();
}

document.querySelectorAll('[name="key_lang"]').forEach(function(el) {
    el.textContent = LANG_LABELS[LANG] || LANG.toUpperCase();
});
</script>
</body>
</html>
