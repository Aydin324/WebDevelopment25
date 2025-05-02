<?php
require '../vendor/autoload.php'; // Load installed packages

Flight::route('/', function() {  // Define the homepage route
    echo 'Hello world!';
});

Flight::start();  // Start FlightPHP
?>