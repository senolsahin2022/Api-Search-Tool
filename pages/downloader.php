<?php
$pageTitle = __('video_downloader_title');
$pageDescription = __('video_downloader_desc');
$canonicalUrl = '/downloader';

require __DIR__ . '/../includes/header.php';
?>

<div class="downloader-page">
    <section class="hero hero-small">
        <div class="container">
            <h1 class="animate-up"><?= e($pageTitle) ?></h1>
            <p class="animate-up" style="animation-delay: 0.1s;"><?= e($pageDescription) ?></p>
            
            <div class="search-container animate-up" style="animation-delay: 0.2s; max-width: 700px; margin: 30px auto;">
                <form action="/downloader" method="POST" class="downloader-page-form shadow-lg">
                    <input type="text" name="url" placeholder="https://x.com/kullanici/status/2023151190113222658" required class="downloader-page-input">
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
                $tweet = getTweet($tweetId);
                $tweetData = $tweet['data'] ?? $tweet['tweet'] ?? $tweet['result'] ?? $tweet;
                
                if ($tweetData) {
                    echo '<div class="download-results animate-up">';
                    echo '<h2 class="section-title"><i class="fa-solid fa-circle-check"></i> ' . e(__('search_results')) . '</h2>';
                    
                    // Tweet Content Card
                    echo '<div class="content-preview-card shadow-sm">';
                    if (!empty($tweetData['content'] ?? $tweetData['text'])) {
                        echo '<div class="tweet-content-text">' . e($tweetData['content'] ?? $tweetData['text']) . '</div>';
                    }
                    
                    if (!empty($tweetData['media'])) {
                        echo '<div class="media-grid">';
                        foreach ($tweetData['media'] as $media) {
                            $mediaUrl = is_array($media) ? ($media['url'] ?? $media['media_url_https'] ?? '') : $media;
                            
                            // Extract video variants
                            $videoUrl = '';
                            $videoInfo = null;
                            
                            // 1. Root video object (Based on user reference)
                            if (isset($tweetData['video']['variants'])) {
                                foreach ($tweetData['video']['variants'] as $variant) {
                                    if (isset($variant['src']) && strpos($variant['src'], '.mp4') !== false) {
                                        $videoUrl = $variant['src']; // Last one is usually highest quality
                                    }
                                }
                            }

                            // 2. mediaDetails fallback (Based on user reference)
                            if (!$videoUrl && isset($tweetData['mediaDetails'])) {
                                foreach ($tweetData['mediaDetails'] as $mDet) {
                                    if (isset($mDet['video_info']['variants'])) {
                                        $maxBitrate = -1;
                                        foreach ($mDet['video_info']['variants'] as $variant) {
                                            if (
                                                isset($variant['content_type']) &&
                                                $variant['content_type'] === 'video/mp4' &&
                                                isset($variant['bitrate']) &&
                                                $variant['bitrate'] > $maxBitrate
                                            ) {
                                                $maxBitrate = $variant['bitrate'];
                                                $videoUrl = $variant['url'];
                                            }
                                        }
                                    }
                                }
                            }

                            // 3. Deep fallback (Recursive)
                            if (!$videoUrl) {
                                array_walk_recursive($tweetData, function($val, $key) use (&$videoUrl) {
                                    if (is_string($val) && strpos($val, '.mp4') !== false && !$videoUrl) {
                                        if (strpos($val, 'video') !== false || strpos($val, 'ext_tw_video') !== false) {
                                            $videoUrl = $val;
                                        }
                                    }
                                });
                            }

                            if ($videoUrl) {
                                echo '<div class="video-container" style="margin-bottom: 20px;">';
                                echo '<video controls preload="metadata" class="w-100 rounded shadow" style="max-height: 500px; background: #000; display: block; width: 100%;" poster="'.e($media['url'] ?? $media['media_url_https'] ?? '').'">';
                                echo '<source src="'.e($videoUrl).'" type="video/mp4">';
                                echo 'Your browser does not support the video tag.';
                                echo '</video>';
                                echo '</div>';
                                echo '<div class="media-info">';
                                echo '<span class="badge badge-video"><i class="fa-solid fa-video"></i> MP4 Video</span>';
                                echo '<div style="display: flex; gap: 10px; margin-top: 15px;">';
                                echo '<a href="'.e($videoUrl).'" class="btn btn-download-source" target="_blank" download style="flex: 1; justify-content: center; display: flex; align-items: center; gap: 8px;">';
                                echo '<i class="fa-solid fa-cloud-arrow-down"></i> ' . e(__('download_btn')) . ' (Video)';
                                echo '</a>';
                                echo '<a href="'.e($videoUrl).'" class="btn" target="_blank" style="background: var(--bg-secondary); border: 1px solid var(--border); color: var(--text); display: flex; align-items: center; justify-content: center; width: 50px;"><i class="fa-solid fa-up-right-from-square"></i></a>';
                                echo '</div>';
                                echo '</div>';
                            } else {
                                echo '<div class="image-container">';
                                echo '<img src="'.e($mediaUrl).'" alt="Media Content" class="w-100 rounded">';
                                echo '</div>';
                                echo '<div class="media-info">';
                                echo '<span class="badge badge-image"><i class="fa-solid fa-image"></i> HD Image</span>';
                                echo '<a href="'.e($mediaUrl).'" class="btn btn-download-source mt-3" target="_blank" download>';
                                echo '<i class="fa-solid fa-cloud-arrow-down"></i> ' . e(__('download_btn')) . ' (Image)';
                                echo '</a>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        echo '</div>';
                    }
                    echo '</div>'; // End Content Card
                    echo '</div>';
                } else {
                    echo '<div class="alert alert-danger animate-up"><i class="fa-solid fa-circle-exclamation"></i> ' . e(__('user_not_found_text')) . '</div>';
                }
            } else {
                echo '<div class="alert alert-warning animate-up"><i class="fa-solid fa-circle-info"></i> Invalid URL. Please enter a valid status link.</div>';
            }
        }
        ?>

    <div class="faq-section mt-5 animate-up">
        <h2 class="section-title"><?= e(__('video_downloader_title')) ?> - FAQ</h2>
        <div class="faq-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="faq-card shadow-sm" style="padding: 20px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
                <div class="faq-header">
                    <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q1')) ?></h3>
                </div>
                <div class="faq-body">
                    <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);"><?= e(__('faq_a1')) ?></p>
                </div>
            </div>
            <div class="faq-card shadow-sm" style="padding: 20px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
                <div class="faq-header">
                    <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q2')) ?></h3>
                </div>
                <div class="faq-body">
                    <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);"><?= e(__('faq_a2')) ?></p>
                </div>
            </div>
            <div class="faq-card shadow-sm" style="padding: 20px; background: var(--bg-secondary); border-radius: 12px; border: 1px solid var(--border);">
                <div class="faq-header">
                    <h3 style="font-size: 1.1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-circle-question"></i> <?= e(__('faq_q3')) ?></h3>
                </div>
                <div class="faq-body">
                    <p style="font-size: 0.95rem; line-height: 1.5; color: var(--text-secondary);"><?= e(__('faq_a3')) ?></p>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<style>
.downloader-page { background: var(--bg-body); min-height: 100vh; }
.hero-small { padding: 60px 0; background: var(--gradient-dark); border-bottom: 1px solid var(--border); }
.btn-back { background: var(--bg-card); color: var(--text); border: 1px solid var(--border); padding: 10px 20px; border-radius: 30px; cursor: pointer; transition: all 0.3s ease; font-weight: 500; }
.btn-back:hover { background: var(--accent); color: white; border-color: var(--accent); transform: translateX(-5px); }
.content-preview-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: 20px; padding: 30px; margin-top: 20px; }
.tweet-content-text { font-size: 1.25rem; line-height: 1.6; margin-bottom: 30px; color: var(--text); }
.media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
.media-item-card { background: rgba(255,255,255,0.03); border: 1px solid var(--border); border-radius: 15px; overflow: hidden; padding: 15px; }
.btn-download-source { display: flex; align-items: center; justify-content: center; gap: 10px; background: var(--gradient-1); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: 600; text-decoration: none; width: 100%; transition: transform 0.2s; }
.btn-download-source:hover { transform: translateY(-2px); opacity: 0.9; }
.badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; margin-bottom: 10px; }
.badge-video { background: rgba(231, 76, 60, 0.2); color: #e74c3c; }
.badge-image { background: rgba(52, 152, 219, 0.2); color: #3498db; }
.section-title { font-size: 1.8rem; font-weight: 700; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; color: var(--text); }
.w-100 { width: 100%; }
.rounded { border-radius: 12px; }
.py-5 { padding-top: 3rem; padding-bottom: 3rem; }
.mt-5 { margin-top: 3rem; }
.mt-3 { margin-top: 1rem; }
.shadow-sm { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
.shadow-lg { box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
</style>

    <div class="faq-section" style="margin-top: 60px;">
        <h2><?= e(__('video_downloader_title')) ?> Hakkında Sıkça Sorulan Sorular</h2>
        <div class="faq-grid">
            <div class="faq-item">
                <h3>X (Twitter) videolarını nasıl indirebilirim?</h3>
                <p>İndirmek istediğiniz videonun bulunduğu tweet linkini yukarıdaki kutucuğa yapıştırın ve "İndir" butonuna basın. Ardından karşınıza çıkan indirme butonlarını kullanarak videoyu cihazınıza kaydedebilirsiniz.</p>
            </div>
            <div class="faq-item">
                <h3>Hangi formatları destekliyorsunuz?</h3>
                <p>MP4 video formatını ve yüksek kaliteli JPG/PNG resim formatlarını destekliyoruz. Ayrıca GIF'leri de video olarak indirebilirsiniz.</p>
            </div>
            <div class="faq-item">
                <h3>Ücretli mi?</h3>
                <p>Hayır, TwitExplorer Video İndirici tamamen ücretsizdir ve herhangi bir sınırlama yoktur.</p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
