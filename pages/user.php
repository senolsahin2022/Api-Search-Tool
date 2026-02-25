<?php
$currentPage = 'user';
$username = $username ?? '';

if (empty($username)) {
    header('Location: /');
    exit;
}

$userData = getUser($username);
$using_fallback = false;

if (empty($userData) || empty($userData['tweets'])) {
    $url = "https://hashtag.senolsahin2022.workers.dev/?q=" . urlencode($username);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => ['X-Pentest-Auth: authorized-pentest-2026'],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $response) {
        $fbData = json_decode($response, true);
        if (isset($fbData['payload']) && is_array($fbData['payload'])) {
            $tweets = [];
            foreach ($fbData['payload'] as $item) {
                $author = $item['author'] ?? [];
                $extra = $item['extra'] ?? [];
                $tweets[] = [
                    'id_str' => $item['vendorId'] ?? '',
                    'full_text' => $item['caption'] ?? '',
                    'created_at' => $item['publishedAt'] ?? '',
                    'author' => [
                        'name' => $author['name'] ?? 'User',
                        'handle' => $author['username'] ?? 'user',
                        'image' => $author['profilePictureUrl'] ?? '',
                        'followers' => $author['followersCount'] ?? 0,
                        'verified' => $author['isVerifiedProfile'] ?? false
                    ],
                    'favorite_count' => $item['likesCount'] ?? 0,
                    'retweet_count' => $extra['repostCount'] ?? 0,
                    'reply_count' => $extra['replyCount'] ?? 0
                ];
            }
            if (!empty($tweets)) {
                $userData = ['tweets' => $tweets];
                $using_fallback = true;
            }
        }
    }
}

$firstAuthor = !empty($tweets[0]['author']) ? $tweets[0]['author'] : [];
$displayName = $firstAuthor['name'] ?? $username;
$screenName = $firstAuthor['handle'] ?? $username;

$pageTitle = sprintf(__('user_title'), $screenName);
$pageDescription = sprintf(__('user_desc'), $screenName);
$pageKeywords = $screenName . ', twitter profil, kullanıcı, sosyal medya, ' . __('meta_keywords');
$canonicalUrl = '/user/' . urlencode($username);
$ogType = 'profile';
$ogProfile = $screenName;

require __DIR__ . '/../includes/header.php';

if (!empty($userData) && is_array($userData)):
    $tweets = $userData['tweets'] ?? [];
    $avatar = $firstAuthor['image'] ?? '';
    $followers = $firstAuthor['followers'] ?? 0;
    $verified = $firstAuthor['verified'] ?? false;

    if ($avatar) {
        $avatarLarge = str_replace('_normal', '_400x400', $avatar);
    } else {
        $avatarLarge = '';
    }

    $pageTitle = $displayName . ' (@' . $screenName . ')';
    // Update description to be dynamic too
    $pageDescription = $displayName . ' (@' . $screenName . ') ' . __('user_desc');
?>

<section class="profile-header">
    <div class="no-banner"></div>
    <div class="profile-info">
        <?php if ($avatarLarge): ?>
            <img src="<?= e($avatarLarge) ?>" alt="<?= e($displayName) ?>" class="profile-avatar" loading="lazy">
        <?php else: ?>
            <div class="profile-avatar" style="background:var(--gradient-1);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:2.5rem;color:white"><?= e(mb_substr($displayName, 0, 1)) ?></div>
        <?php endif; ?>
        <div class="profile-details">
            <h1 class="profile-name">
                <?= e($displayName) ?>
                <?php if ($verified): ?><i class="fa-solid fa-circle-check verified" style="font-size:1.2rem"></i><?php endif; ?>
            </h1>
            <div class="profile-handle">@<?= e($screenName) ?></div>
            <div class="profile-stats-bar">
                <?php if ($followers): ?><span class="profile-stat"><strong><?= formatNumber($followers) ?></strong> <?= e(__('followers')) ?></span><?php endif; ?>
                <span class="profile-stat"><strong><?= formatNumber(count($tweets)) ?></strong> <?= e(__('posts')) ?></span>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($tweets) && is_array($tweets)): ?>
<section>
    <h2 class="section-title"><i class="fa-regular fa-newspaper"></i> <?= e(__('last_posts')) ?></h2>
    <div class="tweet-list">
        <?php foreach ($tweets as $tweet):
            require __DIR__ . '/../includes/tweet_card.php';
        endforeach; ?>
    </div>
</section>

<section class="faq-section" style="margin-top: 40px; padding: 20px; background: var(--card-bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
    <h2 style="margin-bottom: 20px; font-size: 1.5rem; color: var(--primary);">@<?= e($screenName) ?> <?= e(__('faq_title')) ?></h2>
    
    <div class="faq-item" style="margin-bottom: 15px;">
        <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q1_user'), $screenName) ?></h3>
        <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a1_user'), $screenName) ?></p>
    </div>

    <div class="faq-item" style="margin-bottom: 15px;">
        <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q2_user'), $screenName) ?></h3>
        <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a2_user'), $screenName) ?></p>
    </div>
</section>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "<?= addslashes(sprintf(__('faq_q1_user'), $screenName)) ?>",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "<?= addslashes(sprintf(__('faq_a1_user'), $screenName)) ?>"
    }
  }, {
    "@type": "Question",
    "name": "<?= addslashes(sprintf(__('faq_q2_user'), $screenName)) ?>",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "<?= addslashes(sprintf(__('faq_a2_user'), $screenName)) ?>"
    }
  }]
}
</script>
<?php endif; ?>

<?php else: ?>
    <div class="error-page">
        <i class="fa-solid fa-user-xmark"></i>
        <h2><?= e(__('user_not_found')) ?></h2>
        <p>"<?= e($username) ?>" <?= e(__('user_not_found_text')) ?></p>
        <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> <?= e(__('home_btn')) ?></a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
