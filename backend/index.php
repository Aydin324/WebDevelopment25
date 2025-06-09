<?php
require '../vendor/autoload.php'; // Load installed packages

// Enable error logging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Starting application...");

require 'rest/dao/config-first.php';
require "middleware/AuthMiddleware.php";
require "data/roles.php";

require 'rest/routes/OrdersRoutes.php';
require 'rest/routes/ProductsRoutes.php';
require 'rest/routes/ReviewsRoutes.php';
require 'rest/routes/SubscriptionsRoutes.php';
require 'rest/routes/PaymentsRoutes.php';
require 'rest/routes/UsersSubscriptionsRoutes.php';
require 'rest/routes/UsersRoutes.php';
require 'rest/routes/AuthRoutes.php';

require 'rest/services/UsersService.php';
require 'rest/services/OrdersService.php';
require 'rest/services/PaymentsService.php';
require 'rest/services/ProductsService.php';
require 'rest/services/ReviewsService.php';
require 'rest/services/SubscriptionsService.php';
require 'rest/services/UsersSubscriptionsService.php';
require 'rest/services/AuthService.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

error_log("All required files loaded successfully");

Flight::register('auth_middleware', "AuthMiddleware");

// Global authentication middleware
Flight::before('start', function(&$params, &$output) {
    $request = Flight::request();
    $currentPath = $request->url;
    error_log("Global middleware - Received request to: " . $currentPath);
    
    // Public routes that don't need authentication
    if(
        strpos($currentPath, '/auth/login') === 0 ||
        strpos($currentPath, '/auth/register') === 0 ||
        // Product routes - all GET requests are public
        (strpos($currentPath, '/products') === 0 && $request->method === 'GET') ||
        // Review routes - all GET requests are public
        (strpos($currentPath, '/reviews') === 0 && $request->method === 'GET') ||
        // Reviews by product - public
        (strpos($currentPath, '/reviews/product/') === 0 && $request->method === 'GET')
    ) {
        error_log("Accessing public route - no auth needed");
        return true;
    }
    
    try {
        $token = $request->getHeader("Authentication");
        error_log("Global middleware - Received token: " . ($token ? "yes" : "no"));
        
        if (!$token) {
            error_log("Global middleware - No token provided");
            Flight::halt(401, "Authentication required");
            return false;
        }
        
        // Verify token and set user/role in Flight
        $auth = Flight::auth_middleware();
        if($auth->verifyToken($token)) {
            error_log("Global middleware - Token verified successfully");
            error_log("Global middleware - Role set: " . Flight::get('role'));
            return true;
        }
        
        error_log("Global middleware - Token verification failed");
        Flight::halt(401, "Invalid authentication");
        return false;
    } catch (\Exception $e) {
        error_log("Global middleware - Authentication error: " . $e->getMessage());
        Flight::halt(401, $e->getMessage());
        return false;
    }
});

// Register error handler
Flight::map('error', function(Exception $ex){
    error_log("Error occurred: " . $ex->getMessage());
    error_log("Stack trace: " . $ex->getTraceAsString());
    
    $code = ($ex instanceof PDOException) ? 500 : 400;
    Flight::json([
        'status' => 'error',
        'message' => $ex->getMessage()
    ], $code);
});

error_log("Routes configured, starting Flight...");
Flight::start();  // Start FlightPHP

// Register services
Flight::register('usersService', 'UsersService');
Flight::register('ordersService', 'OrdersService');
Flight::register('paymentsService', 'PaymentsService');
Flight::register('productsService', 'ProductsService');
Flight::register('reviewsService', 'ReviewsService');
Flight::register('subscriptionsService', 'SubscriptionsService');
Flight::register('usersSubscriptionsService', 'UsersSubscriptionsService');
Flight::register('authService', 'AuthService');
?>