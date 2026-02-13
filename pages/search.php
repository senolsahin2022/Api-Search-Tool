<?php
$currentPage = 'search';
$query = trim($_GET['q'] ?? '');

if (empty($query)) {
    header('Location: /');
    exit;
}

$isUserSearch = str_starts_with($query, '@');
if ($isUserSearch) {
    $cleanUser = ltrim($query, '@');
    header('Location: /user/' . urlencode($cleanUser));
    exit;
}

$isHashSearch = str_starts_with($query, '#');
if ($isHashSearch) {
    $cleanTag = ltrim($query, '#');
    header('Location: /hashtag/' . urlencode($cleanTag));
    exit;
}

$results = searchPosts($query);

$pageTitle = '"' . $query . '" Arama Sonuçları - TwitExplorer';
$pageDescription = $query . ' hakkındaki en güncel paylaşımları ve içerikleri keşfet.';
$pageKeywords = $query . ', twitter arama, sosyal medya, paylaşımlar';
$canonicalUrl = '/search?q=' . urlencode($query);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title"><i class="fa-solid fa-magnifying-glass" style="color:var(--primary)"></i> "<?= e($query) ?>" <?= e(__('search_results')) ?></h1>
<p class="page-subtitle">
    <?= e(__('try_different')) ?>
    <a href="/hashtag/<?= urlencode($query) ?>" class="tag-link">#<?= e($query) ?></a>
</p>

<?php if (!empty($results) && is_array($results)):
    $tweets = $results['results'] ?? $results['data'] ?? $results['tweets'] ?? $results['statuses'] ?? $results;
?>
    <div class="tweet-list">
        <?php foreach ($tweets as $tweet):
            require __DIR__ . '/../includes/tweet_card.php';
        endforeach; ?>
    </div>
<?php else: ?>
    <div class="error-page">
        <i class="fa-solid fa-magnifying-glass"></i>
        <h2><?= e(__('no_results')) ?></h2>
        <p>"<?= e($query) ?>" <?= e(__('no_results_text')) ?></p>
        <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> <?= e(__('home_btn')) ?></a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
