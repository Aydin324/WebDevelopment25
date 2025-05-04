<?php 

Flight::register('usersService', 'UsersService');
Flight::register('ordersService', 'OrdersService');
Flight::register('paymentsService', 'PaymentsService');
Flight::register('productsService', 'ProductsService');
Flight::register('reviewsService', 'ReviewsService');
Flight::register('subscriptionsService', 'SubscriptionsService');
Flight::register('userSubscriptionsService', 'UserSubscriptionsService');

// ==== USERS ====

//users - get all
Flight::route('GET /users', function(){
    Flight::json(Flight::usersService()->getAll());
});

//users - get single
Flight::route('GET /users/@id', function($id){
    Flight::json(Flight::usersService()->getById($id));
});

//users - register
Flight::route('POST /users/register', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->registerUser($data));
});

//users - login
Flight::route('POST /users/login', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->authenticate($data['email'], $data['password']));
});

//users - update
Flight::route('PUT /users/@id', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->update($id, $data));
});

// ==== ORDERS ====

//orders - get all
Flight::route('GET /orders', function(){
    Flight::json(Flight::ordersService()->getAll());
});

//orders - get single
Flight::route('GET /orders/@id', function($id){
    Flight::json(Flight::ordersService()->getById($id));
});

//orders - get by status
Flight::route('GET /orders/status/@status', function($status){
    Flight::json(Flight::ordersService()->getByStatus($status));
});

//orders - create
Flight::route('POST /orders', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ordersService()->createOrder($data));
});

//orders - update status
Flight::route('PUT /orders/@id/status', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ordersService()->updateStatus($id, $data['status']));
});

// ==== PAYMENTS ====

//payments - get by order
Flight::route('GET /payments/order/@order_id', function($order_id){
    Flight::json(Flight::paymentsService()->getByOrder($order_id));
});

//payments - create
Flight::route('POST /payments', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::paymentsService()->createPayment($data));
});

//payments - update status
Flight::route('PUT /payments/@id/status', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::paymentsService()->updateStatus($id, $data['status']));
});

// ==== PRODUCTS ====

//products - get all
Flight::route('GET /products', function(){
    Flight::json(Flight::productsService()->getAll());
});

//products - get single
Flight::route('GET /products/@id', function($id){
    Flight::json(Flight::productsService()->getById($id));
});

//products - search by name
Flight::route('GET /products/search/@name', function($name){
    Flight::json(Flight::productsService()->searchByName($name));
});

//products - update stock
Flight::route('PUT /products/@id/stock', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::productsService()->updateStock($id, $data['quantity']));
});

// ==== REVIEWS ====

//reviews - get by product
Flight::route('GET /reviews/product/@product_id', function($product_id){
    Flight::json(Flight::reviewsService()->getByProductId($product_id));
});

//reviews - get by rating
Flight::route('GET /reviews/rating/@rating', function($rating){
    Flight::json(Flight::reviewsService()->getByRating($rating));
});

//reviews - create
Flight::route('POST /reviews', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reviewsService()->createReview($data));
});

// ==== SUBSCRIPTIONS ====

//subscriptions - get all
Flight::route('GET /subscriptions', function(){
    Flight::json(Flight::subscriptionsService()->getAll());
});

//subscriptions - get by price range
Flight::route('GET /subscriptions/price/@min/@max', function($min, $max){
    Flight::json(Flight::subscriptionsService()->getByPriceRange($min, $max));
});

//subscriptions - get by duration
Flight::route('GET /subscriptions/duration/@months', function($months){
    Flight::json(Flight::subscriptionsService()->getByDuration($months));
});

//subscriptions - calculate monthly price
Flight::route('GET /subscriptions/@id/monthly-price', function($id){
    Flight::json(Flight::subscriptionsService()->calculateMonthlyPrice($id));
});

// ==== USER SUBSCRIPTION ====

//user subscriptions - get by user
Flight::route('GET /users/@user_id/subscriptions', function($user_id){
    Flight::json(Flight::userSubscriptionsService()->getByUserId($user_id));
});

//user subscriptions - get active
Flight::route('GET /users/@user_id/subscriptions/active', function($user_id){
    Flight::json(Flight::userSubscriptionsService()->getActiveSubscriptions($user_id));
});

//user subscriptions - create
Flight::route('POST /user-subscriptions', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userSubscriptionsService()->createUserSubscription($data));
});

//user subscriptions - update status
Flight::route('PUT /user-subscriptions/@id/status', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userSubscriptionsService()->updateStatus($id, $data['status']));
});

//user subscriptions - check active
Flight::route('GET /user-subscriptions/@id/active', function($id){
    Flight::json(Flight::userSubscriptionsService()->isSubscriptionActive($id));
});

//user subscriptions - cancel
Flight::route('PUT /user-subscriptions/@id/cancel', function($id){
    Flight::json(Flight::userSubscriptionsService()->cancelSubscription($id));
});