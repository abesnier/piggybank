<?php
include 'kid.php';
include $_SERVER['DOCUMENT_ROOT'] . '/scripts/password.php';

if (!isset($params)) {
	$params = [];
}
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Prefer POST data when present. Keep escaped versions for safe output
if ($method === 'POST') {
	$rawMontant = $_POST['montant'] ?? '';
	$rawCredit = $_POST['credit'] ?? '';
	$rawRaison = $_POST['raison'] ?? '';
	$rawMdp = $_POST['mdp'] ?? '';

	$montant = $rawMontant !== '' ? htmlspecialchars($rawMontant, ENT_QUOTES, 'UTF-8') : '';
	$credit = $rawCredit !== '' ? htmlspecialchars($rawCredit, ENT_QUOTES, 'UTF-8') : '';
	$raison = $rawRaison !== '' ? htmlspecialchars($rawRaison, ENT_QUOTES, 'UTF-8') : '';
	$mdp = $rawMdp !== '' ? htmlspecialchars($rawMdp, ENT_QUOTES, 'UTF-8') : '';
} else {
	$rawMontant = $params['montant'] ?? '';
	$rawCredit = $params['credit'] ?? '';
	$rawRaison = $params['raison'] ?? '';
	$rawMdp = $_POST['mdp'] ?? '';

	$montant = isset($params['montant']) ? htmlspecialchars($params['montant'], ENT_QUOTES, 'UTF-8') : '';
	$credit = isset($params['credit']) ? htmlspecialchars($params['credit'], ENT_QUOTES, 'UTF-8') : '';
	$raison = isset($params['raison']) ? htmlspecialchars($params['raison'], ENT_QUOTES, 'UTF-8') : '';
	$mdp = isset($params['mdp']) ? htmlspecialchars($params['mdp'], ENT_QUOTES, 'UTF-8') : '';
}

// If POST and montant provided, insert into SQLite database
if ($method === 'POST' && !empty($montant) && !empty($mdp)) {

	$dbFile = __DIR__ . '/../../db/piggy.sqlite';
	try {

		if (hash('sha256', $mdp) !== $password) {
			throw new Exception('Invalid password');
		}
		$pdo = new PDO('sqlite:' . $dbFile);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		// Prepare values for insertion using POST raw values
		$montantVal = is_numeric($rawMontant) ? $rawMontant : 0;
		$creditVal = isset($rawCredit) && ($rawCredit === '1' || $rawCredit === 1) ? 1 : -1;
		$raisonVal = $rawRaison ?? '';
		$tsVal = time(); // current Unix timestamp as integer

		$stmt = $pdo->prepare('INSERT INTO '.$table.' (montant, credit, raison, timestamp) VALUES (:montant, :credit, :raison, :timestamp)');
		$stmt->execute([
			':montant' => $montantVal,
			':credit' => $creditVal,
			':raison' => $raisonVal,
			':timestamp' => $tsVal,
		]);

		// Return JSON success response and exit (include timestamp)
		$response = [
			'success' => true,
			'message' => 'Inserted successfully',
			'data' => [
				'montant' => $montantVal + 0,
				'credit' => (int) $creditVal,
				'raison' => $raisonVal,
				'timestamp' => (int) $tsVal,
			],
		];
		header('Content-Type: application/json');
		echo json_encode($response);
		exit;

	} catch (Exception $e) {
		error_log('SQLite insert error: ' . $e->getMessage());
		// Return JSON error response and exit
		$err = [
			'success' => false,
			'message' => 'Database insert failed',
			'error' => $e->getMessage(),
		];
		header('Content-Type: application/json');
		http_response_code(500);
		echo json_encode($err);
		exit;
	}
} else {
	$err = [
		'success' => false,
		'method' => $method,
		'message' => 'Invalid request method or missing parameter',
	];
	header('Content-Type: application/json');
	http_response_code(500);
	echo json_encode($err);
	exit;
}
