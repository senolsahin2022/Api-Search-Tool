<?php
$currentPage = 'hashtag';
$tag = $tag ?? '';

if (empty($tag)) {
    header('Location: /');
    exit;
}

$results = getHashtag($tag);

$pageTitle = sprintf(__('hashtag_title'), $tag);
$pageDescription = sprintf(__('hashtag_desc'), $tag);
$pageKeywords = $tag . ', hashtag, twitter, sosyal medya, paylaşımlar';
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
    // New nested structure from fallback endpoint
    elseif (isset($results['data']['search_by_raw_query']['search_timeline']['timeline']['instructions'])) {
        $instructions = $results['data']['search_by_raw_query']['search_timeline']['timeline']['instructions'];
        foreach ($instructions as $inst) {
            if (isset($inst['entries'])) {
                foreach ($inst['entries'] as $entry) {
                    if (isset($entry['content']['itemContent']['tweet_results']['result'])) {
                        $tweetRes = $entry['content']['itemContent']['tweet_results']['result'];
                        // Standardize for tweet_card.php
                        $legacy = $tweetRes['legacy'] ?? [];
                        $core = $tweetRes['core'] ?? [];
                        $user = $core['user_results']['result']['legacy'] ?? [];
                        
                        $tweets[] = array_merge($legacy, [
                            'id' => $legacy['id_str'] ?? '',
                            'author' => array_merge($user, [
                                'name' => $user['name'] ?? '',
                                'handle' => $user['screen_name'] ?? '',
                                'image' => $user['profile_image_url_https'] ?? ''
                            ]),
                            'engagement' => [
                                'likes' => $legacy['favorite_count'] ?? 0,
                                'retweets' => $legacy['retweet_count'] ?? 0,
                                'replies' => $legacy['reply_count'] ?? 0
                            ]
                        ]);
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
