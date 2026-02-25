<?php
header('Content-Type: application/xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8"?>';

$base = 'https://freedom-x.net';
$today = date('Y-m-d');
$langs = ['tr', 'en', 'ar', 'zh', 'ru', 'fa'];

$trends = getTrends();
$trendList = [];
if (!empty($trends)) {
    if (isset($trends['trends'])) $trendList = $trends['trends'];
    elseif (isset($trends['data'])) $trendList = $trends['data'];
    else $trendList = $trends;
}

$popularUsers = ['elonmusk', 'POTUS', 'CNN', 'BBCWorld', 'NBA', 'CoinDesk', 'OpenAI', 'SpaceX', 'Tesla', 'Google'];
$popularHashtags = ['bitcoin', 'crypto', 'ai', 'technology', 'breakingnews', 'btc', 'ethereum', 'chatgpt', 'sports', 'gaming'];
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
   <url>
      <loc><?= $base ?>/</loc>
<?php foreach ($langs as $l): ?>
      <xhtml:link rel="alternate" hreflang="<?= $l ?>" href="<?= $base ?>/?lang=<?= $l ?>" />
<?php endforeach; ?>
      <xhtml:link rel="alternate" hreflang="x-default" href="<?= $base ?>/" />
      <lastmod><?= $today ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>1.0</priority>
   </url>
   <url>
      <loc><?= $base ?>/downloader</loc>
      <lastmod><?= $today ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
   </url>
<?php foreach ($popularUsers as $user): ?>
   <url>
      <loc><?= $base ?>/user/<?= $user ?></loc>
<?php foreach ($langs as $l): ?>
      <xhtml:link rel="alternate" hreflang="<?= $l ?>" href="<?= $base ?>/user/<?= $user ?>?lang=<?= $l ?>" />
<?php endforeach; ?>
      <lastmod><?= $today ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>0.8</priority>
   </url>
<?php endforeach; ?>
<?php foreach ($popularHashtags as $tag): ?>
   <url>
      <loc><?= $base ?>/hashtag/<?= $tag ?></loc>
<?php foreach ($langs as $l): ?>
      <xhtml:link rel="alternate" hreflang="<?= $l ?>" href="<?= $base ?>/hashtag/<?= $tag ?>?lang=<?= $l ?>" />
<?php endforeach; ?>
      <lastmod><?= $today ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>0.7</priority>
   </url>
<?php endforeach; ?>
<?php
foreach ($trendList as $trend) {
    $name = is_string($trend) ? $trend : ($trend['name'] ?? $trend['query'] ?? $trend['topic'] ?? '');
    if (empty($name)) continue;
    $cleanName = ltrim($name, '#');
    $encodedName = urlencode($cleanName);
    if (in_array(strtolower($cleanName), array_map('strtolower', $popularHashtags))) continue;
?>
   <url>
      <loc><?= $base ?>/search?q=<?= $encodedName ?></loc>
      <lastmod><?= $today ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>0.6</priority>
   </url>
<?php } ?>
</urlset>
