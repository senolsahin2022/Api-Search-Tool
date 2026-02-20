<?php
$currentPage = 'hashtag';
$tag = $tag ?? '';

if (empty($tag)) {
    header('Location: /');
    exit;
}

$results = getHashtag($tag);
$using_fallback = false;
$fallback_status = '';

// Debugging: What did we get from 1st endpoint?
$is_empty = empty($results) || 
           (isset($results['data']) && empty($results['data'])) || 
           (isset($results['results']) && empty($results['results'])) ||
           (isset($results['tweets']) && empty($results['tweets']));

if ($is_empty) {
    $using_fallback = true;
    $url = "https://hashtag.senolsahin2022.workers.dev/?q=" . urlencode($tag);
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => ['X-Pentest-Auth: authorized-pentest-2026'],
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpCode === 200 && $response) {
        $results = json_decode($response, true);
        $fallback_status = 'success';
    } else {
        $fallback_status = 'error';
    }
}

$pageTitle = sprintf(__('hashtag_title'), $tag);
$pageDescription = sprintf(__('hashtag_desc'), $tag);
$pageKeywords = $tag . ', ' . __('hashtag') . ', twitter, X, ' . __('search') . ', ' . __('trending');
$canonicalUrl = '/hashtag/' . urlencode($tag);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title"><span style="color:var(--primary)">#</span><?= e($tag) ?></h1>
<p class="page-subtitle"><?= e(__('hashtag_subtitle')) ?></p>

<?php if ($using_fallback && $fallback_status !== 'success'): ?>
    <div style="padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; background: rgba(249, 24, 128, 0.1); border: 1px solid rgba(249, 24, 128, 0.3);">
        <i class="fa-solid fa-triangle-exclamation" style="color: #f91880;"></i>
        <span style="color: #f91880; font-weight: 600;">HATA: Her iki endpointten de veri alınamadı! ❌</span>
    </div>
<?php endif; ?>

<?php if (!empty($results) && is_array($results)):
    $tweets = [];
    
    // Normal hashtag structure
    if (isset($results['tweets'])) $tweets = $results['tweets'];
    elseif (isset($results['results'])) $tweets = $results['results'];
    elseif (isset($results['statuses'])) $tweets = $results['statuses'];
    // New nested structure from fallback endpoint
    elseif (isset($results['data']['search_by_raw_query']['search_timeline']['timeline']['instructions'])) {
        $instructions = $results['data']['search_by_raw_query']['search_timeline']['timeline']['instructions'];
        foreach ($instructions as $inst) {
            if (isset($inst['entries'])) {
                foreach ($inst['entries'] as $entry) {
                    $tweetData = null;
                    if (isset($entry['content']['itemContent']['tweet_results']['result'])) {
                        $tweetData = $entry['content']['itemContent']['tweet_results']['result'];
                    } elseif (isset($entry['content']['timelineModule']['items'][0]['item']['itemContent']['tweet_results']['result'])) {
                        $tweetData = $entry['content']['timelineModule']['items'][0]['item']['itemContent']['tweet_results']['result'];
                    }

                        if ($tweetData) {
                            if (isset($tweetData['tweet'])) $tweetData = $tweetData['tweet'];
                            
                            $legacy = $tweetData['legacy'] ?? $tweetData ?? [];
                            $core = $tweetData['core'] ?? [];
                            $avatar = $tweetData['avatar'] ?? [];
                            
                            // Handle the structure the user found: "avatar": { "image_url": "..." }, "core": { "name": "...", "screen_name": "..." }
                            $user = [
                                'name' => $core['name'] ?? $tweetData['user_results']['result']['legacy']['name'] ?? $tweetData['user_results']['result']['name'] ?? 'User',
                                'screen_name' => $core['screen_name'] ?? $tweetData['user_results']['result']['legacy']['screen_name'] ?? $tweetData['user_results']['result']['screen_name'] ?? $tweetData['user_results']['result']['handle'] ?? 'user',
                                'profile_image_url_https' => $avatar['image_url'] ?? $tweetData['user_results']['result']['legacy']['profile_image_url_https'] ?? $tweetData['user_results']['result']['profile_image_url_https'] ?? ''
                            ];

                        // Ensure we have a valid tweet structure
                        $tweets[] = [
                            'id' => $legacy['id_str'] ?? $tweetData['rest_id'] ?? $tweetData['id_str'] ?? '',
                            'id_str' => $legacy['id_str'] ?? $tweetData['rest_id'] ?? $tweetData['id_str'] ?? '',
                            'full_text' => $legacy['full_text'] ?? $legacy['text'] ?? $tweetData['full_text'] ?? $tweetData['text'] ?? '',
                            'created_at' => $legacy['created_at'] ?? $tweetData['created_at'] ?? '',
                            'user' => $user,
                            'favorite_count' => $legacy['favorite_count'] ?? $tweetData['favorite_count'] ?? 0,
                            'retweet_count' => $legacy['retweet_count'] ?? $tweetData['retweet_count'] ?? 0,
                            'reply_count' => $legacy['reply_count'] ?? $tweetData['reply_count'] ?? 0,
                            'quote_count' => $legacy['quote_count'] ?? $tweetData['quote_count'] ?? 0,
                            'entities' => $legacy['entities'] ?? $tweetData['entities'] ?? [],
                            'extended_entities' => $legacy['extended_entities'] ?? $tweetData['extended_entities'] ?? []
                        ];
                    }
                }
            }
        }
    } else {
        $tweets = $results['data'] ?? $results;
    }
?>
    <div class="tweet-list">
        <?php foreach ($tweets as $tweet):
            if (empty($tweet)) continue;
            // Add a flag to indicate if we should hide the header
            $tweet['hide_header'] = $using_fallback;
            require __DIR__ . '/../includes/tweet_card.php';
        endforeach; ?>
    </div>

    <!-- FAQ Section for SEO -->
    <section class="faq-section" style="margin-top: 40px; padding: 20px; background: var(--card-bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem; color: var(--primary);"><?= e($tag) ?> <?= e(__('faq_title')) ?></h2>
        
        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q1_hashtag'), $tag) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a1_hashtag'), $tag) ?></p>
        </div>

        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q2_hashtag'), $tag) ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a2_hashtag'), $tag) ?></p>
        </div>
    </section>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [{
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q1_hashtag'), $tag)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a1_hashtag'), $tag)) ?>"
        }
      }, {
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q2_hashtag'), $tag)) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a2_hashtag'), $tag)) ?>"
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
