<?php
$allowedUserAgent = 'dana56 BOOSTING';

if ($_SERVER['HTTP_USER_AGENT'] !== $allowedUserAgent) {
    http_response_code(403);
    die('Access denied.');
}

if (!isset($_GET['type'])) {
    http_response_code(400);
    die('Type parameter is missing.');
}

$type = $_GET['type'];

$files = [
    'client' => 'https://boostanull.fun/api/local/files/Client.zip',
    'jar' => 'https://boostanull.fun/api/local/files/Client.zip',
];

if (!array_key_exists($type, $files)) {
    http_response_code(400);
    die('Invalid type parameter.');
}

$fileUrl = $files[$type];

header('Location: ' . $fileUrl);
exit;
?>