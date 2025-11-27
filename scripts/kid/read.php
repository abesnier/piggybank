<?php
include 'kid.php';

// Return JSON of rows from alice table (only credit=1 rows) and total of montant for those rows
header('Content-Type: application/json');

// Only allow GET requests
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method',
        'method' => $method,
    ]);
    exit;
}

$dbFile = __DIR__ . '/../../db/piggy.sqlite';
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT * FROM '.$table.' ORDER BY timestamp DESC;');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filtered = [];
    $total = 0.0;

    foreach ($rows as $r) {
        $credit = isset($r['credit']) ? (int) $r['credit'] : -1;

        $montant = isset($r['montant']) ? (float) $r['montant'] : 0.0;
        $total += $montant * $credit;

        // normalize types for JSON output
        $r['montant'] = $montant + 0; // float
        $r['credit'] = $credit;
        if (isset($r['timestamp'])) {
            $r['timestamp'] = (int) $r['timestamp'];
        }
        $filtered[] = $r;

    }

    $response = [
        'success' => true,
        'rows' => $filtered,
        'total' => $total + 0,
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}


