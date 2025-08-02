<?php
header('Content-Type: application/json');

// --- Allow GET or POST ---
$method = $_SERVER['REQUEST_METHOD'];
if ($method !== 'POST' && $method !== 'GET') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method not allowed']);
    exit;
}
$url = $method === 'POST'
     ? trim($_POST['url'] ?? '')
     : trim($_GET['url'] ?? '');

if (!$url || !preg_match('#^(https?://)?(www\.)?instagram\.com/#i', $url)) {
    echo json_encode(['success'=>false,'error'=>'Please provide a valid Instagram URL.']);
    exit;
}

// Normalize (strip query, ensure trailing slash)
$url = preg_replace('/\?.*/','',$url);
if (substr($url,-1)!=='/') $url .= '/';

// --- Fetch the page ---
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
    echo json_encode(['success'=>false,'error'=>"Failed to fetch page (HTTP $code)"]);
    exit;
}

// --- Extract embedded JSON ---
// 1) Next.js data
if (preg_match('/<script\s+id="__NEXT_DATA__"\s+type="application\/json">(.+?)<\/script>/s', $html, $m)) {
    $json = $m[1];

// 2) legacy window._sharedData
} elseif (preg_match('/window\._sharedData = (.+?);<\/script>/', $html, $m)) {
    $json = $m[1];

// 3) legacy __additionalDataLoaded
} elseif (preg_match('/window\.__additionalDataLoaded\([^,]+,\s*(\{.+?\})\);<\/script>/', $html, $m)) {
    $json = $m[1];

// 4) JSON-LD fallback
} elseif (preg_match('/<script type="application\/ld\+json">(.+?)<\/script>/s', $html, $m)) {
    $json = $m[1];

} else {
    echo json_encode(['success'=>false,'error'=>'Could not locate Instagram data in page.']);
    exit;
}

$data = json_decode($json, true);
if (!$data) {
    echo json_encode(['success'=>false,'error'=>'Failed to parse JSON.']);
    exit;
}

// --- Drill down to media object ---
// Try NextData path
if (isset($data['props']['pageProps']['graphql']['shortcode_media'])) {
    $media = $data['props']['pageProps']['graphql']['shortcode_media'];

// NextData sometimes nests differently:
} elseif (isset($data['props']['pageProps']['mediaData'])) {
    $media = $data['props']['pageProps']['mediaData'];

// Legacy PostPage/ReelPage:
} elseif (isset($data['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
    $media = $data['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
} elseif (isset($data['entry_data']['ReelPage'][0]['graphql']['shortcode_media'])) {
    $media = $data['entry_data']['ReelPage'][0]['graphql']['shortcode_media'];
} else {
    $media = null;
}

if (!$media) {
    echo json_encode(['success'=>false,'error'=>'Media object not found in JSON.']);
    exit;
}

// --- Gather title & thumbnail ---
$caption = '';
if (!empty($media['edge_media_to_caption']['edges'][0]['node']['text'])) {
    $caption = $media['edge_media_to_caption']['edges'][0]['node']['text'];
} elseif (!empty($media['accessibility_caption'])) {
    $caption = $media['accessibility_caption'];
}
$thumbnail = $media['thumbnail_src'] 
           ?? $media['display_url'] 
           ?? ($media['thumbnail_resources'][0]['src'] ?? '');

// --- Collect items ---
$items = [];
// Carousel
if (!empty($media['edge_sidecar_to_children']['edges'])) {
    foreach ($media['edge_sidecar_to_children']['edges'] as $edge) {
        $n = $edge['node'];
        if (!empty($n['is_video'])) {
            $items[] = ['type'=>'Video','url'=>$n['video_url']];
        } else {
            $items[] = ['type'=>'Image','url'=>$n['display_url'] ?? $n['display_src']];
        }
    }
// Single
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

echo json_encode([
    'success'   => true,
    'title'     => $caption,
    'thumbnail' => $thumbnail,
    'items'     => $items
]);
