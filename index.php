<?php

// Parse the request path (strip query string) and normalize trailing slash
//$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$request = rtrim($path, '/');
if ($request === '') {
  $request = '/';
}

$viewDir = '/scripts/';

// Collect parameters depending on method. For POST, merge GET and POST
// so both query string and body values are available. Views can also
// access the native $_GET and $_POST superglobals if needed.
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method === 'POST') {
  $params = array_merge($_GET, $_POST);
} else {
  $params = $_GET;
}

switch ($request) {
  case '':
  case '/':
    require __DIR__ . $viewDir . 'home.php';
    break;
/*
Routes for your kids will be added here when created with the create_kid.php script
Example:

  case '/alice':
    require __DIR__ . $viewDir . 'home.php';
    break;
  case '/alice/modify':
    require __DIR__ . $viewDir . 'alice/modify.php';
    break;
  case '/alice/read':
    require __DIR__ . $viewDir . 'alice/read.php';
    break;
  case '/alice/add':
    require __DIR__ . $viewDir . 'alice/add.php';
    break;
*/

default:
    http_response_code(404);
    require __DIR__ . $viewDir . '404.php';
}
?>
