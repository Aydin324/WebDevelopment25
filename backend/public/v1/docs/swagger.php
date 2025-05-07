<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../../../vendor/autoload.php'; // Ensure correct path to Composer autoload

// Define API base URL
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    define('BASE_URL', 'http://localhost/WebDevelopment25/backend');
} else {
    define('BASE_URL', 'https://your-production-server/backend/');
}

// Scan for OpenAPI annotations
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php', // Scan doc_setup.php
    __DIR__ . '/../../../rest/routes', // Scan all routes for annotations
]);

// Output JSON documentation
header('Content-Type: application/json');
echo $openapi->toJson();