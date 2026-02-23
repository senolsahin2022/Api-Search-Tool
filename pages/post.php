<?php
$tweetId = $tweetId ?? '';
if (empty($tweetId)) {
    header('Location: /');
    exit;
}

$tweet = getTweet($tweetId);
$tweetData = $tweet['data'] ?? $tweet['tweet'] ?? $tweet['result'] ?? $tweet;

$content = $tweetData['content'] ?? $tweetData['text'] ?? $tweetData['full_text'] ?? '';
$author = $tweetData['author'] ?? $tweetData['user'] ?? [];
$authorName = $author['name'] ?? 'Twitter';

$cleanContent = strip_tags($content);
$authorHandle = $author['handle'] ?? $author['screen_name'] ?? '';
$pageTitle = sprintf(__('post_title'), $authorHandle ?: $authorName);
$pageDescription = sprintf(__('post_desc'), $authorHandle ?: $authorName, mb_substr($cleanContent, 0, 150));
$pageKeywords = e($authorName) . ', tweet, x explorer, ' . e(__('meta_keywords'));
$canonicalUrl = '/status/' . $tweetId;

require __DIR__ . '/../includes/header.php';
?>

<div class="tweet-detail">
    <?php 
    if ($tweet) {
        if (isset($tweetData['error'])) {
             echo '<div class="error-page">
                <i class="fa-solid fa-circle-exclamation"></i>
                <h2>Hata</h2>
                <p>' . e($tweetData['error']) . '</p>
                <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> ' . e(__('home_btn')) . '</a>
            </div>';
        } else {
            $tweet = $tweetData;
            require __DIR__ . '/../includes/tweet_card.php';
        }
    } else {
        echo '<div class="error-page">
            <i class="fa-solid fa-circle-exclamation"></i>
            <h2>' . e(__('no_results')) . '</h2>
            <p>Tweet bulunamadı veya API hatası oluştu.</p>
            <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> ' . e(__('home_btn')) . '</a>
        </div>';
    }
    ?>

    <!-- FAQ Section for SEO -->
    <section class="faq-section" style="margin-top: 40px; padding: 20px; background: var(--card-bg); border-radius: var(--radius-md); border: 1px solid var(--border);">
        <h2 style="margin-bottom: 20px; font-size: 1.5rem; color: var(--primary);"><?= e($authorName) ?> <?= e(__('faq_title')) ?></h2>
        
        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q1_post'), $author['handle'] ?? $author['screen_name'] ?? 'user') ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a1_post'), $author['handle'] ?? $author['screen_name'] ?? 'user') ?></p>
        </div>

        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q2_post'), $author['handle'] ?? $author['screen_name'] ?? 'user') ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a2_post'), $author['handle'] ?? $author['screen_name'] ?? 'user') ?></p>
        </div>

        <div class="faq-item" style="margin-bottom: 15px;">
            <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?= sprintf(__('faq_q3_post'), $author['handle'] ?? $author['screen_name'] ?? 'user') ?></h3>
            <p style="color: var(--text-muted); line-height: 1.6;"><?= sprintf(__('faq_a3_post'), $author['handle'] ?? $author['screen_name'] ?? 'user') ?></p>
        </div>
    </section>

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [{
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q1_post'), $author['handle'] ?? $author['screen_name'] ?? 'user')) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a1_post'), $author['handle'] ?? $author['screen_name'] ?? 'user')) ?>"
        }
      }, {
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q2_post'), $author['handle'] ?? $author['screen_name'] ?? 'user')) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a2_post'), $author['handle'] ?? $author['screen_name'] ?? 'user')) ?>"
        }
      }, {
        "@type": "Question",
        "name": "<?= addslashes(sprintf(__('faq_q3_post'), $author['handle'] ?? $author['screen_name'] ?? 'user')) ?>",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "<?= addslashes(sprintf(__('faq_a3_post'), $author['handle'] ?? $author['screen_name'] ?? 'user')) ?>"
        }
      }]
    }
    </script>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
