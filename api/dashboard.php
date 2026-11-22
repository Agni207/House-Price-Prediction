<?php
/**
 * api/dashboard.php — Dashboard data + delete endpoint
 */
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
    exit;
}

$user   = currentUser();
$db     = getDB();
$action = $_REQUEST['action'] ?? 'stats';

/* ── Delete ─────────────────────────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id < 1) {
        echo json_encode(['success' => false, 'error' => 'Invalid ID.']);
        exit;
    }
    $stmt = $db->prepare('DELETE FROM predictions WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
    echo json_encode(['success' => $stmt->rowCount() > 0]);
    exit;
}

/* ── Get single prediction ──────────────────────────────────── */
if ($action === 'get_prediction') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM predictions WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user['id']]);
    $pred = $stmt->fetch();
    echo json_encode(['success' => (bool)$pred, 'prediction' => $pred ?: null]);
    exit;
}

/* ── Stats ──────────────────────────────────────────────────── */
$stats = $db->prepare('
    SELECT
        COUNT(*)                   AS total,
        AVG(predicted_price)       AS avg_price,
        MAX(predicted_price)       AS max_price,
        MIN(predicted_price)       AS min_price
    FROM predictions WHERE user_id = ?
');
$stats->execute([$user['id']]);
$summary = $stats->fetch();

/* ── Price distribution ─────────────────────────────────────── */
$distBuckets = [
    '< ₹25L'         => [0,           2500000],
    '₹25L – ₹50L'   => [2500000,     5000000],
    '₹50L – ₹1Cr'   => [5000000,    10000000],
    '₹1Cr – ₹2Cr'   => [10000000,   20000000],
    '₹2Cr – ₹5Cr'   => [20000000,   50000000],
    '> ₹5Cr'         => [50000000, PHP_INT_MAX],
];
$distLabels = [];
$distCounts = [];
foreach ($distBuckets as $label => [$lo, $hi]) {
    $q = $db->prepare('SELECT COUNT(*) FROM predictions WHERE user_id = ? AND predicted_price >= ? AND predicted_price < ?');
    $hiVal = ($hi === PHP_INT_MAX) ? 999999999999 : $hi;
    $q->execute([$user['id'], $lo, $hiVal]);
    $distLabels[] = $label;
    $distCounts[] = (int)$q->fetchColumn();
}

/* ── Location wise ──────────────────────────────────────────── */
$locQ = $db->prepare('
    SELECT location, ROUND(AVG(predicted_price)/100000,2) AS avg_lakh
    FROM predictions WHERE user_id = ?
    GROUP BY location ORDER BY avg_lakh DESC LIMIT 8
');
$locQ->execute([$user['id']]);
$locRows = $locQ->fetchAll();

/* ── Property type ──────────────────────────────────────────── */
$typeQ = $db->prepare('
    SELECT property_type, COUNT(*) AS cnt
    FROM predictions WHERE user_id = ?
    GROUP BY property_type
');
$typeQ->execute([$user['id']]);
$typeRows = $typeQ->fetchAll();

/* ── Recent ─────────────────────────────────────────────────── */
$recQ = $db->prepare('
    SELECT * FROM predictions WHERE user_id = ?
    ORDER BY created_at DESC LIMIT 5
');
$recQ->execute([$user['id']]);
$recent = $recQ->fetchAll();

echo json_encode([
    'success' => true,
    'summary' => [
        'total'     => (int)$summary['total'],
        'avg_price' => round((float)$summary['avg_price']),
        'max_price' => round((float)$summary['max_price']),
        'min_price' => round((float)$summary['min_price']),
    ],
    'dist'   => ['labels' => $distLabels, 'counts' => $distCounts],
    'loc'    => ['labels' => array_column($locRows, 'location'), 'prices' => array_column($locRows, 'avg_lakh')],
    'types'  => ['labels' => array_column($typeRows, 'property_type'), 'counts' => array_column($typeRows, 'cnt')],
    'recent' => $recent,
]);
