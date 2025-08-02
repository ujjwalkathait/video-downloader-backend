<?php
header("Content-Type: application/json");

if (!isset($_GET['url'])) {
    echo json_encode(["success" => false, "error" => "URL not provided"]);
    exit;
}

$url = $_GET['url'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/119 Safari/537.36");

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["success" => false, "error" => "Curl error: " . curl_error($ch)]);
    curl_close($ch);
    exit;
}

curl_close($ch);

// Extract media JSON
preg_match('/"video_url":"([^"]+)"/', $response, $matches);
$videoUrl = isset($matches[1]) ? stripslashes($matches[1]) : null;

preg_match('/"display_url":"([^"]+)"/', $response, $thumbMatch);
$thumb = isset($thumbMatch[1]) ? stripslashes($thumbMatch[1]) : null;

preg_match('/"edge_media_to_caption":\{"edges":\[\{"node":\{"text":"(.*?)"/', $response, $captionMatch);
$title = isset($captionMatch[1]) ? html_entity_decode($captionMatch[1]) : "Instagram Video";

if ($videoUrl) {
    echo json_encode([
        "success" => true,
        "title" => $title,
        "thumbnail" => $thumb,
        "items" => [[
            "type" => "MP4 (Video)",
            "url" => $videoUrl
        ]]
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Could not locate Instagram data in page."]);
}
