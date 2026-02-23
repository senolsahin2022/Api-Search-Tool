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

$pageTitle = sprintf(__('search_title'), $query);
$pageDescription = sprintf(__('search_desc'), $query) . ' - ' . __('hero_subtitle');
$pageKeywords = sprintf(__('search_keywords'), $query);
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

    <section style="margin-top: 50px;">
        <h2 class="section-title"><i class="fa-solid fa-circle-question"></i> FAQ</h2>
        <div class="faq-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
            <div class="trend-card" style="margin-bottom: 0; cursor: default;">
                <div class="trend-name">"<?= e($query) ?>" araması güvenli mi?</div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;">Evet, TwitExplorer üzerinden yapılan tüm aramalar anonimdir ve güvenli bağlantı üzerinden gerçekleşir.</p>
            </div>
            <div class="trend-card" style="margin-bottom: 0; cursor: default;">
                <div class="trend-name">En son "<?= e($query) ?>" haberlerini nasıl görürüm?</div>
                <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;">Bu sayfa anlık olarak güncellenerek "<?= e($query) ?>" hakkındaki en taze içerikleri sunar.</p>
            </div>
        </div>
    </section>
<?php else: ?>
    <div class="error-page">
        <i class="fa-solid fa-magnifying-glass"></i>
        <h2><?= e(__('no_results')) ?></h2>
        <p>"<?= e($query) ?>" <?= e(__('no_results_text')) ?></p>
        <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> <?= e(__('home_btn')) ?></a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
