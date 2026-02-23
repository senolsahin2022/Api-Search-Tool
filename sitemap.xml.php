<?php header('Content-Type: application/xml; charset=UTF-8'); echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
   <url>
      <loc>https://freedom-x.net/</loc>
      <lastmod><?= date('Y-m-d') ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>1.0</priority>
   </url>
   <url>
      <loc>https://freedom-x.net/downloader</loc>
      <lastmod><?= date('Y-m-d') ?></lastmod>
      <changefreq>weekly</changefreq>
      <priority>0.8</priority>
   </url>
   <url>
      <loc>https://freedom-x.net/hashtag/bitcoin</loc>
      <lastmod><?= date('Y-m-d') ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>0.7</priority>
   </url>
   <url>
      <loc>https://freedom-x.net/hashtag/crypto</loc>
      <lastmod><?= date('Y-m-d') ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>0.7</priority>
   </url>
   <url>
      <loc>https://freedom-x.net/user/elonmusk</loc>
      <lastmod><?= date('Y-m-d') ?></lastmod>
      <changefreq>hourly</changefreq>
      <priority>0.7</priority>
   </url>
</urlset>
