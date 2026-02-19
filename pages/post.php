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
        <div class="faq-grid">
            <div class="faq-item">
                <h3>Bu tweeti nasıl indirebilirim?</h3>
                <p>Tweet içerisindeki videoları veya resimleri indirmek için üst menüdeki "Video İndirici" sayfasını kullanabilirsiniz.</p>
            </div>
            <div class="faq-item">
                <h3>Tweet içeriği güncel mi?</h3>
                <p>Evet, TwitExplorer verileri doğrudan kaynaktan anlık olarak çekmektedir.</p>
            </div>
            <div class="faq-item">
                <h3>Paylaşım yapabilir miyim?</h3>
                <p>Tweet kartının altındaki paylaşım butonlarını kullanarak bu içeriği X, Facebook veya WhatsApp üzerinden paylaşabilirsiniz.</p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
