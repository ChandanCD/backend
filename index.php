<?php
// enable type restriction
declare(strict_types=1);

//instantiate SPL (Standard PHP Library) Autoloader
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}
// send json content type
header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if($parts[2] != "data"){
    http_response_code(404);
   exit;
}

$action = $parts[3] ?? "getdata"; // action allowed getdata, create , update , delete

$id = $parts[4] ?? null;

$controller = new DataController(__DIR__ . '/src/data.csv');

$controller->processRequest($_SERVER["REQUEST_METHOD"], $action, $id);












