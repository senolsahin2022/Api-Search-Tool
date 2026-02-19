<?php
$tweetId = $tweetId ?? '';
if (empty($tweetId)) {
    header('Location: /');
    exit;
}

$tweet = getTweet($tweetId);

$pageTitle = __('post_title');
$pageDescription = __('post_desc');
$canonicalUrl = '/status/' . $tweetId;

require __DIR__ . '/../includes/header.php';
?>

<div class="tweet-detail">
    <?php 
    if ($tweet) {
        $tweetData = $tweet['data'] ?? $tweet['tweet'] ?? $tweet['result'] ?? $tweet;
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
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
