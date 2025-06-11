<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../../../vendor/autoload.php';

// Get the directory path for scanning
$rootPath = realpath(__DIR__ . '/../../../');

// Ensure the paths exist
if (!$rootPath) {
    die('Could not resolve root path');
}

// Scan for OpenAPI annotations
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',
    $rootPath . '/rest/routes'
]);

// Output JSON documentation
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Authentication');
echo $openapi->toJson();