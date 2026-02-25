<?php
$tweetId = $tweetId ?? '';
if (empty($tweetId)) {
    header('Location: /');
    exit;
}

$tweetData = getTweet($tweetId);

$content = $tweetData['text'] ?? $tweetData['content'] ?? $tweetData['full_text'] ?? '';
$author = $tweetData['user'] ?? $tweetData['author'] ?? [];
$authorName = $author['name'] ?? 'Twitter';
$authorHandle = $author['screen_name'] ?? $author['handle'] ?? '';
$cleanContent = strip_tags($content);

$pageTitle = sprintf(__('post_title'), $authorHandle ?: $authorName);
$pageDescription = sprintf(__('post_desc'), $authorHandle ?: $authorName, mb_substr($cleanContent, 0, 150));
$pageKeywords = e($authorName) . ', tweet, x explorer, ' . e(__('meta_keywords'));
$canonicalUrl = '/status/' . $tweetId;
$ogType = 'article';
$articleDate = $tweetData['created_at'] ?? '';

$firstMedia = $tweetData['mediaDetails'][0] ?? null;
if ($firstMedia && !empty($firstMedia['media_url_https'])) {
    $ogImage = $firstMedia['media_url_https'];
}

require __DIR__ . '/../includes/header.php';
?>

<div class="tweet-detail">
    <?php if ($tweetData && !isset($tweetData['error'])): ?>
        <?php
        $mediaItems = $tweetData['mediaDetails'] ?? [];
        $mediaArr = [];
        foreach ($mediaItems as $m) {
            $entry = [
                'type' => $m['type'] ?? 'photo',
                'media_url_https' => $m['media_url_https'] ?? '',
                'url' => $m['url'] ?? '',
                'display_url' => $m['display_url'] ?? '',
            ];
            if (isset($m['video_info'])) {
                $entry['video_info'] = $m['video_info'];
            }
            $mediaArr[] = $entry;
        }

        $tweet = [
            'id' => $tweetData['id_str'] ?? $tweetId,
            'content' => $content,
            'text' => $content,
            'date' => $tweetData['created_at'] ?? '',
            'author' => [
                'name' => $authorName,
                'handle' => $authorHandle,
                'screen_name' => $authorHandle,
                'image' => $author['profile_image_url_https'] ?? $author['profile_image_url'] ?? '',
                'verified' => $author['is_blue_verified'] ?? $author['verified'] ?? false,
            ],
            'engagement' => [
                'likes' => $tweetData['favorite_count'] ?? 0,
                'retweets' => $tweetData['retweet_count'] ?? 0,
                'replies' => $tweetData['reply_count'] ?? 0,
                'quotes' => $tweetData['quote_count'] ?? 0,
            ],
            'views' => $tweetData['views_count'] ?? $tweetData['ext_views']['count'] ?? 0,
            'media' => $mediaArr,
        ];

        require __DIR__ . '/../includes/tweet_card.php';
        ?>
    <?php elseif ($tweetData && isset($tweetData['error'])): ?>
        <div class="error-page">
            <i class="fa-solid fa-circle-exclamation"></i>
            <h2>Hata</h2>
            <p><?= e($tweetData['error']) ?></p>
            <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> <?= e(__('home_btn')) ?></a>
        </div>
    <?php else: ?>
        <div class="error-page">
            <i class="fa-solid fa-circle-exclamation"></i>
            <h2><?= e(__('no_results')) ?></h2>
            <p><?= e(__('tweet_not_found')) ?></p>
            <div style="display:flex; gap:15px; justify-content:center; flex-wrap:wrap; margin-top:15px;">
                <a href="https://x.com/i/status/<?= e($tweetId) ?>" target="_blank" rel="noopener" class="btn btn-primary"><i class="fa-brands fa-x-twitter"></i> X.com</a>
                <a href="/" class="btn btn-primary" style="background:var(--bg-card);"><i class="fa-solid fa-house"></i> <?= e(__('home_btn')) ?></a>
            </div>
        </div>
    <?php endif; ?>

    <?php $handle = $authorHandle ?: 'user'; ?>
    <section class="faq-section" style="margin-top: 40px; padding: 20px; background: var(--card-bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem; color: var(--primary);"><?= e($authorName) ?> <?= e(__('faq_title')) ?></h2>
        
        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q1_post'), $handle) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a1_post'), $handle) ?></p>
        </div>

        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q2_post'), $handle) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a2_post'), $handle) ?></p>
        </div>

        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q3_post'), $handle) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a3_post'), $handle) ?></p>
        </div>
    </section>

    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [{
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q1_post'), $handle)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a1_post'), $handle)) ?>"
        }
      }, {
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q2_post'), $handle)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a2_post'), $handle)) ?>"
        }
      }, {
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q3_post'), $handle)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a3_post'), $handle)) ?>"
        }
      }]
    }
    </script>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
