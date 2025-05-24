<?php
require '../vendor/autoload.php'; // Load installed packages

require 'rest/dao/config.php';

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

Flight::route('/*', function() {
   if(
       strpos(Flight::request()->url, '/auth/login') === 0 ||
       strpos(Flight::request()->url, '/auth/register') === 0
   ) {
       return TRUE;
   } else {
       try {
           $token = Flight::request()->getHeader("Authentication");
           if(!$token)
               Flight::halt(401, "Missing authentication header");


           $decoded_token = JWT::decode($token, new Key(Database::JWT_SECRET(), 'HS256'));


           Flight::set('user', $decoded_token->user);
           Flight::set('jwt_token', $token);
           return TRUE;
       } catch (\Exception $e) {
           Flight::halt(401, $e->getMessage());
       }
   }
});

Flight::start();  // Start FlightPHP
?>