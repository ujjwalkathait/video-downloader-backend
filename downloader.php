<?php
header('Content-Type: application/json');

if (!isset($_GET['url']) || empty($_GET['url'])) {
    echo json_encode(['success' => false, 'error' => 'No URL provided']);
    exit;
}

$url = $_GET['url'];
$escaped_url = escapeshellarg($url);

// Use latest yt-dlp with cookies for Instagram
$cmd = "yt-dlp --cookies cookies.txt -J $escaped_url";

// Execute
$output = shell_exec($cmd);
if (!$output) {
    echo json_encode(['success' => false, 'error' => 'No output from yt-dlp']);
    exit;
}

$data = json_decode($output, true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Invalid response from yt-dlp']);
    exit;
}

$response = [
    'success' => true,
    'title' => $data['title'] ?? '',
    'thumbnail' => $data['thumbnail'] ?? '',
    'items' => []
];

// For videos or multiple formats
if (isset($data['formats'])) {
    foreach ($data['formats'] as $format) {
        if (isset($format['url']) && strpos($format['format_note'] ?? '', 'audio') === false) {
            $response['items'][] = [
                'url' => $format['url'],
                'type' => $format['format_note'] ?? $format['ext'],
            ];
        }
    }
}

echo json_encode($response);
?>
