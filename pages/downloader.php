<?php
require_once __DIR__ . '/../includes/api.php';

$pageTitle = __('video_downloader_title') . ' - TwitExplorer';
$pageDescription = __('video_downloader_desc') . ' ' . __('hero_subtitle');
$pageKeywords = 'video downloader, x video indir, twitter video download, ' . __('meta_keywords');
$canonicalUrl = '/downloader';

require __DIR__ . '/../includes/header.php';
?>

<div class="downloader-page">
    <section class="hero hero-small">
        <div class="container">
            <h1 class="animate-up"><?= e(__('video_downloader_title')) ?></h1>
            <p class="animate-up" style="animation-delay: 0.1s;"><?= e(__('video_downloader_desc')) ?></p>
            
            <div class="search-container animate-up" style="animation-delay: 0.2s; max-width: 700px; margin: 30px auto;">
                <form action="/downloader" method="POST" class="downloader-page-form shadow-lg">
                    <input type="text" name="url" placeholder="https://x.com/kullanici/status/2023151190113222658" required class="downloader-page-input" value="<?= e($_POST['url'] ?? '') ?>">
                    <button type="submit" class="downloader-page-button">
                        <i class="fa-solid fa-download"></i> <?= e(__('download_btn')) ?>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div style="margin-bottom: 30px;">
            <button onclick="window.history.back()" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> <?= e(__('back_home')) ?>
            </button>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
            $url = $_POST['url'];
            preg_match('/status\/(\d+)/', $url, $matches);
            $tweetId = $matches[1] ?? null;

            if ($tweetId) {
                $tweetData = getTweet($tweetId);
                
                if ($tweetData && !empty($tweetData['id_str'])) {
                    $userName = $tweetData['user']['name'] ?? '';
                    $screenName = $tweetData['user']['screen_name'] ?? '';
                    $avatar = $tweetData['user']['profile_image_url_https'] ?? '';
                    $tweetText = $tweetData['text'] ?? '';
                    $createdAt = $tweetData['created_at'] ?? '';

                    $videos = [];
                    $images = [];

                    if (!empty($tweetData['mediaDetails']) && is_array($tweetData['mediaDetails'])) {
                        foreach ($tweetData['mediaDetails'] as $media) {
                            $type = $media['type'] ?? '';
                            $thumb = $media['media_url_https'] ?? '';

                            if (($type === 'video' || $type === 'animated_gif') && !empty($media['video_info']['variants'])) {
                                $mp4Variants = [];
                                foreach ($media['video_info']['variants'] as $variant) {
                                    if (isset($variant['content_type']) && $variant['content_type'] === 'video/mp4') {
                                        $mp4Variants[] = [
                                            'url' => $variant['url'],
                                            'bitrate' => $variant['bitrate'] ?? 0,
                                        ];
                                    }
                                }
                                usort($mp4Variants, fn($a, $b) => $b['bitrate'] - $a['bitrate']);
                                if (!empty($mp4Variants)) {
                                    $videos[] = [
                                        'variants' => $mp4Variants,
                                        'thumbnail' => $thumb,
                                        'duration' => $media['video_info']['duration_millis'] ?? 0,
                                        'type' => $type,
                                    ];
                                }
                            } elseif ($type === 'photo' && $thumb) {
                                $images[] = $thumb;
                            }
                        }
                    }

                    if (empty($videos) && !empty($tweetData['video']['variants'])) {
                        $mp4Variants = [];
                        foreach ($tweetData['video']['variants'] as $variant) {
                            $vUrl = $variant['src'] ?? $variant['url'] ?? '';
                            if ($vUrl && (strpos($vUrl, '.mp4') !== false || ($variant['content_type'] ?? '') === 'video/mp4')) {
                                $mp4Variants[] = [
                                    'url' => $vUrl,
                                    'bitrate' => $variant['bitrate'] ?? 0,
                                ];
                            }
                        }
                        usort($mp4Variants, fn($a, $b) => $b['bitrate'] - $a['bitrate']);
                        if (!empty($mp4Variants)) {
                            $thumb = $tweetData['photos'][0]['url'] ?? '';
                            $videos[] = [
                                'variants' => $mp4Variants,
                                'thumbnail' => $thumb,
                                'duration' => 0,
                                'type' => 'video',
                            ];
                        }
                    }

                    if (empty($images) && !empty($tweetData['photos']) && is_array($tweetData['photos'])) {
                        foreach ($tweetData['photos'] as $photo) {
                            $pUrl = $photo['url'] ?? $photo['media_url_https'] ?? '';
                            if ($pUrl) $images[] = $pUrl;
                        }
                    }
                    ?>

                    <div class="download-results animate-up">
                        <div class="content-preview-card">
                            <div class="dl-tweet-header">
                                <?php if ($avatar): ?>
                                    <img src="<?= e($avatar) ?>" alt="<?= e($userName) ?>" class="dl-avatar">
                                <?php endif; ?>
                                <div>
                                    <div class="dl-name"><?= e($userName) ?></div>
                                    <div class="dl-handle">@<?= e($screenName) ?></div>
                                </div>
                                <?php if ($createdAt): ?>
                                    <span class="dl-time"><?= timeAgo($createdAt) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($tweetText): ?>
                                <div class="dl-text"><?= e($tweetText) ?></div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($videos)): ?>
                            <?php foreach ($videos as $vi => $video): ?>
                                <div class="dl-media-card">
                                    <div class="dl-media-badge">
                                        <span class="badge badge-video"><i class="fa-solid fa-video"></i> <?= $video['type'] === 'animated_gif' ? 'GIF' : 'MP4 Video' ?></span>
                                        <?php if ($video['duration']): ?>
                                            <span class="dl-duration"><i class="fa-regular fa-clock"></i> <?= gmdate($video['duration'] > 3600000 ? 'H:i:s' : 'i:s', intval($video['duration'] / 1000)) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="video-container">
                                        <video controls preload="metadata" poster="<?= e($video['thumbnail']) ?>" style="width:100%;border-radius:12px;max-height:500px;background:#000;display:block;">
                                            <source src="/video-proxy?u=<?= urlencode(base64_encode($video['variants'][0]['url'])) ?>" type="video/mp4">
                                        </video>
                                    </div>

                                    <div class="dl-quality-grid">
                                        <?php foreach ($video['variants'] as $qi => $q):
                                            $label = 'SD';
                                            if ($q['bitrate'] >= 2000000) $label = 'HD 1080p';
                                            elseif ($q['bitrate'] >= 800000) $label = 'HD 720p';
                                            elseif ($q['bitrate'] >= 500000) $label = 'SD 480p';
                                            elseif ($q['bitrate'] > 0) $label = 'Low ' . round($q['bitrate'] / 1000) . 'k';
                                        ?>
                                            <a href="/video-proxy?u=<?= urlencode(base64_encode($q['url'])) ?>" class="dl-quality-btn <?= $qi === 0 ? 'best' : '' ?>" download>
                                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                                <span><?= $label ?></span>
                                                <?php if ($qi === 0): ?><span class="dl-best-tag">BEST</span><?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php elseif (!empty($images)): ?>
                            <div class="dl-media-card">
                                <span class="badge badge-image"><i class="fa-solid fa-image"></i> HD Image</span>
                                <div class="dl-images-grid">
                                    <?php foreach ($images as $img): ?>
                                        <div class="dl-image-item">
                                            <img src="<?= e($img) ?>" alt="<?= e($userName) ?>" loading="lazy">
                                            <a href="<?= e($img) ?>:orig" target="_blank" download class="dl-img-download">
                                                <i class="fa-solid fa-cloud-arrow-down"></i> <?= e(__('download_btn')) ?> (Original)
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="dl-no-media">
                                <i class="fa-solid fa-photo-film"></i>
                                <p>Bu tweette indirilebilir video veya medya bulunamadı.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                } else {
                    echo '<div class="dl-error"><i class="fa-solid fa-circle-exclamation"></i> ' . e(__('tweet_not_found')) . '</div>';
                }
            } else {
                echo '<div class="dl-error"><i class="fa-solid fa-circle-info"></i> Geçersiz URL. Lütfen geçerli bir tweet linki girin.</div>';
            }
        }
        ?>

    <div class="faq-section mt-5 animate-up">
        <h2 class="section-title"><?= e(__('video_downloader_title')) ?> - FAQ</h2>
        <div class="faq-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="faq-card shadow-sm" style="padding: 20px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q1')) ?></h3>
                <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);"><?= e(__('faq_a1')) ?></p>
            </div>
            <div class="faq-card shadow-sm" style="padding: 20px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q2')) ?></h3>
                <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);"><?= e(__('faq_a2')) ?></p>
            </div>
            <div class="faq-card shadow-sm" style="padding: 20px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
                <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q3')) ?></h3>
                <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);"><?= e(__('faq_a3')) ?></p>
            </div>
        </div>
    </div>
    </div>
</div>

<style>
.downloader-page { min-height: 100vh; }
.hero-small { padding: 60px 0; border-bottom: 1px solid var(--border); }
.content-preview-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 20px; padding: 24px; margin-bottom: 20px; }
.dl-tweet-header { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
.dl-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
.dl-name { font-weight: 700; font-size: 1rem; }
.dl-handle { color: var(--text-secondary); font-size: 0.85rem; }
.dl-time { margin-left: auto; color: var(--text-secondary); font-size: 0.8rem; }
.dl-text { font-size: 1.05rem; line-height: 1.65; color: var(--text); word-wrap: break-word; }
.dl-media-card { background: var(--bg-secondary); border: 1px solid var(--border); border-radius: 20px; padding: 24px; margin-bottom: 20px; }
.dl-media-badge { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.dl-duration { font-size: 0.82rem; color: var(--text-secondary); display: flex; align-items: center; gap: 4px; }
.video-container { margin-bottom: 20px; border-radius: 12px; overflow: hidden; background: #000; }
.video-container video { width: 100%; display: block; max-height: 500px; }
.dl-quality-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; }
.dl-quality-btn { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 12px; background: var(--bg); border: 1px solid var(--border); color: var(--text); text-decoration: none; font-weight: 600; font-size: 0.88rem; transition: all 0.3s; position: relative; }
.dl-quality-btn:hover { border-color: var(--primary); background: rgba(29, 155, 240, 0.1); color: var(--primary); transform: translateY(-2px); }
.dl-quality-btn.best { background: linear-gradient(135deg, rgba(29, 155, 240, 0.15), rgba(120, 86, 255, 0.15)); border-color: var(--primary); }
.dl-best-tag { position: absolute; top: -6px; right: -4px; background: linear-gradient(135deg, #1d9bf0, #7856ff); color: #fff; font-size: 0.6rem; padding: 2px 6px; border-radius: 6px; font-weight: 800; letter-spacing: 0.5px; }
.dl-images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-top: 14px; }
.dl-image-item { border-radius: 14px; overflow: hidden; border: 1px solid var(--border); background: var(--bg); }
.dl-image-item img { width: 100%; height: auto; display: block; }
.dl-img-download { display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px; background: var(--gradient-1); color: #fff; text-decoration: none; font-weight: 600; font-size: 0.88rem; transition: all 0.3s; }
.dl-img-download:hover { filter: brightness(1.15); }
.dl-no-media { text-align: center; padding: 40px 20px; color: var(--text-secondary); }
.dl-no-media i { font-size: 3rem; margin-bottom: 16px; display: block; color: var(--text-secondary); }
.dl-error { background: rgba(249, 24, 128, 0.1); border: 1px solid rgba(249, 24, 128, 0.3); color: var(--danger); padding: 16px 24px; border-radius: 14px; font-weight: 600; display: flex; align-items: center; gap: 10px; font-size: 0.95rem; }
.badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
.badge-video { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
.badge-image { background: rgba(52, 152, 219, 0.2); color: #3498db; }
.py-5 { padding-top: 3rem; padding-bottom: 3rem; }
.mt-5 { margin-top: 3rem; }
</style>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "<?= addslashes(__('faq_q1')) ?>",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "<?= addslashes(__('faq_a1')) ?>"
    }
  }, {
    "@type": "Question",
    "name": "<?= addslashes(__('faq_q2')) ?>",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "<?= addslashes(__('faq_a2')) ?>"
    }
  }, {
    "@type": "Question",
    "name": "<?= addslashes(__('faq_q3')) ?>",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "<?= addslashes(__('faq_a3')) ?>"
    }
  }]
}
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
