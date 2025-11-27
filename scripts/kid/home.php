<?php
// Fetch JSON from read.php and render total + rows table
include 'kid.php';

$json = false;
$host = $_SERVER['HTTP_HOST'] ?? null;
if ($host) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $url = $scheme . '://' . $host . '/'.$kid.'/read';
    $context = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 5]]);
    $json = @file_get_contents($url, false, $context);
}

// Fallback: include the file directly and capture output
if ($json === false) {
    ob_start();
    // ensure read.php treats this as a GET
    $_SERVER['REQUEST_METHOD'] = 'GET';
    include __DIR__ . '/'.$kid.'/read';
    $json = ob_get_clean();
}

$data = json_decode($json, true);
if (!is_array($data) || empty($data['success'])) {
    echo '<p>Error fetching data.</p>';
    if (is_array($data) && isset($data['error'])) {
        echo '<pre>' . htmlspecialchars($data['error']) . '</pre>';
    }
    return;
}

$rows = $data['rows'] ?? [];
$total = isset($data['total']) ? $data['total'] : 0;

?>

<h2> <?php echo strtoupper($kid); ?> </h2>
<div>
    <span class="header3">
        Total: <?php echo htmlspecialchars((string) $total, ENT_QUOTES, 'UTF-8'); ?> €
    </span>
    <span style="display: inline; vertical-align: sub;">
        <a href="/<?php echo ($kid);?>/add"><img src = "/plus-sign.svg" /></a>
    </span>
</div>

<?php if (empty($rows)): ?>
    <p>No rows to display.</p>
<?php else: ?>

    <div class="cards">
        <?php
        // determine columns from first row, exclude the 'id' and 'credit' columns
        $cols = array_keys((array) $rows[0]);
        $cols = array_values(array_filter($cols, function ($c) {
            return $c !== 'id' && $c !== 'credit';
        }));

        foreach ($rows as $r) {
            $credit = isset($r['credit']) ? (int) $r['credit'] : 0;
            $rowClass = $credit === 1 ? 'credit-1' : ($credit === -1 ? 'credit--1' : '');
            echo '<div class="' . $rowClass . '">';
            echo '<div class="read-card">';
            // optional heading: montant if present
            if (isset($r['montant'])) {
                $m = (string) $r['montant'];
                echo '<div class="header3">' . htmlspecialchars($m, ENT_QUOTES, 'UTF-8') . ' €</div>';
            }
            // timestamp meta
            if (isset($r['timestamp']) && is_numeric($r['timestamp'])) {
                echo '<div class="meta">' . htmlspecialchars(date('d/m/Y', (int) $r['timestamp']), ENT_QUOTES, 'UTF-8') . '</div>';
            }

            foreach ($cols as $c) {
                $val = isset($r[$c]) ? $r[$c] : '';
                // format timestamp label
                if ($c === 'timestamp' && is_numeric($val)) {
                    $val = date('d/m/Y', (int) $val);
                }
                if ($c === 'raison') {
                    //echo '<dt>' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '</dt>';
                    echo '<h4>' . htmlspecialchars((string) $val, ENT_QUOTES, 'UTF-8') . '</h4>';
                }
            }
            echo '</div>'; // read-card
            echo '</div>'; // row wrapper
        }
        ?>
    </div>
<?php endif; ?>
