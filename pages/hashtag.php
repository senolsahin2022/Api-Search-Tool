<?php
$currentPage = 'hashtag';
$tag = $tag ?? '';

if (empty($tag)) {
    header('Location: /');
    exit;
}

$results = getHashtag($tag);
$using_fallback = false;

// If primary API fails or returns no data, try the new fallback
$is_empty = empty($results) || 
           (isset($results['data']) && empty($results['data'])) || 
           (isset($results['results']) && empty($results['results'])) ||
           (isset($results['tweets']) && empty($results['tweets']));

if ($is_empty) {
    $url = "https://hashtag.senolsahin2022.workers.dev/?q=" . urlencode($tag);
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
            $results = $fbData['payload'];
            $using_fallback = true;
        }
    }
}

$pageTitle = sprintf(__('hashtag_title'), $tag) . ' - TwitExplorer';
$pageDescription = sprintf(__('hashtag_desc'), $tag) . ' ' . __('hashtag_subtitle');
$pageKeywords = $tag . ', ' . __('hashtag') . ', twitter, X, ' . __('search') . ', ' . __('trending') . ', ' . __('meta_keywords');
$canonicalUrl = '/hashtag/' . urlencode($tag);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title"><span style="color:var(--primary)">#</span><?= e($tag) ?></h1>
<p class="page-subtitle"><?= e(__('hashtag_subtitle')) ?></p>

<?php if (!empty($results) && is_array($results)):
    $tweets = [];
    
    // Normal hashtag structure
    if (isset($results['tweets'])) $tweets = $results['tweets'];
    elseif (isset($results['results'])) $tweets = $results['results'];
    elseif (isset($results['statuses'])) $tweets = $results['statuses'];
    elseif ($using_fallback) {
        // Handle the new clean payload structure
        foreach ($results as $item) {
            $author = $item['author'] ?? [];
            $extra = $item['extra'] ?? [];
            
            $tweets[] = [
                'id_str' => $item['vendorId'] ?? '',
                'full_text' => $item['caption'] ?? '',
                'created_at' => $item['publishedAt'] ?? '',
                'user' => [
                    'name' => $author['name'] ?? 'User',
                    'screen_name' => $author['username'] ?? 'user',
                    'profile_image_url_https' => $author['profilePictureUrl'] ?? ''
                ],
                'favorite_count' => $item['likesCount'] ?? 0,
                'retweet_count' => $extra['repostCount'] ?? 0,
                'reply_count' => $extra['replyCount'] ?? 0,
                'quote_count' => 0,
                'entities' => [],
                'extended_entities' => [
                    'media' => array_map(function($m) {
                        return [
                            'type' => $m['type'] === 'video' ? 'video' : 'photo',
                            'media_url_https' => $m['cover']['standard']['url'] ?? '',
                            'video_info' => $m['type'] === 'video' ? ['variants' => [['url' => $m['url']]]] : null
                        ];
                    }, $item['media'] ?? [])
                ]
            ];
        }
    } else {
        $tweets = $results['data'] ?? $results;
    }
?>
    <div class="tweet-list">
        <?php foreach ($tweets as $tweet):
            if (empty($tweet)) continue;
            // The user wants author info to be visible even for fallback
            $tweet['hide_header'] = false; 
            require __DIR__ . '/../includes/tweet_card.php';
        endforeach; ?>
    </div>

    <!-- FAQ Section for SEO -->
    <section class="faq-section" style="margin-top: 40px; padding: 20px; background: var(--card-bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem; color: var(--primary);"><?= e($tag) ?> <?= e(__('faq_title')) ?></h2>
        
        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q1_hashtag'), '#' . $tag) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a1_hashtag'), '#' . $tag) ?></p>
        </div>

        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q2_hashtag'), '#' . $tag) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a2_hashtag'), '#' . $tag) ?></p>
        </div>
    </section>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [{
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q1_hashtag'), '#' . $tag)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a1_hashtag'), '#' . $tag)) ?>"
        }
      }, {
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q2_hashtag'), '#' . $tag)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a2_hashtag'), '#' . $tag)) ?>"
        }
      }]
    }
    </script>
<?php else: ?>
    <div class="error-page">
        <i class="fa-solid fa-hashtag"></i>
        <h2>Sonuç bulunamadı</h2>
        <p>#<?= e($tag) ?> hashtag'i ile eşleşen içerik bulunamadı.</p>
        <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
