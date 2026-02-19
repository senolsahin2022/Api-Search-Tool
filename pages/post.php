<?php
$tweetId = $tweetId ?? '';
if (empty($tweetId)) {
    header('Location: /');
    exit;
}

$tweet = getTweet($tweetId);
$tweetData = $tweet['data'] ?? $tweet['tweet'] ?? $tweet['result'] ?? $tweet;

$content = $tweetData['content'] ?? $tweetData['text'] ?? '';
$authorName = $tweetData['author']['name'] ?? 'Twitter';

$pageTitle = $authorName . ' Tweet - TwitExplorer';
$pageDescription = mb_substr(strip_tags($content), 0, 160);
$pageKeywords = __('meta_keywords') . ', tweet detail, ' . $authorName;
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

    <div class="faq-section" style="margin-top: 40px;">
        <h2><?= e($authorName) ?> Tweeti Hakkında SSS</h2>
        <div class="faq-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="faq-item card shadow-sm" style="padding: 20px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--accent);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q1')) ?></h3>
                <p style="font-size: 0.95rem; line-height: 1.5;"><?= e(__('faq_a1')) ?></p>
            </div>
            <div class="faq-item card shadow-sm" style="padding: 20px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--accent);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q2')) ?></h3>
                <p style="font-size: 0.95rem; line-height: 1.5;"><?= e(__('faq_a2')) ?></p>
            </div>
            <div class="faq-item card shadow-sm" style="padding: 20px; background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--accent);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q3')) ?></h3>
                <p style="font-size: 0.95rem; line-height: 1.5;"><?= e(__('faq_a3')) ?></p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
