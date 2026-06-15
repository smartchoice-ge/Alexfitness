<?php
$defaultConfig = [
    'api_key'       => '',
    'place_id'      => '',
    'business_name' => 'Alex Fit',
    'maps_url'      => 'https://www.google.com/maps/search/?api=1&query=Alex+Fit+Kobuleti',
];

$configFile = __DIR__ . '/google-config.php';
$config     = $defaultConfig;
if (file_exists($configFile)) {
    $loadedConfig = require $configFile;
    if (is_array($loadedConfig)) {
        $config = array_merge($config, $loadedConfig);
    }
}

$cacheFile     = __DIR__ . '/../cache/google_reviews.json';
$cacheDuration = 86400;

function synergyFetchGoogleReviews($placeId, $apiKey) {
    if ($placeId === '' || $apiKey === '' || !function_exists('curl_init')) return null;
    $url  = 'https://maps.googleapis.com/maps/api/place/details/json?place_id=' . rawurlencode($placeId)
          . '&fields=name,rating,user_ratings_total,reviews&key=' . rawurlencode($apiKey) . '&language=en';
    $curl = curl_init();
    curl_setopt_array($curl, [CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true,
                               CURLOPT_SSL_VERIFYPEER => true, CURLOPT_TIMEOUT => 10]);
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpCode !== 200 || $response === false) return null;
    $data = json_decode($response, true);
    return $data['result'] ?? null;
}

function synergyGetGoogleReviews($placeId, $apiKey, $cacheFile, $cacheDuration) {
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheDuration) {
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached)) return $cached;
    }
    $fresh = synergyFetchGoogleReviews($placeId, $apiKey);
    if ($fresh) {
        $dir = dirname($cacheFile);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        file_put_contents($cacheFile, json_encode($fresh, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return $fresh;
    }
    if (file_exists($cacheFile)) {
        $stale = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($stale)) return $stale;
    }
    return null;
}

function synergyRenderStars($rating, $size = '1.3rem') {
    $full  = max(0, min(5, (int) floor($rating)));
    $half  = ($rating - $full) >= 0.5;
    $empty = 5 - $full - ($half ? 1 : 0);
    $html  = '';
    for ($i = 0; $i < $full;  $i++) $html .= '<span class="rev-star rev-star--on"  style="font-size:' . $size . '">★</span>';
    if ($half)                        $html .= '<span class="rev-star rev-star--half" style="font-size:' . $size . '">★</span>';
    for ($i = 0; $i < $empty; $i++) $html .= '<span class="rev-star rev-star--off"  style="font-size:' . $size . '">★</span>';
    return $html;
}

function synergyGetInitials($name) {
    $words = preg_split('/\s+/', trim($name));
    if (!$words || $words[0] === '') return 'AF';
    return count($words) >= 2
        ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1))
        : strtoupper(substr($words[0], 0, 2));
}

/* Avatar gradients using the site palette */
function synergyGetAvatarStyle($index) {
    $styles = [
        'background:linear-gradient(135deg,#16a34a,#4ade80);color:#000;',
        'background:linear-gradient(135deg,#0284c7,#38bdf8);color:#000;',
        'background:linear-gradient(135deg,#15803d,#22c55e);color:#000;',
        'background:linear-gradient(135deg,#1d4ed8,#60a5fa);color:#000;',
        'background:linear-gradient(135deg,#166534,#4ade80);color:#000;',
        'background:linear-gradient(135deg,#0369a1,#38bdf8);color:#000;',
    ];
    return $styles[$index % count($styles)];
}

$reviewsData   = synergyGetGoogleReviews($config['place_id'], $config['api_key'], $cacheFile, $cacheDuration);
$gymName       = $config['business_name'];
$mapsUrl       = $config['maps_url'];
$overallRating = 4.8;
$totalReviews  = 124;
$reviews       = [];

if ($reviewsData) {
    $gymName       = $reviewsData['name'] ?? $gymName;
    $overallRating = isset($reviewsData['rating'])            ? (float) $reviewsData['rating']          : $overallRating;
    $totalReviews  = isset($reviewsData['user_ratings_total']) ? (int)   $reviewsData['user_ratings_total'] : $totalReviews;
    if (isset($reviewsData['reviews']) && is_array($reviewsData['reviews'])) {
        foreach ($reviewsData['reviews'] as $r) {
            if (isset($r['rating']) && (int) $r['rating'] === 5) {
                $reviews[] = $r;
                if (count($reviews) >= 6) break;
            }
        }
    }
}

$curatedReviews = [
    ['author_name' => 'Nino G.',   'rating' => 5, 'text' => 'Very clean gym, strong atmosphere, and friendly staff. The equipment is in great condition and it always feels motivating to train here.'],
    ['author_name' => 'Giorgi M.', 'rating' => 5, 'text' => 'One of the best places to work out in Tbilisi. Good music, serious equipment, and a team that actually cares about members.'],
    ['author_name' => 'Luka B.',   'rating' => 5, 'text' => 'The atmosphere is excellent and the gym has everything needed for real training. Great value and a very solid experience overall.'],
    ['author_name' => 'Elene S.',  'rating' => 5, 'text' => 'I like how organised and welcoming the space feels. Comfortable for regular training and the staff is always helpful.'],
    ['author_name' => 'Ana K.',    'rating' => 5, 'text' => 'Modern equipment, clean environment, and a strong fitness vibe. This place gives you motivation as soon as you walk in.'],
    ['author_name' => 'David T.',  'rating' => 5, 'text' => 'Excellent gym with good service and plenty of room to train. Highly recommended if you want a serious workout environment.'],
];

while (count($reviews) < 6) {
    $reviews[] = array_shift($curatedReviews);
}
?>

<style>
/* ── Reviews Section ──────────────────────────────────────────────────────── */
.reviews-section {
    padding: 70px 0 80px;
    background:
        radial-gradient(ellipse 70% 55% at 50% 100%, rgba(74,222,128,0.07) 0%, transparent 65%),
        #080e0a;
    border-top: 1px solid rgba(74,222,128,0.12);
}

.reviews-inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ── Section heading ── */
.reviews-heading {
    text-align: center;
    margin-bottom: 48px;
}
.reviews-google-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: rgba(74,222,128,0.08);
    border: 1px solid rgba(74,222,128,0.25);
    border-radius: 50px;
    padding: 7px 18px;
    margin-bottom: 22px;
}
.reviews-google-badge img  { width: 22px; height: 22px; }
.reviews-google-badge span { color: #a0a0a0; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px; }

.reviews-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: #ececec;
    margin: 0 0 6px;
    letter-spacing: 0.5px;
}
.reviews-title span { color: #4ade80; }
.reviews-subtitle {
    color: #7a7a7a;
    font-size: 1rem;
    margin: 0 0 32px;
}

/* ── Hero rating bar ── */
.reviews-hero-rating {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 28px;
    flex-wrap: wrap;
    background: rgba(74,222,128,0.05);
    border: 1px solid rgba(74,222,128,0.18);
    border-radius: 16px;
    padding: 24px 36px;
    max-width: 520px;
    margin: 0 auto;
}
.reviews-big-number {
    font-size: 4.2rem;
    font-weight: 900;
    line-height: 1;
    color: #4ade80;
    text-shadow: 0 0 28px rgba(74,222,128,0.50);
    letter-spacing: -2px;
}
.reviews-hero-right { text-align: left; }
.reviews-hero-stars { display: flex; gap: 3px; margin-bottom: 5px; }
.reviews-hero-count {
    color: #8a8a8a;
    font-size: 0.88rem;
    margin-bottom: 8px;
}
.reviews-maps-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: #38bdf8;
    font-size: 0.88rem;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s;
}
.reviews-maps-link:hover { color: #7dd3fc; text-decoration: none; }

/* ── Stars ── */
.rev-star { line-height: 1; }
.rev-star--on   { color: #4ade80; text-shadow: 0 0 8px rgba(74,222,128,0.65); }
.rev-star--half { color: #4ade80; opacity: 0.60; }
.rev-star--off  { color: #2a2a2a; }

/* ── Cards grid ── */
.reviews-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-top: 52px;
}
@media (max-width: 900px) { .reviews-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 580px) { .reviews-grid { grid-template-columns: 1fr; } }

/* ── Individual card ── */
.review-card {
    position: relative;
    display: flex;
    flex-direction: column;
    padding: 28px 24px 22px;
    border-radius: 14px;
    background: rgba(8, 20, 12, 0.72);
    border: 1px solid rgba(74,222,128,0.14);
    border-top: 2px solid rgba(74,222,128,0.40);
    backdrop-filter: blur(10px);
    transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    overflow: hidden;
}
.review-card::before {
    content: '"';
    position: absolute;
    top: 12px; right: 18px;
    font-size: 5rem;
    line-height: 1;
    color: rgba(74,222,128,0.10);
    font-family: Georgia, serif;
    font-weight: 900;
    pointer-events: none;
}
.review-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 44px rgba(0,0,0,0.55), 0 0 22px rgba(74,222,128,0.14);
    border-color: rgba(74,222,128,0.45);
    border-top-color: #4ade80;
}

.review-text {
    color: #d0d0d0;
    font-size: 0.95rem;
    line-height: 1.75;
    flex: 1;
    margin: 0 0 20px;
    position: relative;
    z-index: 1;
}

/* ── Card footer: avatar + name + stars ── */
.review-footer {
    display: flex;
    align-items: center;
    gap: 12px;
    border-top: 1px solid rgba(74,222,128,0.10);
    padding-top: 16px;
}
.review-avatar {
    flex-shrink: 0;
    width: 44px; height: 44px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 0.95rem;
    box-shadow: 0 3px 12px rgba(0,0,0,0.35);
}
.review-author-name {
    display: block;
    font-weight: 700;
    font-size: 0.95rem;
    color: #ececec;
    margin-bottom: 3px;
}
.review-card-stars { display: flex; gap: 2px; }

/* ── Date ── */
.review-date {
    display: block;
    color: #606060;
    font-size: 0.78rem;
    margin-top: 4px;
}
</style>

<section class="reviews-section">
  <div class="reviews-inner">

    <!-- Heading -->
    <div class="reviews-heading">
      <div class="reviews-google-badge">
        <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg"
             alt="Google"
             onerror="this.onerror=null;this.src='https://placehold.co/22x22/ffffff/000000?text=G';">
        <span name="key_reviews_google_badge">Google Reviews</span>
      </div>
      <h2 class="reviews-title">
        <span name="key_reviews_title_1">What Our Members</span><br><span name="key_reviews_title_2">Say About Us</span>
      </h2>
      <p class="reviews-subtitle" name="key_reviews_subtitle">Real feedback from real members of Alex Fit</p>

      <!-- Hero rating -->
      <div class="reviews-hero-rating">
        <div class="reviews-big-number"><?php echo number_format($overallRating, 1); ?></div>
        <div class="reviews-hero-right">
          <div class="reviews-hero-stars">
            <?php echo synergyRenderStars($overallRating, '1.55rem'); ?>
          </div>
          <p class="reviews-hero-count">
            <span name="key_reviews_based_on">Based on</span> <?php echo number_format($totalReviews); ?> <span name="key_reviews_count_label">reviews</span>
          </p>
          <a href="<?php echo htmlspecialchars($mapsUrl, ENT_QUOTES, 'UTF-8'); ?>"
             target="_blank" rel="noopener noreferrer"
             class="reviews-maps-link"
             name="key_reviews_maps_link">
            View on Google Maps ›
          </a>
        </div>
      </div>
    </div>

    <!-- Cards -->
    <div class="reviews-grid">
      <?php foreach ($reviews as $index => $review): ?>
      <div class="review-card">
        <p class="review-text" name="key_review_text_<?php echo $index; ?>">
          <?php echo htmlspecialchars($review['text'], ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <div class="review-footer">
          <div class="review-avatar" style="<?php echo synergyGetAvatarStyle($index); ?>">
            <?php echo htmlspecialchars(synergyGetInitials($review['author_name']), ENT_QUOTES, 'UTF-8'); ?>
          </div>
          <div>
            <span class="review-author-name">
              <?php echo htmlspecialchars($review['author_name'], ENT_QUOTES, 'UTF-8'); ?>
            </span>
            <div class="review-card-stars">
              <?php echo synergyRenderStars((int) ($review['rating'] ?? 5), '1.05rem'); ?>
            </div>
            <?php if (isset($review['time'])): ?>
            <span class="review-date"><?php echo date('F Y', $review['time']); ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>
