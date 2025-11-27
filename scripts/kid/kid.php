<?php

$kid = "kid";

// Create the SQLite table for this kid if it doesn't exist.
// Sanitize the table name to allow only letters, numbers and underscores.
$table = preg_replace('/[^A-Za-z0-9_]/', '', $kid);
if ($table === '') {
    throw new RuntimeException('Invalid table name');
}

$dbFile = __DIR__ . '/../../db/piggy.sqlite';
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "CREATE TABLE IF NOT EXISTS \"$table\" (
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		montant REAL NOT NULL DEFAULT 0,
		credit INTEGER NOT NULL DEFAULT 0,
		raison TEXT,
		timestamp INTEGER
	);";

    $pdo->exec($sql);
    // optional: echo confirmation
    //echo "Table '$table' ensured in $dbFile\n";
} catch (Exception $e) {
    error_log('SQLite error: ' . $e->getMessage());
    throw $e;
}

