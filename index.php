<?php

require_once __DIR__ . '/includes/api.php';

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Content-Type: text/html; charset=UTF-8');

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = rtrim($requestUri, '/') ?: '/';

if (preg_match('#^/assets/#', $requestUri)) {
    return false;
}

switch (true) {
    case $requestUri === '/':
        require __DIR__ . '/pages/home.php';
        break;
    case $requestUri === '/search':
        require __DIR__ . '/pages/search.php';
        break;
    case preg_match('#^/user/([a-zA-Z0-9_]+)$#', $requestUri, $m) === 1:
        $username = $m[1];
        require __DIR__ . '/pages/user.php';
        break;
    case preg_match('#^/hashtag/([a-zA-Z0-9_ğüşıöçĞÜŞİÖÇ]+)$#u', $requestUri, $m) === 1:
        $tag = $m[1];
        require __DIR__ . '/pages/hashtag.php';
        break;
    case $requestUri === '/twitexplorer.zip':
        $file = __DIR__ . '/twitexplorer.zip';
        if (file_exists($file)) {
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="twitexplorer.zip"');
            header('Content-Length: ' . filesize($file));
            readfile($file);
            exit;
        }
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
    case $requestUri === '/sitemap.xml':
        header('Content-Type: application/xml');
        require __DIR__ . '/sitemap.xml.php';
        break;
    case $requestUri === '/robots.txt':
        $file = __DIR__ . '/robots.txt';
        if (file_exists($file)) {
            header('Content-Type: text/plain');
            readfile($file);
            exit;
        }
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
}
