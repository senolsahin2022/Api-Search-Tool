<!DOCTYPE html>
<html lang="<?= e($lang) ?>" dir="<?= in_array($lang, ['ar', 'fa']) ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? __('meta_title')) ?></title>
    <meta name="description" content="<?= e($pageDescription ?? __('meta_desc')) ?>">
    <meta name="keywords" content="<?= e($pageKeywords ?? __('meta_keywords')) ?>">
    <meta name="robots" content="<?= !empty($noindex) ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1' ?>">
    <?php
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if (strpos($host, 'replit') !== false || strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $baseUrl = $protocol . $host;
    } else {
        $baseUrl = 'https://' . $host;
    }
    ?>
    <link rel="canonical" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . ($lang !== 'tr' ? (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') . 'lang=' . $lang : '') ?>">
    <meta property="og:title" content="<?= e($pageTitle ?? __('meta_title')) ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? __('meta_desc')) ?>">
    <meta property="og:url" content="<?= e($baseUrl . ($canonicalUrl ?? '/')) . ($lang !== 'tr' ? (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') . 'lang=' . $lang : '') ?>">
    <meta property="og:site_name" content="TwitExplorer">
    <meta property="og:image" content="<?= e($ogImage ?? $baseUrl . '/assets/images/og-image.png') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:type" content="<?= e($ogType ?? 'website') ?>">
    <?php if (!empty($ogProfile)): ?>
    <meta property="profile:username" content="<?= e($ogProfile) ?>">
    <?php endif; ?>
    <?php if (!empty($articleDate)): ?>
    <meta property="article:published_time" content="<?= e($articleDate) ?>">
    <meta property="article:author" content="<?= e($authorName ?? '') ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle ?? __('meta_title')) ?>">
    <meta name="twitter:description" content="<?= e($pageDescription ?? __('meta_desc')) ?>">
    <meta name="twitter:image" content="<?= e($ogImage ?? $baseUrl . '/assets/images/og-image.png') ?>">
    
    <link rel="alternate" hreflang="tr" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') ?>lang=tr">
    <link rel="alternate" hreflang="en" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') ?>lang=en">
    <link rel="alternate" hreflang="ar" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') ?>lang=ar">
    <link rel="alternate" hreflang="zh" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') ?>lang=zh">
    <link rel="alternate" hreflang="ru" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') ?>lang=ru">
    <link rel="alternate" hreflang="fa" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) . (str_contains($canonicalUrl ?? '', '?') ? '&' : '?') ?>lang=fa">
    <link rel="alternate" hreflang="x-default" href="<?= e($baseUrl . ($canonicalUrl ?? '/')) ?>">
    
    <link rel="icon" type="image/svg+xml" href="/assets/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <?php if (($currentPage ?? '') !== 'widget'): ?>
    <div class="promo-bar" id="promoBar">
        <div class="container promo-bar-inner">
            <div class="promo-slide active" id="promoSlide1">
                <div class="promo-bar-content">
                    <span class="promo-bar-badge">NEW</span>
                    <span class="promo-bar-text"><i class="fa-solid fa-cube"></i> <?= e(__('promo_bar')) ?></span>
                </div>
                <div class="promo-bar-actions">
                    <a href="/widget" class="promo-bar-cta"><?= e(__('promo_bar_cta')) ?> <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="promo-slide" id="promoSlide2">
                <div class="promo-bar-content">
                    <span class="promo-bar-badge promo-bar-badge-dl"><i class="fa-solid fa-download"></i></span>
                    <span class="promo-bar-text"><i class="fa-solid fa-video"></i> <?= e(__('promo_bar_dl')) ?></span>
                </div>
                <div class="promo-bar-actions">
                    <a href="/downloader" class="promo-bar-cta promo-bar-cta-dl"><?= e(__('promo_bar_dl_cta')) ?> <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <button class="promo-bar-close" onclick="document.getElementById('promoBar').style.display='none';sessionStorage.setItem('promoBarClosed','1');" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
        </div>
    </div>
    <script>
    if(sessionStorage.getItem('promoBarClosed')){document.getElementById('promoBar').style.display='none';}
    else{(function(){var s1=document.getElementById('promoSlide1'),s2=document.getElementById('promoSlide2'),c=0;setInterval(function(){c=1-c;s1.classList.toggle('active',c===0);s2.classList.toggle('active',c===1);},4000);})();}
    </script>
    <?php endif; ?>
    <nav class="navbar">
        <div class="container nav-container">
            <a href="/" class="logo">
                <i class="fa-brands fa-x-twitter"></i>
                <span>TwitExplorer</span>
            </a>
            <form action="/search" method="GET" class="search-bar">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" placeholder="<?= e(__('search_placeholder')) ?>" value="<?= e($_GET['q'] ?? '') ?>" required>
                <button type="submit"><?= e(__('search_button')) ?></button>
            </form>
            <div class="lang-selector">
                <form action="<?= e($_SERVER['REQUEST_URI']) ?>" method="GET">
                    <?php foreach ($_GET as $k => $v): if ($k !== 'lang'): ?>
                        <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
                    <?php endif; endforeach; ?>
                    <select name="lang" onchange="this.form.submit()">
                        <option value="tr" <?= $lang === 'tr' ? 'selected' : '' ?>>TR</option>
                        <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>>EN</option>
                        <option value="ar" <?= $lang === 'ar' ? 'selected' : '' ?>>AR</option>
                        <option value="zh" <?= $lang === 'zh' ? 'selected' : '' ?>>ZH</option>
                        <option value="ru" <?= $lang === 'ru' ? 'selected' : '' ?>>RU</option>
                        <option value="fa" <?= $lang === 'fa' ? 'selected' : '' ?>>FA</option>
                    </select>
                </form>
            </div>
            <div class="nav-links">
                <a href="/" class="<?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>"><i class="fa-solid fa-fire-flame-curved"></i> <?= e(__('home')) ?></a>
                <a href="/downloader" class="<?= ($currentPage ?? '') === 'downloader' ? 'active' : '' ?>"><i class="fa-solid fa-download"></i> <?= e(__('video_downloader')) ?></a>
                <a href="/widget" class="<?= ($currentPage ?? '') === 'widget' ? 'active' : '' ?>"><i class="fa-solid fa-cube"></i> Widget</a>
            </div>
            <button class="mobile-menu-btn" onclick="document.querySelector('.nav-links').classList.toggle('show')">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>
    <main class="container main-content">
