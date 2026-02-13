<?php
$currentPage = 'user';
$username = $username ?? '';

if (empty($username)) {
    header('Location: /');
    exit;
}

$userData = getUser($username);

$pageTitle = '@' . $username . ' Profili - TwitExplorer';
$pageDescription = $username . ' kullanıcısının sosyal medya profili ve son paylaşımları.';
$pageKeywords = $username . ', twitter profil, kullanıcı, sosyal medya';
$canonicalUrl = '/user/' . urlencode($username);

require __DIR__ . '/../includes/header.php';

if (!empty($userData) && is_array($userData)):
    $tweets = $userData['tweets'] ?? [];

    $firstAuthor = !empty($tweets[0]['author']) ? $tweets[0]['author'] : [];
    $displayName = $firstAuthor['name'] ?? $username;
    $screenName = $firstAuthor['handle'] ?? $username;
    $avatar = $firstAuthor['image'] ?? '';
    $followers = $firstAuthor['followers'] ?? 0;
    $verified = $firstAuthor['verified'] ?? false;

    if ($avatar) {
        $avatarLarge = str_replace('_normal', '_400x400', $avatar);
    } else {
        $avatarLarge = '';
    }

    $pageTitle = $displayName . ' (@' . $screenName . ') - TwitExplorer';
?>

<section class="profile-header">
    <div class="no-banner"></div>
    <div class="profile-info">
        <?php if ($avatarLarge): ?>
            <img src="<?= e($avatarLarge) ?>" alt="<?= e($displayName) ?>" class="profile-avatar" loading="lazy">
        <?php else: ?>
            <div class="profile-avatar" style="background:var(--gradient-1);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:2.5rem;color:white"><?= e(mb_substr($displayName, 0, 1)) ?></div>
        <?php endif; ?>
        <div class="profile-details">
            <h1 class="profile-name">
                <?= e($displayName) ?>
                <?php if ($verified): ?><i class="fa-solid fa-circle-check verified" style="font-size:1.2rem"></i><?php endif; ?>
            </h1>
            <div class="profile-handle">@<?= e($screenName) ?></div>
            <div class="profile-stats-bar">
                <?php if ($followers): ?><span class="profile-stat"><strong><?= formatNumber($followers) ?></strong> Takipçi</span><?php endif; ?>
                <span class="profile-stat"><strong><?= formatNumber(count($tweets)) ?></strong> Gönderi</span>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($tweets) && is_array($tweets)): ?>
<section>
    <h2 class="section-title"><i class="fa-regular fa-newspaper"></i> Son Gönderiler</h2>
    <div class="tweet-list">
        <?php foreach ($tweets as $tweet):
            require __DIR__ . '/../includes/tweet_card.php';
        endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php else: ?>
    <div class="error-page">
        <i class="fa-solid fa-user-xmark"></i>
        <h2>Kullanıcı bulunamadı</h2>
        <p>"<?= e($username) ?>" kullanıcısı bulunamadı veya profil bilgileri alınamadı.</p>
        <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> Ana Sayfa</a>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
