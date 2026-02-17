<?php
$currentPage = 'home';
$pageTitle = 'TwitExplorer - Sosyal Medya Trendlerini Keşfet';
$pageDescription = 'Güncel sosyal medya trendlerini keşfet, popüler konuları takip et ve en çok konuşulan hashtag\'leri bul.';
$pageKeywords = 'twitter trendler, gündem, popüler konular, sosyal medya, hashtag';
$canonicalUrl = '/';

$trends = getTrends();

require __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <h1><?= e(__('hero_title')) ?></h1>
    <p><?= e(__('hero_subtitle')) ?></p>
    <div class="quick-search-tags">
        <a href="/search?q=crypto" class="tag-link">#crypto</a>
        <a href="/search?q=bitcoin" class="tag-link">#bitcoin</a>
        <a href="/search?q=technology" class="tag-link">#technology</a>
        <a href="/search?q=ai" class="tag-link">#ai</a>
        <a href="/hashtag/btc" class="tag-link">#btc</a>
        <a href="/user/elonmusk" class="tag-link">@elonmusk</a>
    </div>
</section>

<section>
    <h2 class="section-title"><i class="fa-solid fa-fire-flame-curved"></i> <?= e(__('trending_topics')) ?></h2>
    <?php if (!empty($trends) && is_array($trends)): ?>
        <div class="trends-grid">
            <?php
            $trendList = $trends;
            if (isset($trends['trends'])) $trendList = $trends['trends'];
            elseif (isset($trends['data'])) $trendList = $trends['data'];

            $rank = 1;
            foreach ($trendList as $trend):
                if (is_string($trend)) {
                    $name = $trend;
                    $volume = '';
                } else {
                    $name = $trend['name'] ?? $trend['query'] ?? $trend['topic'] ?? ($trend['trend'] ?? '');
                    $volume = $trend['tweet_volume'] ?? $trend['volume'] ?? $trend['count'] ?? '';
                }

                if (empty($name)) continue;

                $searchName = ltrim($name, '#');
                $isHashtag = str_starts_with($name, '#');
                $link = '/search?q=' . urlencode($searchName);
            ?>
            <a href="<?= e($link) ?>" class="trend-card">
                <div class="trend-rank"><?= $rank ?> · <?= e(__('trend')) ?></div>
                <div class="trend-name"><?= e($name) ?></div>
                <?php if ($volume): ?>
                    <div class="trend-volume"><?= formatNumber($volume) ?> <?= e(__('posts')) ?></div>
                <?php endif; ?>
            </a>
            <?php $rank++; endforeach; ?>
        </div>
    <?php else: ?>
        <div class="error-page">
            <i class="fa-solid fa-cloud-bolt"></i>
            <h2><?= e(__('load_error')) ?></h2>
            <p><?= e(__('retry')) ?></p>
        </div>
    <?php endif; ?>
</section>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "TwitExplorer",
  "url": "<?= $baseUrl ?>",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "<?= $baseUrl ?>/search?q={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
</script>

<section style="margin-top: 50px;">
    <h2 class="section-title"><i class="fa-solid fa-circle-question"></i> <?= e(__('popular_searches')) ?> FAQ</h2>
    <div class="faq-container">
        <div class="trend-card" style="margin-bottom: 10px; cursor: default;">
            <div class="trend-name"><?= e(__('hero_title')) ?>?</div>
            <p style="color: var(--text-secondary); font-size: 0.9rem;"><?= e(__('hero_subtitle')) ?></p>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
