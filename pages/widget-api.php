<?php
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: public, max-age=60');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$type = $_GET['type'] ?? '';
$source = trim($_GET['source'] ?? '');
$count = min(max(intval($_GET['count'] ?? 5), 1), 20);

$response = ['error' => null];

switch ($type) {
    case 'user':
        if (empty($source)) {
            $response = ['error' => 'Username required'];
            break;
        }
        $data = getUser($source);
        if ($data) {
            $tweets = $data['tweets'] ?? $data['results'] ?? $data['data'] ?? [];
            if (empty($tweets) && isset($data[0])) $tweets = $data;
            $response = ['tweets' => array_slice($tweets, 0, $count)];
        } else {
            $response = ['error' => 'User not found'];
        }
        break;

    case 'hashtag':
        if (empty($source)) {
            $response = ['error' => 'Hashtag required'];
            break;
        }
        $data = getHashtag($source);
        if ($data) {
            $tweets = $data['tweets'] ?? $data['results'] ?? $data['data'] ?? [];
            if (empty($tweets) && isset($data[0])) $tweets = $data;
            $response = ['tweets' => array_slice($tweets, 0, $count)];
        } else {
            $response = ['error' => 'Hashtag not found'];
        }
        break;

    case 'search':
        if (empty($source)) {
            $response = ['error' => 'Search query required'];
            break;
        }
        $data = searchPosts($source);
        if ($data) {
            $tweets = $data['tweets'] ?? $data['results'] ?? $data['data'] ?? [];
            if (empty($tweets) && isset($data[0])) $tweets = $data;
            $response = ['tweets' => array_slice($tweets, 0, $count)];
        } else {
            $response = ['error' => 'No results'];
        }
        break;

    case 'trends':
        $data = getTrends();
        if ($data) {
            $trends = [];
            if (isset($data['trends'])) $trends = $data['trends'];
            elseif (isset($data['data'])) $trends = $data['data'];
            else $trends = $data;
            $response = ['trends' => array_slice($trends, 0, $count)];
        } else {
            $response = ['error' => 'Trends not available'];
        }
        break;

    default:
        $response = ['error' => 'Invalid widget type'];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
