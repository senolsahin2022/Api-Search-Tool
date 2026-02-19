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
        // Single tweet detail view
        require __DIR__ . '/../includes/tweet_card.php';
    } else {
        echo '<div class="error-page"><h2>' . e(__('no_results')) . '</h2></div>';
    }
    ?>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
