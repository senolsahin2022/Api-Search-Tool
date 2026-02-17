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
    default:
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
}
