<?php
$currentPage = '404';
$pageTitle = __('page_not_found') . ' - TwitExplorer';
$pageDescription = __('page_not_found_text');
$canonicalUrl = '/';
$noindex = true;

require __DIR__ . '/../includes/header.php';
?>

<div class="error-page">
    <i class="fa-solid fa-ghost"></i>
    <h2><?= e(__('page_not_found')) ?></h2>
    <p><?= e(__('page_not_found_text')) ?></p>
    <a href="/" class="btn btn-primary"><i class="fa-solid fa-house"></i> <?= e(__('back_home')) ?></a>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
