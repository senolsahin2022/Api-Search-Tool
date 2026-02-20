<?php
$currentPage = 'hashtag';
$tag = $tag ?? '';

if (empty($tag)) {
    header('Location: /');
    exit;
}

$results = getHashtag($tag);
$using_fallback = false;

$pageTitle = sprintf(__('hashtag_title'), $tag);
$pageDescription = sprintf(__('hashtag_desc'), $tag);
$pageKeywords = $tag . ', ' . __('hashtag') . ', twitter, X, ' . __('search') . ', ' . __('trending');
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
    else {
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
