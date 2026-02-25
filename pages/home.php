<?php
$currentPage = 'home';
$pageTitle = __('meta_title');
$pageDescription = __('meta_desc');
$pageKeywords = __('meta_keywords');
$canonicalUrl = '/';

$trends = getTrends();

require __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <h1><?= e(__('hero_title')) ?></h1>
    <p><?= e(__('hero_subtitle')) ?></p>
    
    <div class="bypass-info" style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; text-align: left;">
        <div class="trend-card" style="cursor: default; background: rgba(29, 155, 240, 0.1); border-color: var(--primary);">
            <div class="trend-name"><i class="fa-solid fa-shield-halved"></i> <?= e(__('bypass_title')) ?></div>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 10px;"><?= e(__('bypass_desc')) ?></p>
        </div>
        <div class="trend-card" style="cursor: default; background: rgba(0, 186, 124, 0.1); border-color: var(--success);">
            <div class="trend-name"><i class="fa-solid fa-magnifying-glass-chart"></i> <?= e(__('seo_friendly')) ?></div>
            <p style="font-size: 0.9rem; color: var(--text-secondary); margin-top: 10px;"><?= e(__('seo_desc')) ?></p>
        </div>
    </div>

    <div class="quick-search-tags" style="margin-top: 30px;">
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

<section class="widget-showcase">
    <div class="showcase-glow"></div>
    <div class="showcase-content">
        <div class="showcase-text">
            <div class="showcase-badge"><i class="fa-solid fa-sparkles"></i> Widget Builder</div>
            <h2><?= e(__('widget_showcase_title')) ?></h2>
            <p><?= e(__('widget_showcase_desc')) ?></p>
            <div class="showcase-features">
                <div class="showcase-feature">
                    <div class="showcase-feature-icon"><i class="fa-solid fa-layer-group"></i></div>
                    <div>
                        <strong><?= e(__('widget_feature_1')) ?></strong>
                        <span><?= e(__('widget_feature_1_desc')) ?></span>
                    </div>
                </div>
                <div class="showcase-feature">
                    <div class="showcase-feature-icon"><i class="fa-solid fa-palette"></i></div>
                    <div>
                        <strong><?= e(__('widget_feature_2')) ?></strong>
                        <span><?= e(__('widget_feature_2_desc')) ?></span>
                    </div>
                </div>
                <div class="showcase-feature">
                    <div class="showcase-feature-icon"><i class="fa-solid fa-code"></i></div>
                    <div>
                        <strong><?= e(__('widget_feature_3')) ?></strong>
                        <span><?= e(__('widget_feature_3_desc')) ?></span>
                    </div>
                </div>
            </div>
            <a href="/widget" class="showcase-cta"><?= e(__('promo_bar_cta')) ?> <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="showcase-preview">
            <div class="showcase-widget-mock">
                <div class="mock-header">
                    <div class="mock-accent-bar"></div>
                    <div>
                        <div class="mock-title">@elonmusk</div>
                        <div class="mock-subtitle"><?= e(__('last_posts')) ?></div>
                    </div>
                </div>
                <div class="mock-tweet">
                    <div class="mock-avatar"></div>
                    <div class="mock-tweet-body">
                        <div class="mock-name-row"><span class="mock-name"></span><span class="mock-handle"></span></div>
                        <div class="mock-text-line w100"></div>
                        <div class="mock-text-line w80"></div>
                        <div class="mock-text-line w60"></div>
                        <div class="mock-stats-row"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <div class="mock-tweet">
                    <div class="mock-avatar"></div>
                    <div class="mock-tweet-body">
                        <div class="mock-name-row"><span class="mock-name"></span><span class="mock-handle"></span></div>
                        <div class="mock-text-line w100"></div>
                        <div class="mock-text-line w70"></div>
                        <div class="mock-media-block"></div>
                        <div class="mock-stats-row"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <div class="mock-tweet">
                    <div class="mock-avatar"></div>
                    <div class="mock-tweet-body">
                        <div class="mock-name-row"><span class="mock-name"></span><span class="mock-handle"></span></div>
                        <div class="mock-text-line w90"></div>
                        <div class="mock-text-line w50"></div>
                        <div class="mock-stats-row"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <div class="mock-branding">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="#1d9bf0"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    TwitExplorer
                </div>
            </div>
        </div>
    </div>
</section>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "TwitExplorer",
  "alternateName": ["Twitter Trend Explorer", "Freedom X", "X Explorer"],
  "url": "<?= $baseUrl ?>",
  "description": "<?= e(__('hero_subtitle')) ?>",
  "inLanguage": ["tr", "en", "ar", "zh", "ru", "fa"],
  "potentialAction": {
    "@type": "SearchAction",
    "target": {
      "@type": "EntryPoint",
      "urlTemplate": "<?= $baseUrl ?>/search?q={search_term_string}"
    },
    "query-input": "required name=search_term_string"
  },
  "publisher": {
    "@type": "Organization",
    "name": "TwitExplorer",
    "url": "<?= $baseUrl ?>",
    "logo": {
      "@type": "ImageObject",
      "url": "<?= $baseUrl ?>/assets/images/og-image.png",
      "width": 1200,
      "height": 630
    },
    "sameAs": [
      "https://freedom-x.net"
    ]
  }
}
</script>

<section style="margin-top: 50px;">
    <h2 class="section-title"><i class="fa-solid fa-circle-question"></i> <?= e(__('popular_searches')) ?> FAQ</h2>
    <div class="faq-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
        <div class="trend-card" style="margin-bottom: 0; cursor: default;">
            <div class="trend-name"><?= e(__('faq_q1')) ?></div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;"><?= e(__('faq_a1')) ?></p>
        </div>
        <div class="trend-card" style="margin-bottom: 0; cursor: default;">
            <div class="trend-name"><?= e(__('faq_q2')) ?></div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;"><?= e(__('faq_a2')) ?></p>
        </div>
        <div class="trend-card" style="margin-bottom: 0; cursor: default;">
            <div class="trend-name"><?= e(__('faq_q3')) ?></div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;"><?= e(__('faq_a3')) ?></p>
        </div>
        <div class="trend-card" style="margin-bottom: 0; cursor: default;">
            <div class="trend-name"><?= e(__('faq_q4')) ?></div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;"><?= e(__('faq_a4')) ?></p>
        </div>
        <div class="trend-card" style="margin-bottom: 0; cursor: default;">
            <div class="trend-name"><?= e(__('faq_q5')) ?></div>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 8px;"><?= e(__('faq_a5')) ?></p>
        </div>
    </div>
</section>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "<?= e(__('faq_q1')) ?>",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "<?= e(__('faq_a1')) ?>"
      }
    },
    {
      "@type": "Question",
      "name": "<?= e(__('faq_q2')) ?>",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "<?= e(__('faq_a2')) ?>"
      }
    },
    {
      "@type": "Question",
      "name": "<?= e(__('faq_q5')) ?>",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "<?= e(__('faq_a5')) ?>"
      }
    }
  ]
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
