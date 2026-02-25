<?php

require_once __DIR__ . '/includes/api.php';

session_start();
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'tr';
$_SESSION['lang'] = $lang;

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
    case $requestUri === '/widget':
        require __DIR__ . '/pages/widget.php';
        break;
    case $requestUri === '/widget/api':
        require __DIR__ . '/pages/widget-api.php';
        exit;
    case $requestUri === '/downloader':
        require __DIR__ . '/pages/downloader.php';
        break;
    case preg_match('#^/user/(.+)$#u', $requestUri, $m) === 1:
        $username = $m[1];
        require __DIR__ . '/pages/user.php';
        break;
    case preg_match('#^/hashtag/(.+)$#u', $requestUri, $m) === 1:
        $tag = $m[1];
        require __DIR__ . '/pages/hashtag.php';
        break;
    case preg_match('#^/status/([0-9]+)$#', $requestUri, $m) === 1:
        $tweetId = $m[1];
        require __DIR__ . '/pages/post.php';
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
