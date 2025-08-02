<?php
header('Content-Type: application/json');

// Allow both GET and POST
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST' && $method !== 'GET') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method not allowed']);
    exit;
}

// Read URL from GET or POST
if ($method === 'POST') {
    $url = trim($_POST['url'] ?? '');
} else {
    $url = trim($_GET['url'] ?? '');
}

if (!$url || !preg_match('#^(https?://)?(www\.)?instagram\.com/#i', $url)) {
    echo json_encode(['success'=>false,'error'=>'Please provide a valid Instagram URL.']);
    exit;
}
// Normalize URL
$url = preg_replace('/\?.*/','',$url);
if (substr($url,-1)!=='/') $url .= '/';

// Fetch page
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
]);
$html = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if (!$html || $code !== 200) {
    echo json_encode(['success'=>false,'error'=>'Failed to fetch Instagram page (HTTP '.$code.')']);
    exit;
}

// Extract embedded JSON
$json = '';
if (preg_match('/window\._sharedData = (.+?);<\/script>/', $html, $m)) {
    $json = $m[1];
} elseif (preg_match('/window\.__additionalDataLoaded\([^,]+,\s*(\{.+?\})\);<\/script>/', $html, $m)) {
    $json = $m[1];
}

// Fallback: JSON-LD (<script type="application/ld+json">)
if (!$json && preg_match('/<script type="application\/ld\+json">(.+?)<\/script>/s', $html, $m2)) {
    $json = $m2[1];
}

if (!$json) {
    echo json_encode(['success'=>false,'error'=>'Could not locate Instagram data in page.']);
    exit;
}

$data = json_decode($json, true);
if (!$data) {
    echo json_encode(['success'=>false,'error'=>'Failed to parse JSON data.']);
    exit;
}

// Drill down to the media object
$media = null;
if (isset($data['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
    $media = $data['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
} elseif (isset($data['entry_data']['ReelPage'][0]['graphql']['shortcode_media'])) {
    $media = $data['entry_data']['ReelPage'][0]['graphql']['shortcode_media'];
} elseif (isset($data['@type']) && $data['@type']==='VideoObject') {
    // JSON-LD may give a VideoObject
    $items = [['type'=>'Video','url'=>$data['contentUrl']]];
    $title = $data['caption'] ?? $data['description'] ?? '';
    $thumb = $data['thumbnailUrl'] ?? '';
    echo json_encode(['success'=>true,'title'=>$title,'thumbnail'=>$thumb,'items'=>$items]);
    exit;
}

if (!$media) {
    echo json_encode(['success'=>false,'error'=>'Media data not found in JSON.']);
    exit;
}

// Title (caption)
$caption = $media['edge_media_to_caption']['edges'][0]['node']['text'] 
         ?? $media['accessibility_caption'] ?? '';

// Thumbnail
$thumb = $media['thumbnail_src'] 
       ?? $media['display_url'] 
       ?? '';

// Gather items
$items = [];
if (!empty($media['edge_sidecar_to_children']['edges'])) {
    foreach ($media['edge_sidecar_to_children']['edges'] as $edge) {
        $n = $edge['node'];
        if (!empty($n['is_video'])) {
            $items[] = ['type'=>'Video','url'=>$n['video_url']];
        } else {
            $items[] = ['type'=>'Image','url'=>$n['display_url'] ?? $n['display_src']];
        }
    }
} else {
    if (!empty($media['is_video'])) {
        $items[] = ['type'=>'Video','url'=>$media['video_url']];
    } else {
        $items[] = ['type'=>'Image','url'=>$media['display_url'] ?? $media['display_src']];
    }
}

if (empty($items)) {
    echo json_encode(['success'=>false,'error'=>'No downloadable media found.']);
    exit;
}

// Success
echo json_encode([
    'success'=>true,
    'title'=>$caption,
    'thumbnail'=>$thumb,
    'items'=>$items
]);
