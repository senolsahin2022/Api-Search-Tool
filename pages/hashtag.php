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

// Fallback logic moved directly to hashtag page
if (empty($results) || (isset($results['data']) && empty($results['data'])) || (isset($results['results']) && empty($results['results']))) {
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
$pageKeywords = $tag . ', hashtag, twitter, sosyal medya, paylaşımlar';
$canonicalUrl = '/hashtag/' . urlencode($tag);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title"><span style="color:var(--primary)">#</span><?= e($tag) ?></h1>
<p class="page-subtitle"><?= e(__('hashtag_subtitle')) ?></p>

<?php if ($using_fallback): ?>
    <div style="padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px; background: rgba(255, 165, 0, 0.1); border: 1px solid rgba(255, 165, 0, 0.3);">
        <div style="display: flex; align-items: center; gap: 12px;">
            <?php if ($fallback_status === 'success'): ?>
                <i class="fa-solid fa-info-circle" style="color: #ffa500;"></i>
                <span style="color: #ffa500; font-weight: 600;">1. Endpoint başarısız oldu, 2. Endpointten veriler çekilmeye çalışıldı. ✅</span>
            <?php else: ?>
                <i class="fa-solid fa-triangle-exclamation" style="color: #f91880;"></i>
                <span style="color: #f91880; font-weight: 600;">HATA: Her iki endpointten de veri alınamadı! ❌</span>
            <?php endif; ?>
        </div>
        
        <details style="width: 100%;">
            <summary style="cursor: pointer; color: #ffa500; font-size: 0.9rem; opacity: 0.8;">API Ham Verisini Gör (Debug)</summary>
            <pre style="background: #15202b; color: #8899a6; padding: 15px; border-radius: 8px; margin-top: 10px; overflow-x: auto; font-size: 0.8rem; border: 1px solid #38444d; max-height: 400px;"><?php echo htmlspecialchars(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
        </details>
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
                        
                        $legacy = $tweetData['legacy'] ?? [];
                        $core = $tweetData['core'] ?? [];
                        $userResult = $core['user_results']['result'] ?? [];
                        if (isset($userResult['tweet'])) $userResult = $userResult['tweet'];
                        $user = $userResult['legacy'] ?? [];
                        
                        if (!empty($legacy)) {
                            // Map to exactly what tweet_card.php expects
                            $tweets[] = [
                                'id_str' => $legacy['id_str'] ?? ($tweetData['rest_id'] ?? ''),
                                'full_text' => $legacy['full_text'] ?? '',
                                'created_at' => $legacy['created_at'] ?? '',
                                'user' => [
                                    'name' => $user['name'] ?? 'User',
                                    'screen_name' => $user['screen_name'] ?? 'user',
                                    'profile_image_url_https' => $user['profile_image_url_https'] ?? ''
                                ],
                                'favorite_count' => $legacy['favorite_count'] ?? 0,
                                'retweet_count' => $legacy['retweet_count'] ?? 0,
                                'reply_count' => $legacy['reply_count'] ?? 0,
                                'quote_count' => $legacy['quote_count'] ?? 0,
                                'entities' => $legacy['entities'] ?? [],
                                'extended_entities' => $legacy['extended_entities'] ?? []
                            ];
                        }
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
            require __DIR__ . '/../includes/tweet_card.php';
        endforeach; ?>
    </div>
<?php else: ?>
    <div class="error-page">
        <i class="fa-solid fa-hashtag"></i>
        <h2>Sonuç bulunamadı</h2>
        <p>#<?= e($tag) ?> hashtag'i ile eşleşen içerik bulunamadı.</p>
        <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
