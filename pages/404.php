<?php
$currentPage = '404';
$pageTitle = 'Sayfa Bulunamadı - TwitExplorer';
$pageDescription = 'Aradığınız sayfa bulunamadı.';
$canonicalUrl = '/';

require __DIR__ . '/../includes/header.php';
?>

<div class="error-page">
    <i class="fa-solid fa-ghost"></i>
    <h2>Sayfa Bulunamadı</h2>
    <p>Aradığınız sayfa mevcut değil veya taşınmış olabilir.</p>
    <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> Ana Sayfaya Dön</a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
