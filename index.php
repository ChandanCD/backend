<?php
// enable type restriction
declare(strict_types=1);
require __DIR__ . "/vendor/autoload.php"; // load packages installed from packages

// define root path of application
define('APPROOT', __DIR__ . '/src/');

//instantiate SPL (Standard PHP Library) Autoloader
spl_autoload_register(function ($class) {
    require APPROOT . "Controllers/$class.php";
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

$parts = explode("/", $_SERVER["REQUEST_URI"]);

if($parts[2] != "data"){
    http_response_code(404);
   exit;
}

$action = $parts[3] ?? "getdata"; // action allowed getdata, create , update , delete

$id = $parts[4] ?? null;

$controller = new OrderController(__DIR__ . '/src/data.csv');

$controller->processRequest($_SERVER["REQUEST_METHOD"], $action, intval($id));












