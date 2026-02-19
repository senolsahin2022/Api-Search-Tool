<?php
$pageTitle = __('video_downloader_title');
$pageDescription = __('video_downloader_desc');
$canonicalUrl = '/downloader';

require __DIR__ . '/../includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1><?= e($pageTitle) ?></h1>
        <p><?= e($pageDescription) ?></p>
        
        <div class="search-container" style="max-width: 600px; margin: 30px auto;">
            <form action="/downloader" method="POST" class="search-form">
                <input type="text" name="url" placeholder="<?= e(__('enter_url')) ?>" required class="search-input">
                <button type="submit" class="search-button"><?= e(__('download_btn')) ?></button>
            </form>
        </div>
    </div>
</section>

<div class="container">
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
        $url = $_POST['url'];
        // Extract ID from URL
        preg_match('/status\/(\d+)/', $url, $matches);
        $tweetId = $matches[1] ?? null;

        if ($tweetId) {
            $tweet = getTweet($tweetId);
            $tweetData = $tweet['data'] ?? $tweet['tweet'] ?? $tweet['result'] ?? $tweet;
            
            if ($tweetData && !empty($tweetData['media'])) {
                echo '<div class="download-results" style="margin-top: 40px;">';
                echo '<h3>Sonuçlar</h3>';
                echo '<div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">';
                
                foreach ($tweetData['media'] as $media) {
                    $mediaUrl = is_array($media) ? ($media['url'] ?? $media['media_url_https'] ?? '') : $media;
                    $type = $media['type'] ?? 'image';
                    
                    echo '<div class="card" style="padding: 15px; text-align: center;">';
                    if ($type === 'video' || $type === 'animated_gif') {
                        echo '<video controls style="width: 100%; border-radius: 10px; margin-bottom: 15px;"><source src="'.e($mediaUrl).'" type="video/mp4"></video>';
                        echo '<a href="'.e($mediaUrl).'" download class="btn btn-primary" target="_blank"><i class="fa-solid fa-download"></i> Videoyu İndir</a>';
                    } else {
                        echo '<img src="'.e($mediaUrl).'" style="width: 100%; border-radius: 10px; margin-bottom: 15px;">';
                        echo '<a href="'.e($mediaUrl).'" download class="btn btn-primary" target="_blank"><i class="fa-solid fa-download"></i> Resmi İndir</a>';
                    }
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="error-page" style="margin-top: 40px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <p>Bu tweette indirilebilir medya bulunamadı.</p>
                </div>';
            }
        } else {
            echo '<div class="error-page" style="margin-top: 40px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                <p>Geçersiz Tweet URL\'si.</p>
            </div>';
        }
    }
    ?>

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
