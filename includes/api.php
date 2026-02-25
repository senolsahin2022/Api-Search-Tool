<?php

require_once __DIR__ . '/lang.php';

function getBaseUrl() {
    $urls = [
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api',
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api'
    ];
    return $urls[array_rand($urls)];
}

define('API_AUTH_HEADER', 'X-Pentest-Auth: authorized-pentest-2026');

function apiRequest($endpoint, $params = []) {
    $url = getBaseUrl() . '/' . $endpoint;
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [API_AUTH_HEADER],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_FOLLOWLOCATION => true,
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || $response === false) {
        return null;
    }

    return json_decode($response, true);
}

function getUser($username) {
    return apiRequest('user', ['user' => $username]);
}

function searchPosts($query) {
    return apiRequest('search', ['q' => $query]);
}

function getHashtag($tag) {
    return apiRequest('hashtag', ['tag' => $tag]);
}

function getTweet($id) {
    $endpoints = [
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api/tweet?id=' . urlencode($id),
        'https://autumn-bush-ac99.senolsahin2022.workers.dev/api/status?id=' . urlencode($id),
    ];
    
    foreach ($endpoints as $url) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [API_AUTH_HEADER],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response !== false) {
            $data = json_decode($response, true);
            if (!empty($data) && !isset($data['error'])) {
                return $data;
            }
        }
    }
    
    return null;
}

function getTrends() {
    return apiRequest('trends');
}

function timeAgo($datetime) {
    if (empty($datetime)) return '';
    try {
        $time = new DateTime($datetime);
        $now = new DateTime();
        $diff = $now->diff($time);

        if ($diff->y > 0) return $diff->y . ' ' . __('year_ago');
        if ($diff->m > 0) return $diff->m . ' ' . __('month_ago');
        if ($diff->d > 0) return $diff->d . ' ' . __('day_ago');
        if ($diff->h > 0) return $diff->h . ' ' . __('hour_ago');
        if ($diff->i > 0) return $diff->i . ' ' . __('minute_ago');
        return __('just_now');
    } catch (Exception $e) {
        return $datetime;
    }
}

function formatNumber($num) {
    if (!is_numeric($num)) return $num;
    if ($num >= 1000000) return round($num / 1000000, 1) . 'M';
    if ($num >= 1000) return round($num / 1000, 1) . 'K';
    return number_format($num);
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function getDomain() {
    $domain = getenv('REPL_SLUG') . '.' . getenv('REPL_OWNER') . '.repl.co';
    if (empty(getenv('REPL_SLUG'))) {
        $domain = $_SERVER['HTTP_HOST'] ?? 'localhost:5000';
    }
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $domain;
}
