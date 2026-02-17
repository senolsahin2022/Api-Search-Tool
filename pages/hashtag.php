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
    $tweets = $results['tweets'] ?? $results['results'] ?? $results['data'] ?? $results['statuses'] ?? $results;
?>
    <div class="tweet-list">
        <?php foreach ($tweets as $tweet):
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
