<?php
/**
 * Synergy Google Reviews block.
 * Styled to match the Tonus layout more closely while keeping Synergy naming.
 */

$defaultConfig = [
    'api_key' => '',
    'place_id' => '',
    'business_name' => 'Luka Qaliashvili, ID: 0172409681',
    'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Synergy%20Gym%20Tbilisi',
];

$configFile = __DIR__ . '/google-config.php';
$config = $defaultConfig;
if (file_exists($configFile)) {
    $loadedConfig = require $configFile;
    if (is_array($loadedConfig)) {
        $config = array_merge($config, $loadedConfig);
    }
}

$cacheFile = __DIR__ . '/../cache/google_reviews.json';
$cacheDuration = 86400;

function synergyFetchGoogleReviews($placeId, $apiKey) {
    if ($placeId === '' || $apiKey === '' || !function_exists('curl_init')) {
        return null;
    }

    $url = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . rawurlencode($placeId) . '&fields=name,rating,user_ratings_total,reviews&key=' . rawurlencode($apiKey) . '&language=en';
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode !== 200 || $response === false) {
        return null;
    }

    $data = json_decode($response, true);
    return $data['result'] ?? null;
}

function synergyGetGoogleReviews($placeId, $apiKey, $cacheFile, $cacheDuration) {
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheDuration) {
        $cachedData = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cachedData)) {
            return $cachedData;
        }
    }

    $freshData = synergyFetchGoogleReviews($placeId, $apiKey);
    if ($freshData) {
        $cacheDir = dirname($cacheFile);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        file_put_contents($cacheFile, json_encode($freshData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $freshData;
    }

    if (file_exists($cacheFile)) {
        $staleData = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($staleData)) {
            return $staleData;
        }
    }

    return null;
}

function synergyRenderStars($rating) {
    $fullStars = max(0, min(5, (int) floor($rating)));
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $html = '';

    for ($index = 0; $index < $fullStars; $index++) {
        $html .= '<span style="color:#c8e600;font-size:1.1rem;">★</span>';
    }

    if ($hasHalfStar) {
        $html .= '<span style="color:#c8e600;font-size:1.1rem;">★</span>';
    }

    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    for ($index = 0; $index < $emptyStars; $index++) {
        $html .= '<span style="color:#6f6f6f;font-size:1.1rem;">★</span>';
    }

    return $html;
}

function synergyGetInitials($name) {
    $words = preg_split('/\s+/', trim($name));
    if (!$words || $words[0] === '') {
        return 'SG';
    }
    if (count($words) >= 2) {
        return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    }
    return strtoupper(substr($words[0], 0, 2));
}

function synergyGetAvatarColor($index) {
    $colors = ['#1a73e8', '#c8e600', '#4caf50', '#ff7043', '#00acc1', '#8e24aa'];
    return $colors[$index % count($colors)];
}

$reviewsData = synergyGetGoogleReviews($config['place_id'], $config['api_key'], $cacheFile, $cacheDuration);

$gymName = $config['business_name'];
$mapsUrl = $config['maps_url'];
$overallRating = 4.8;
$totalReviews = 124;
$reviews = [];

if ($reviewsData) {
    $gymName = $reviewsData['name'] ?? $gymName;
    $overallRating = isset($reviewsData['rating']) ? (float) $reviewsData['rating'] : $overallRating;
    $totalReviews = isset($reviewsData['user_ratings_total']) ? (int) $reviewsData['user_ratings_total'] : $totalReviews;

    $fiveStarReviews = [];
    if (isset($reviewsData['reviews']) && is_array($reviewsData['reviews'])) {
        foreach ($reviewsData['reviews'] as $review) {
            if (isset($review['rating']) && (int) $review['rating'] === 5) {
                $fiveStarReviews[] = $review;
                if (count($fiveStarReviews) >= 6) {
                    break;
                }
            }
        }
    }
    $reviews = $fiveStarReviews;
}

$curatedReviews = [
    [
        'author_name' => 'Nino G.',
        'rating' => 5,
        'text' => 'Very clean gym, strong atmosphere, and friendly staff. The equipment is in great condition and it always feels motivating to train here.'
    ],
    [
        'author_name' => 'Giorgi M.',
        'rating' => 5,
        'text' => 'One of the best places to work out in Tbilisi. Good music, serious equipment, and a team that actually cares about members.'
    ],
    [
        'author_name' => 'Luka B.',
        'rating' => 5,
        'text' => 'The atmosphere is excellent and the gym has everything needed for real training. Great value and very solid experience overall.'
    ],
    [
        'author_name' => 'Elene S.',
        'rating' => 5,
        'text' => 'I like how organized and welcoming the space feels. It is comfortable for regular training and the staff is always helpful.'
    ],
    [
        'author_name' => 'Ana K.',
        'rating' => 5,
        'text' => 'Modern equipment, clean environment, and a strong fitness vibe. This place gives you motivation as soon as you walk in.'
    ],
    [
        'author_name' => 'David T.',
        'rating' => 5,
        'text' => 'Excellent gym with good service and plenty of room to train. Highly recommended if you want a serious workout environment.'
    ]
];

if (count($reviews) < 6) {
    foreach ($curatedReviews as $curatedReview) {
        $reviews[] = $curatedReview;
        if (count($reviews) >= 6) {
            break;
        }
    }
}
?>

<div class="bg-black py-12">
    <div class="container mx-auto">
        <div class="max-w-4xl w-full rounded-2xl shadow-lg p-8 mx-auto" style="background:#111111;border:1px solid rgba(200,230,0,0.16);">
            <div class="flex flex-col items-center text-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
                     alt="Google Logo"
                     class="h-12 w-12 mb-4"
                     onerror="this.onerror=null; this.src='https://placehold.co/48x48/ffffff/000000?text=G';">
                <h2 class="text-5xl font-bold text-white">
                    <a href="<?php echo htmlspecialchars($mapsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="hover:underline"
                       style="color:#ffffff;">
                        <?php echo htmlspecialchars($gymName, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h2>
                <p class="text-xl mt-2" style="color:#bdbdbd;">See what our members are saying!</p>
                <div class="flex flex-col items-center my-5">
                    <div class="flex items-center" style="flex-wrap:nowrap;gap:4px;">
                        <?php echo synergyRenderStars(5); ?>
                        <span class="ml-2 text-xl font-semibold" style="white-space:nowrap;color:#d8d8d8;"><?php echo number_format($overallRating, 1); ?> out of 5</span>
                    </div>
                    <span class="text-xl mt-1" style="color:#9a9a9a;">
                        (based on <?php echo number_format($totalReviews); ?> reviews)
                    </span>
                </div>
            </div>

            <div class="row">
                <?php foreach ($reviews as $index => $review): ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="text-left p-4 rounded-lg" style="height:100%;background:#1f1f1f;border:1px solid rgba(255,255,255,0.05);">
                        <div class="d-flex align-items-center mb-3">
                            <div style="display:flex;align-items:center;justify-content:center;width:48px;height:48px;border-radius:50%;background:<?php echo synergyGetAvatarColor($index); ?>;color:<?php echo $index % 6 === 1 ? '#000000' : '#ffffff'; ?>;font-weight:700;font-size:1rem;margin-right:12px;">
                                <?php echo htmlspecialchars(synergyGetInitials($review['author_name']), ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div>
                                <p class="mb-1" style="font-weight:600;font-size:1.15rem;color:#f3f3f3;">
                                    <?php echo htmlspecialchars($review['author_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <div style="display:flex;gap:2px;">
                                    <?php echo synergyRenderStars((int) ($review['rating'] ?? 5)); ?>
                                </div>
                            </div>
                        </div>
                        <p style="color:#d4d4d4;font-size:1rem;line-height:1.7;margin:0;">
                            "<?php echo htmlspecialchars($review['text'], ENT_QUOTES, 'UTF-8'); ?>"
                        </p>
                        <?php if (isset($review['time'])): ?>
                        <p style="color:#8e8e8e;font-size:0.85rem;margin-top:10px;">
                            <?php echo date('F Y', $review['time']); ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>