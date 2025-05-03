<?php 

Flight::register('usersService', 'UsersService');

//get all users
Flight::route('GET /users', function(){
    Flight::json(Flight::usersService()->getAll());
 });
 

?>