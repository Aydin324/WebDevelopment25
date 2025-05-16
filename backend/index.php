<?php
require '../vendor/autoload.php'; // Load installed packages

require 'rest/routes/OrdersRoutes.php';
require 'rest/routes/ProductsRoutes.php';
require 'rest/routes/ReviewsRoutes.php';
require 'rest/routes/SubscriptionsRoutes.php';
require 'rest/routes/PaymentsRoutes.php';
require 'rest/routes/UsersSubscriptionsRoutes.php';
require 'rest/routes/UsersRoutes.php';

require 'rest/services/UsersService.php';
require 'rest/services/OrdersService.php';
require 'rest/services/PaymentsService.php';
require 'rest/services/ProductsService.php';
require 'rest/services/ReviewsService.php';
require 'rest/services/SubscriptionsService.php';
require 'rest/services/UsersSubscriptionsService.php';


Flight::route('/', function() {  // Define the homepage route
    echo 'Hello world!';
});

Flight::start();  // Start FlightPHP
?>