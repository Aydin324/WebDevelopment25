<?php
require '../vendor/autoload.php'; // Load installed packages

require 'rest/dao/config-first.php';
require "middleware/AuthMiddleware.php";

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

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Flight::register('auth_middleware', "AuthMiddleware");

Flight::route('/*', function() {
   if(
       strpos(Flight::request()->url, '/auth/login') === 0 ||
       strpos(Flight::request()->url, '/auth/register') === 0
   ) {
       return TRUE;
   } else {
       try {
           $token = Flight::request()->getHeader("Authentication");
           if(Flight::auth_middleware()->verifyToken($token))
               return TRUE;
       } catch (\Exception $e) {
           Flight::halt(401, $e->getMessage());
       }
   }
});


Flight::start();  // Start FlightPHP
?>