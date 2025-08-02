<?php
header('Content-Type: application/json');

// Allow both GET and POST
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

// Normalize URL (strip params, ensure trailing slash)
$url = preg_replace('/\?.*/','',$url);
if (substr($url, -1) !== '/') $url .= '/';

// 1) Try JSON endpoint
$jsonUrl = $url . '?__a=1&__d=dis';
$ch = curl_init($jsonUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_USERAGENT => 'Mozilla/5.0'
]);
$jsonResponse = curl_exec($ch);
$httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = null;
if ($httpCode === 200 && $jsonResponse) {
    $data = json_decode($jsonResponse, true);
}

if ($data && isset($data['graphql']['shortcode_media'])) {
    $media = $data['graphql']['shortcode_media'];
} else {
    // 2) Fallback: scrape HTML for JSON
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0'
    ]);
    $html = curl_exec($ch);
    curl_close($ch);

    if (!$html) {
        echo json_encode(['success'=>false,'error'=>'Failed to fetch page.']);
        exit;
    }
    // Look for NextData first
    if (preg_match('/<script\s+id="__NEXT_DATA__"\s+type="application\/json">(.+?)<\/script>/s', $html, $m)) {
        $json = $m[1];
    } elseif (preg_match('/window\._sharedData = (.+?);<\/script>/', $html, $m)) {
        $json = $m[1];
    } else {
        echo json_encode(['success'=>false,'error'=>'Could not locate Instagram data in page.']);
        exit;
    }
    $all = json_decode($json, true);
    if (!$all) {
        echo json_encode(['success'=>false,'error'=>'Failed to parse fallback JSON.']);
        exit;
    }
    // Drill down
    if (isset($all['props']['pageProps']['graphql']['shortcode_media'])) {
        $media = $all['props']['pageProps']['graphql']['shortcode_media'];
    } elseif (isset($all['entry_data']['PostPage'][0]['graphql']['shortcode_media'])) {
        $media = $all['entry_data']['PostPage'][0]['graphql']['shortcode_media'];
    } else {
        echo json_encode(['success'=>false,'error'=>'Media object not found in fallback JSON.']);
        exit;
    }
}

// --- Now you have $media ---
// Title/caption
$caption = $media['edge_media_to_caption']['edges'][0]['node']['text'] 
         ?? ($media['accessibility_caption'] ?? '');

// Thumbnail
$thumbnail = $media['thumbnail_src'] 
           ?? ($media['display_url'] ?? '');

// Collect items
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

echo json_encode([
    'success'   => true,
    'title'     => $caption,
    'thumbnail' => $thumbnail,
    'items'     => $items
]);
