<?php
$currentPage = 'widget';
$tag = $_GET['tag'] ?? 'trending';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$theme = $_GET['theme'] ?? 'dark';

// Get hashtag data
require_once __DIR__ . '/../includes/api.php';
$results = getHashtag($tag);

$tweets = [];
if (isset($results['tweets'])) $tweets = $results['tweets'];
elseif (isset($results['results'])) $tweets = $results['results'];
elseif (isset($results['statuses'])) $tweets = $results['statuses'];
else $tweets = $results['data'] ?? [];

$tweets = array_slice($tweets, 0, $limit);

// Handle embedded display (no header/footer)
if (isset($_GET['embed'])):
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1d9bf0;
            --bg: <?= $theme === 'dark' ? '#000000' : '#ffffff' ?>;
            --bg-card: <?= $theme === 'dark' ? '#16181c' : '#f7f9f9' ?>;
            --text: <?= $theme === 'dark' ? '#e7e9ea' : '#0f1419' ?>;
            --text-secondary: #71767b;
            --border: <?= $theme === 'dark' ? '#2f3336' : '#eff3f4' ?>;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 10px; overflow-x: hidden; }
        .widget-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
        .widget-title { font-weight: 800; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
        .widget-title i { color: var(--primary); }
        .tweet-item { background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 12px; margin-bottom: 10px; font-size: 0.9rem; transition: transform 0.2s; cursor: pointer; text-decoration: none; color: inherit; display: block; }
        .tweet-item:hover { transform: translateY(-2px); border-color: var(--primary); }
        .tweet-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; font-size: 0.8rem; color: var(--text-secondary); }
        .tweet-text { line-height: 1.4; word-wrap: break-word; }
        .footer-link { display: block; text-align: center; font-size: 0.75rem; color: var(--primary); text-decoration: none; margin-top: 10px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="widget-header">
        <div class="widget-title"><i class="fa-brands fa-x-twitter"></i> #<?= htmlspecialchars($tag) ?></div>
    </div>
    <?php foreach ($tweets as $tweet): 
        $tweetId = $tweet['id_str'] ?? '';
        $text = $tweet['full_text'] ?? $tweet['text'] ?? '';
        $author = $tweet['user']['name'] ?? 'Kullanıcı';
    ?>
    <a href="<?= getDomain() ?>/status/<?= $tweetId ?>" target="_blank" class="tweet-item">
        <div class="tweet-meta"><strong><?= htmlspecialchars($author) ?></strong> · <?= timeAgo($tweet['created_at'] ?? '') ?></div>
        <div class="tweet-text"><?= htmlspecialchars(mb_substr($text, 0, 120)) . (mb_strlen($text) > 120 ? '...' : '') ?></div>
    </a>
    <?php endforeach; ?>
    <a href="<?= getDomain() ?>/hashtag/<?= urlencode($tag) ?>" target="_blank" class="footer-link">TwitExplorer'da daha fazlasını gör</a>
</body>
</html>
<?php else: 
    $pageTitle = "Widget Oluşturucu - TwitExplorer";
    require __DIR__ . '/../includes/header.php';
    $baseUrl = getDomain();
?>
<div class="container" style="max-width: 800px; padding: 40px 20px;">
    <h1 class="page-title">Widget Oluşturucu</h1>
    <p class="page-subtitle">Kendi web sitene canlı hashtag akışı ekle.</p>

    <div class="content-preview-card" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <div class="settings">
            <h3 style="margin-bottom: 20px;">Ayarlar</h3>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Hashtag (Sembolsüz)</label>
                <input type="text" id="w-tag" value="bjk" class="downloader-page-input" style="background: var(--bg-card); border: 1px solid var(--border); padding: 10px 15px; border-radius: 10px; width: 100%;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Tweet Sayısı</label>
                <select id="w-limit" class="downloader-page-input" style="background: var(--bg-card); border: 1px solid var(--border); padding: 10px 15px; border-radius: 10px; width: 100%; height: auto;">
                    <option value="3">3</option>
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.9rem;">Tema</label>
                <select id="w-theme" class="downloader-page-input" style="background: var(--bg-card); border: 1px solid var(--border); padding: 10px 15px; border-radius: 10px; width: 100%; height: auto;">
                    <option value="dark" selected>Koyu</option>
                    <option value="light">Açık</option>
                </select>
            </div>
        </div>

        <div class="preview">
            <h3 style="margin-bottom: 20px;">Önizleme</h3>
            <iframe id="w-preview" src="<?= $baseUrl ?>/widget?tag=bjk&limit=5&theme=dark&embed=1" style="width: 100%; height: 500px; border: 1px solid var(--border); border-radius: 15px; background: #000;"></iframe>
        </div>
    </div>

    <div class="content-preview-card" style="margin-top: 30px;">
        <h3 style="margin-bottom: 15px;">Embed Kodu</h3>
        <textarea id="w-code" readonly style="width: 100%; height: 120px; background: #111; color: #00ba7c; border: 1px solid var(--border); border-radius: 12px; padding: 15px; font-family: monospace; font-size: 0.85rem; outline: none; resize: none;"></textarea>
        <button onclick="copyCode()" class="btn btn-primary" style="margin-top: 15px; width: 100%;"><i class="fa-solid fa-copy"></i> Kodu Kopyala</button>
    </div>
</div>

<script>
const baseUrl = 'https://freedom-x.net';
function updateWidget() {
    const tag = document.getElementById('w-tag').value || 'trending';
    const limit = document.getElementById('w-limit').value;
    const theme = document.getElementById('w-theme').value;
    
    const url = `${baseUrl}/widget?tag=${encodeURIComponent(tag)}&limit=${limit}&theme=${theme}&embed=1`;
    document.getElementById('w-preview').src = url;
    
    const code = `<iframe src="${url}" width="100%" height="500" frameborder="0" style="border-radius:15px; border:1px solid #2f3336;"></iframe>`;
    document.getElementById('w-code').value = code;
}

function copyCode() {
    const code = document.getElementById('w-code');
    code.select();
    document.execCommand('copy');
    alert('Embed kodu kopyalandı!');
}

document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('change', updateWidget);
    el.addEventListener('keyup', updateWidget);
});

updateWidget();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
<?php endif; ?>
