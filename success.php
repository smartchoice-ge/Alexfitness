<?php
$lang = isset($_GET['lang']) && strtolower($_GET['lang']) === 'en' ? 'en' : 'ka';

$copy = [
    'ka' => [
        'title' => 'რეგისტრაცია დასრულდა - Luka Qaliashvili, ID: 0172409681',
        'heading' => 'რეგისტრაცია წარმატებით დასრულდა',
    ],
    'en' => [
        'title' => 'Registration Complete - Luka Qaliashvili, ID: 0172409681',
        'heading' => 'Registration Completed Successfully',
    ],
];

$text = $copy[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            margin: 0;
            background:
                radial-gradient(circle at top, rgba(200, 230, 0, 0.18), transparent 35%),
                linear-gradient(180deg, #050505 0%, #0f0f0f 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 640px;
            background: rgba(17, 17, 17, 0.94);
            border: 1px solid rgba(200, 230, 0, 0.28);
            border-radius: 24px;
            padding: 40px 28px;
            text-align: center;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.45);
        }

        .logo {
            height: 60px;
            width: auto;
            margin: 0 auto 28px;
            display: block;
        }

        .icon {
            width: 92px;
            height: 92px;
            margin: 0 auto 24px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #c8e600 0%, #93c000 100%);
            color: #000;
            font-size: 42px;
            font-weight: 700;
            box-shadow: 0 16px 36px rgba(200, 230, 0, 0.28);
        }

        h1 {
            margin: 0 0 16px;
            font-size: clamp(30px, 5vw, 42px);
            line-height: 1.1;
        }

        @media (max-width: 640px) {
            .card {
                padding: 32px 20px;
            }
        }
    </style>
</head>
<body>
    <main class="card">
        <img src="img/logo.png" alt="Luka Qaliashvili, ID: 0172409681" class="logo" onerror="this.onerror=null;this.src='https://placehold.co/180x60/cccccc/000000?text=Synergy';">
        <div class="icon">✓</div>
        <h1><?php echo htmlspecialchars($text['heading'], ENT_QUOTES, 'UTF-8'); ?></h1>
    </main>
</body>
</html>
