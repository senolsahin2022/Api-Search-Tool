<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'TwitExplorer - Sosyal Medya Keşfet') ?></title>
    <meta name="description" content="<?= e($pageDescription ?? 'Sosyal medya trendlerini keşfet, kullanıcı profilleri ve hashtag araması yap.') ?>">
    <meta name="keywords" content="<?= e($pageKeywords ?? 'sosyal medya, twitter, trendler, hashtag, arama') ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <?php $baseUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'); ?>
    <link rel="canonical" href="<?= e($baseUrl . ($canonicalUrl ?? '')) ?>">
    <meta property="og:title" content="<?= e($pageTitle ?? 'TwitExplorer') ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? 'Sosyal medya trendlerini keşfet.') ?>">
    <meta property="og:url" content="<?= e($baseUrl . ($canonicalUrl ?? '')) ?>">
    <meta property="og:site_name" content="TwitExplorer">
    <meta property="og:image" content="<?= e($baseUrl) ?>/assets/images/og-image.png">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle ?? 'TwitExplorer') ?>">
    <meta name="twitter:description" content="<?= e($pageDescription ?? 'Sosyal medya trendlerini keşfet.') ?>">
    <meta name="twitter:image" content="<?= e($baseUrl) ?>/assets/images/og-image.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
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
            </div>
            <button class="mobile-menu-btn" onclick="document.querySelector('.nav-links').classList.toggle('show')">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>
    <main class="container main-content">
