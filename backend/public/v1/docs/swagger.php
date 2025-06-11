<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../../../vendor/autoload.php';

// Define API base URL
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    define('BASE_URL', 'http://localhost/WebDevelopment25/backend');
} else {
    define('BASE_URL', 'https://web-full-stack-app-c2jkn.ondigitalocean.app/api');
}

// Get the directory path for scanning
$rootPath = realpath(__DIR__ . '/../../../');

// Scan for OpenAPI annotations
$openapi = \OpenApi\Generator::scan([
    $rootPath . '/public/v1/docs/doc_setup.php',
    $rootPath . '/rest/routes',
]);

// Output JSON documentation
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
echo $openapi->toJson();