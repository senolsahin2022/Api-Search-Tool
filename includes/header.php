<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'TwitExplorer - Sosyal Medya Keşfet') ?></title>
    <meta name="description" content="<?= e($pageDescription ?? 'Sosyal medya trendlerini keşfet, kullanıcı profilleri ve hashtag araması yap.') ?>">
    <meta name="keywords" content="<?= e($pageKeywords ?? 'sosyal medya, twitter, trendler, hashtag, arama') ?>">
    <meta name="robots" content="index, follow">
    <?php $baseUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'); ?>
    <link rel="canonical" href="<?= e($baseUrl . ($canonicalUrl ?? '')) ?>">
    <meta property="og:title" content="<?= e($pageTitle ?? 'TwitExplorer') ?>">
    <meta property="og:description" content="<?= e($pageDescription ?? 'Sosyal medya trendlerini keşfet.') ?>">
    <meta property="og:type" content="website">
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
                <input type="text" name="q" placeholder="Kullanıcı, hashtag veya içerik ara..." value="<?= e($_GET['q'] ?? '') ?>" required>
                <button type="submit">Ara</button>
            </form>
            <div class="nav-links">
                <a href="/" class="<?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>"><i class="fa-solid fa-fire-flame-curved"></i> Trendler</a>
            </div>
            <button class="mobile-menu-btn" onclick="document.querySelector('.nav-links').classList.toggle('show')">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </nav>
    <main class="container main-content">
