<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$term = $_GET['q'] ?? '';
if (strlen($term) < 3) {
    echo json_encode(['error' => 'Zu kurzer Suchbegriff', 'input_term' => $term]);
    exit;
}

// Token-Daten
$apiId = 'ID - ERZEUGT IN BOOKSTACK ';
$apiSecret = 'TOKEN - ERZEUGT IN BOOKTSTACK';
$bookstackUrl = 'BOOKSTACK URL';

// Buch-Whiltelist, die bei der Vorschlagsuche durchsucht werden sollen
$allowedBooks = ['it-support'];

function getPageExcerpt($pageId, $apiId, $apiSecret, $bookstackUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$bookstackUrl/api/pages/$pageId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Token $apiId:$apiSecret",
        "Accept: application/json"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);

    // Nutze html oder markdown, strippe Tags
    $html = $data['html'] ?? '';
    $text = strip_tags($html);
    return mb_substr(trim($text), 0, 150);
}


// BookStack-API aufrufen
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$bookstackUrl/api/search?query=" . urlencode($term));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token $apiId:$apiSecret",
    "Accept: application/json"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

$results = [];

foreach ($data['data'] ?? [] as $entry) {
    if (preg_match('#/books/([^/]+)/#', $entry['url'], $matches)) {
        $bookSlug = strtolower($matches[1]);

        if (in_array($bookSlug, $allowedBooks)) {
            // URL korrekt zusammensetzen
            $url = (strpos($entry['url'], 'http') === 0)
                ? $entry['url']
                : $bookstackUrl . $entry['url'];

            $excerpt = getPageExcerpt($entry['id'], $apiId, $apiSecret, $bookstackUrl);

            $results[] = [
                'title' => $entry['name'],
                'url' => $url,
                'excerpt' => $excerpt
            ];
        }
    }
}

echo json_encode($results);
