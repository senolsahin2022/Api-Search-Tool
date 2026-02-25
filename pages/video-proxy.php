<?php
$url = $_GET['url'] ?? '';

if (empty($url)) {
    http_response_code(400);
    echo 'Missing url parameter';
    exit;
}

$allowed = ['video.twimg.com', 'pbs.twimg.com'];
$host = parse_url($url, PHP_URL_HOST);
if (!$host || !in_array($host, $allowed)) {
    http_response_code(403);
    echo 'Forbidden host';
    exit;
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Referer: https://x.com/',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept: */*',
        'Accept-Language: en-US,en;q=0.9',
        'Origin: https://x.com',
        'Sec-Fetch-Dest: video',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: cross-site',
    ],
    CURLOPT_HEADERFUNCTION => function($ch, $header) {
        $lower = strtolower(trim($header));
        if (strpos($lower, 'content-type:') === 0) {
            header($header, true);
        } elseif (strpos($lower, 'content-length:') === 0) {
            header($header, true);
        }
        return strlen($header);
    },
    CURLOPT_WRITEFUNCTION => function($ch, $data) {
        echo $data;
        flush();
        return strlen($data);
    },
]);

$filename = basename(parse_url($url, PHP_URL_PATH));
header('Content-Type: video/mp4');
header('Content-Disposition: attachment; filename="' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename) . '"');
header('Cache-Control: public, max-age=86400');
header('Access-Control-Allow-Origin: *');

curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (curl_errno($ch) || $httpCode >= 400) {
    if (!headers_sent()) {
        http_response_code($httpCode ?: 502);
    }
}

curl_close($ch);
exit;
