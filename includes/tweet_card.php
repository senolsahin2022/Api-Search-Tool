<?php
$tweetId = $tweet['id'] ?? $tweet['tweet_id'] ?? $tweet['id_str'] ?? '';
$text = $tweet['content'] ?? $tweet['text'] ?? $tweet['full_text'] ?? '';
$engagement = $tweet['engagement'] ?? [];
$author = $tweet['author'] ?? $tweet['user'] ?? [];
$name = $author['name'] ?? $tweet['name'] ?? 'Kullanıcı';
$screenName = $author['handle'] ?? $author['screen_name'] ?? $author['username'] ?? $tweet['screen_name'] ?? '';
$avatar = $author['image'] ?? $author['profile_image_url_https'] ?? $author['profile_image_url'] ?? $author['avatar'] ?? '';
$verified = $author['verified'] ?? false;
$createdAt = $tweet['date'] ?? $tweet['created_at'] ?? '';
$likes = $engagement['likes'] ?? $tweet['favorite_count'] ?? $tweet['likes'] ?? 0;
$retweets = $engagement['retweets'] ?? $tweet['retweet_count'] ?? $tweet['retweets'] ?? 0;
$replies = $engagement['replies'] ?? $tweet['reply_count'] ?? $tweet['replies'] ?? 0;
$quotes = $engagement['quotes'] ?? $tweet['quotes'] ?? 0;
$views = $tweet['views'] ?? $tweet['view_count'] ?? 0;

$mediaUrl = '';
if (!empty($tweet['media']) && is_array($tweet['media'])) {
    $firstMedia = $tweet['media'][0] ?? null;
    if ($firstMedia) {
        $mediaUrl = is_array($firstMedia) ? ($firstMedia['url'] ?? $firstMedia['media_url_https'] ?? '') : $firstMedia;
    }
}

$linkedText = preg_replace(
    ['/#(\w+)/', '/@(\w+)/', '/(https?:\/\/\S+)/'],
    ['<a href="/hashtag/$1">#$1</a>', '<a href="/user/$1">@$1</a>', '<a href="$1" target="_blank" rel="nofollow">$1</a>'],
    e($text)
);
?>
<article class="tweet-card" onclick="window.location.href='/status/<?= e($tweetId) ?>'" style="cursor: pointer;">
    <div class="tweet-header">
        <?php if ($avatar): ?>
            <img src="<?= e($avatar) ?>" alt="<?= e($name) ?>" class="tweet-avatar" loading="lazy">
        <?php else: ?>
            <div class="tweet-avatar" style="background:var(--gradient-1);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.2rem"><?= e(mb_substr($name, 0, 1)) ?></div>
        <?php endif; ?>
        <div class="tweet-user-info">
            <div class="tweet-name">
                <a href="/user/<?= e($screenName) ?>" onclick="event.stopPropagation()"><?= e($name) ?></a>
                <?php if ($verified): ?><i class="fa-solid fa-circle-check verified"></i><?php endif; ?>
            </div>
            <div class="tweet-handle">@<?= e($screenName) ?></div>
        </div>
        <?php if ($createdAt): ?>
            <span class="tweet-time"><?= timeAgo($createdAt) ?></span>
        <?php endif; ?>
    </div>
    <div class="tweet-text" onclick="window.location.href='/status/<?= e($tweetId) ?>'"><?= $linkedText ?></div>
    <?php if ($mediaUrl): ?>
        <div class="tweet-media" onclick="window.location.href='/status/<?= e($tweetId) ?>'">
            <?php 
            $is_video = false;
            if (!empty($tweet['media']) && is_array($tweet['media'])) {
                foreach ($tweet['media'] as $m) {
                    if (isset($m['type']) && ($m['type'] === 'video' || $m['type'] === 'animated_gif')) {
                        $is_video = true;
                        break;
                    }
                }
            }
            ?>
            <?php if ($is_video): ?>
                <video controls preload="metadata" poster="<?= e($mediaUrl) ?>">
                    <source src="<?= e($mediaUrl) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <img src="<?= e($mediaUrl) ?>" alt="Medya içeriği" loading="lazy">
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="tweet-stats">
        <span class="tweet-stat"><i class="fa-regular fa-comment"></i> <?= formatNumber($replies) ?></span>
        <span class="tweet-stat retweets"><i class="fa-solid fa-retweet"></i> <?= formatNumber($retweets) ?></span>
        <span class="tweet-stat likes"><i class="fa-regular fa-heart"></i> <?= formatNumber($likes) ?></span>
        <?php if ($quotes): ?><span class="tweet-stat"><i class="fa-solid fa-quote-right"></i> <?= formatNumber($quotes) ?></span><?php endif; ?>
        <?php if ($views): ?><span class="tweet-stat"><i class="fa-solid fa-chart-simple"></i> <?= formatNumber($views) ?></span><?php endif; ?>
    </div>
    <div class="share-buttons" onclick="event.stopPropagation()" style="margin-top: 15px; display: flex; gap: 10px; border-top: 1px solid var(--border); padding-top: 10px;">
        <a href="https://twitter.com/intent/tweet?text=<?= urlencode($text) ?>&url=<?= urlencode($baseUrl . '/status/' . $tweetId) ?>" target="_blank" class="tag-link" style="font-size: 0.75rem; background: #000; color: #fff;"><i class="fa-brands fa-x-twitter"></i> Paylaş</a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($baseUrl . '/status/' . $tweetId) ?>" target="_blank" class="tag-link" style="font-size: 0.75rem; background: #1877f2; color: #fff;"><i class="fa-brands fa-facebook"></i> Facebook</a>
        <a href="https://wa.me/?text=<?= urlencode($text . ' ' . $baseUrl . '/status/' . $tweetId) ?>" target="_blank" class="tag-link" style="font-size: 0.75rem; background: #25d366; color: #fff;"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
        <button onclick="event.stopPropagation(); navigator.clipboard.writeText('<?= $baseUrl . '/status/' . $tweetId ?>').then(() => alert('Link kopyalandı!'))" class="tag-link" style="font-size: 0.75rem; border: none; cursor: pointer;"><i class="fa-solid fa-link"></i> Kopyala</button>
    </div>
</article>
