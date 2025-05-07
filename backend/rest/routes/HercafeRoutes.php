<?php 

Flight::register('usersService', 'UsersService');
Flight::register('ordersService', 'OrdersService');
Flight::register('paymentsService', 'PaymentsService');
Flight::register('productsService', 'ProductsService');
Flight::register('reviewsService', 'ReviewsService');
Flight::register('subscriptionsService', 'SubscriptionsService');
Flight::register('userSubscriptionsService', 'UserSubscriptionsService');

// ==== USERS ====

/**
 * @OA\Get(
 *     path="/users",
 *     summary="Get all users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//users - get all
Flight::route('GET /users', function(){
    Flight::json(Flight::usersService()->getAll());
});

/**
 * @OA\Get(
 *     path="/users/{id}",
 *     summary="Get a single user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User details",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID format"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//users - get single
Flight::route('GET /users/@id', function($id){
    Flight::json(Flight::usersService()->getById($id));
});

/**
 * @OA\Post(
 *     path="/users/register",
 *     summary="Register a new user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username","email","password"},
 *             @OA\Property(property="username", type="string", example="john_doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="securePassword123"),
 *             @OA\Property(property="role", type="string", example="user")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Username or email already exists"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Registration failed"
 *     )
 * )
 */
//users - register
Flight::route('POST /users/register', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->registerUser($data));
});

/**
 * @OA\Post(
 *     path="/users/login",
 *     summary="Authenticate user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="securePassword123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Authentication successful",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid credentials"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Authentication failed"
 *     )
 * )
 */
//users - login
Flight::route('POST /users/login', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->authenticate($data['email'], $data['password']));
});

/**
 * @OA\Put(
 *     path="/users/{id}",
 *     summary="Update user details",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="username", type="string", example="new_username"),
 *             @OA\Property(property="email", type="string", format="email", example="new@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="newPassword123"),
 *             @OA\Property(property="role", type="string", example="admin")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Update failed"
 *     )
 * )
 */
//users - update
Flight::route('PUT /users/@id', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->update($id, $data));
});

// ==== ORDERS ====

/**
 * @OA\Get(
 *     path="/orders",
 *     summary="Get all orders",
 *     tags={"Orders"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all orders",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Order")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//orders - get all
Flight::route('GET /orders', function(){
    Flight::json(Flight::ordersService()->getAll());
});

/**
 * @OA\Get(
 *     path="/orders/{id}",
 *     summary="Get order by ID",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order details",
 *         @OA\JsonContent(ref="#/components/schemas/Order")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID format"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//orders - get single
Flight::route('GET /orders/@id', function($id){
    Flight::json(Flight::ordersService()->getById($id));
});

/**
 * @OA\Get(
 *     path="/orders/status/{status}",
 *     summary="Get orders by status",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="status",
 *         in="path",
 *         required=true,
 *         description="Order status (pending/completed/cancelled)",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of orders with specified status",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Order")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid status value"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//orders - get by status
Flight::route('GET /orders/status/@status', function($status){
    Flight::json(Flight::ordersService()->getByStatus($status));
});

/**
 * @OA\Post(
 *     path="/orders",
 *     summary="Create a new order",
 *     tags={"Orders"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","order_type","total_price","status"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="order_type", type="string", enum={"subscription","product"}, example="product"),
 *             @OA\Property(property="total_price", type="number", format="float", example=19.99),
 *             @OA\Property(property="status", type="string", enum={"pending","completed","cancelled"}, example="pending"),
 *             @OA\Property(property="subscription_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Order creation failed"
 *     )
 * )
 */
//orders - create
Flight::route('POST /orders', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ordersService()->createOrder($data));
});

/**
 * @OA\Put(
 *     path="/orders/{id}/status",
 *     summary="Update order status",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"pending","completed","cancelled"}, example="completed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid status value"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Status update failed"
 *     )
 * )
 */
//orders - update status
Flight::route('PUT /orders/@id/status', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ordersService()->updateStatus($id, $data['status']));
});

// ==== PAYMENTS ====

/**
 * @OA\Get(
 *     path="/payments/order/{order_id}",
 *     summary="Get payment by order ID",
 *     tags={"Payments"},
 *     @OA\Parameter(
 *         name="order_id",
 *         in="path",
 *         required=true,
 *         description="Order ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment details",
 *         @OA\JsonContent(ref="#/components/schemas/Payment")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid order ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Payment not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//payments - get by order
Flight::route('GET /payments/order/@order_id', function($order_id){
    Flight::json(Flight::paymentsService()->getByOrder($order_id));
});

/**
 * @OA\Post(
 *     path="/payments",
 *     summary="Create a payment",
 *     tags={"Payments"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"order_id","user_id","amount","payment_method","status"},
 *             @OA\Property(property="order_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", format="float", example=19.99),
 *             @OA\Property(property="payment_method", type="string", enum={"credit_card","cash"}, example="credit_card"),
 *             @OA\Property(property="status", type="string", enum={"pending","completed","failed"}, example="pending")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Payment creation failed"
 *     )
 * )
 */
//payments - create
Flight::route('POST /payments', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::paymentsService()->createPayment($data));
});

/**
 * @OA\Put(
 *     path="/payments/{id}/status",
 *     summary="Update payment status",
 *     tags={"Payments"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Payment ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"pending","completed","failed"}, example="completed")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid status value"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Payment not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Status update failed"
 *     )
 * )
 */
//payments - update status
Flight::route('PUT /payments/@id/status', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::paymentsService()->updateStatus($id, $data['status']));
});

// ==== PRODUCTS ====

/**
 * @OA\Get(
 *     path="/products",
 *     summary="Get all products",
 *     tags={"Products"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all products",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//products - get all
Flight::route('GET /products', function(){
    Flight::json(Flight::productsService()->getAll());
});

/**
 * @OA\Get(
 *     path="/products/{id}",
 *     summary="Get product by ID",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product details",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID format"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//products - get single
Flight::route('GET /products/@id', function($id){
    Flight::json(Flight::productsService()->getById($id));
});

/**
 * @OA\Get(
 *     path="/products/search/{name}",
 *     summary="Search products by name",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="name",
 *         in="path",
 *         required=true,
 *         description="Product name or partial name",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of matching products",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Product")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Empty search term"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Search failed"
 *     )
 * )
 */
//products - search by name
Flight::route('GET /products/search/@name', function($name){
    Flight::json(Flight::productsService()->searchByName($name));
});

/**
 * @OA\Put(
 *     path="/products/{id}/stock",
 *     summary="Update product stock",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quantity"},
 *             @OA\Property(property="quantity", type="integer", description="Positive number to add, negative to subtract", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Stock updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid quantity"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="Insufficient stock"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Stock update failed"
 *     )
 * )
 */
//products - update stock
Flight::route('PUT /products/@id/stock', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::productsService()->updateStock($id, $data['quantity']));
});

// ==== REVIEWS ====

/**
 * @OA\Get(
 *     path="/reviews/product/{product_id}",
 *     summary="Get reviews by product ID",
 *     tags={"Reviews"},
 *     @OA\Parameter(
 *         name="product_id",
 *         in="path",
 *         required=true,
 *         description="Product ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of reviews for the product",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Review")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid product ID"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch reviews"
 *     )
 * )
 */
//reviews - get by product
Flight::route('GET /reviews/product/@product_id', function($product_id){
    Flight::json(Flight::reviewsService()->getByProductId($product_id));
});

/**
 * @OA\Get(
 *     path="/reviews/rating/{rating}",
 *     summary="Get reviews by rating",
 *     tags={"Reviews"},
 *     @OA\Parameter(
 *         name="rating",
 *         in="path",
 *         required=true,
 *         description="Rating value (1-5)",
 *         @OA\Schema(type="integer", minimum=1, maximum=5)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of reviews with specified rating",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Review")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid rating value"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch reviews"
 *     )
 * )
 */
//reviews - get by rating
Flight::route('GET /reviews/rating/@rating', function($rating){
    Flight::json(Flight::reviewsService()->getByRating($rating));
});

/**
 * @OA\Post(
 *     path="/reviews",
 *     summary="Create a review",
 *     tags={"Reviews"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","product_id","rating"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=5),
 *             @OA\Property(property="comment", type="string", example="Great product!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=409,
 *         description="User already reviewed this product"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Review creation failed"
 *     )
 * )
 */
//reviews - create
Flight::route('POST /reviews', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reviewsService()->createReview($data));
});

// ==== SUBSCRIPTIONS ====

/**
 * @OA\Get(
 *     path="/subscriptions",
 *     summary="Get all subscriptions",
 *     tags={"Subscriptions"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all subscriptions",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Subscription")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Database error"
 *     )
 * )
 */
//subscriptions - get all
Flight::route('GET /subscriptions', function(){
    Flight::json(Flight::subscriptionsService()->getAll());
});

/**
 * @OA\Get(
 *     path="/subscriptions/price/{min}/{max}",
 *     summary="Get subscriptions by price range",
 *     tags={"Subscriptions"},
 *     @OA\Parameter(
 *         name="min",
 *         in="path",
 *         required=true,
 *         description="Minimum price",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Parameter(
 *         name="max",
 *         in="path",
 *         required=true,
 *         description="Maximum price",
 *         @OA\Schema(type="number", format="float")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of subscriptions within price range",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Subscription")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid price range"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch subscriptions"
 *     )
 * )
 */
//subscriptions - get by price range
Flight::route('GET /subscriptions/price/@min/@max', function($min, $max){
    Flight::json(Flight::subscriptionsService()->getByPriceRange($min, $max));
});

/**
 * @OA\Get(
 *     path="/subscriptions/duration/{months}",
 *     summary="Get subscriptions by duration",
 *     tags={"Subscriptions"},
 *     @OA\Parameter(
 *         name="months",
 *         in="path",
 *         required=true,
 *         description="Duration in months (1-36)",
 *         @OA\Schema(type="integer", minimum=1, maximum=36)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of subscriptions with specified duration",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Subscription")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid duration"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch subscriptions"
 *     )
 * )
 */
//subscriptions - get by duration
Flight::route('GET /subscriptions/duration/@months', function($months){
    Flight::json(Flight::subscriptionsService()->getByDuration($months));
});

/**
 * @OA\Get(
 *     path="/subscriptions/{id}/monthly-price",
 *     summary="Calculate monthly price for a subscription",
 *     tags={"Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscription ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Monthly price calculation",
 *         @OA\JsonContent(
 *             @OA\Property(property="monthly_price", type="number", format="float", example=9.99)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid subscription ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscription not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Calculation failed"
 *     )
 * )
 */
//subscriptions - calculate monthly price
Flight::route('GET /subscriptions/@id/monthly-price', function($id){
    Flight::json(Flight::subscriptionsService()->calculateMonthlyPrice($id));
});

// ==== USER SUBSCRIPTION ====

/**
 * @OA\Get(
 *     path="/users/{user_id}/subscriptions",
 *     summary="Get user's subscriptions",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of user's subscriptions",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/UserSubscription")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user ID"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch subscriptions"
 *     )
 * )
 */
//user subscriptions - get by user
Flight::route('GET /users/@user_id/subscriptions', function($user_id){
    Flight::json(Flight::userSubscriptionsService()->getByUserId($user_id));
});

/**
 * @OA\Get(
 *     path="/users/{user_id}/subscriptions/active",
 *     summary="Get user's active subscriptions",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of user's active subscriptions",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/UserSubscription")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user ID"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch subscriptions"
 *     )
 * )
 */
//user subscriptions - get active
Flight::route('GET /users/@user_id/subscriptions/active', function($user_id){
    Flight::json(Flight::userSubscriptionsService()->getActiveSubscriptions($user_id));
});

/**
 * @OA\Post(
 *     path="/user-subscriptions",
 *     summary="Create a user subscription",
 *     tags={"User Subscriptions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","subscription_id","start_date"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="subscription_id", type="integer", example=1),
 *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="status", type="string", enum={"active","expired","cancelled"}, example="active")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Subscription creation failed"
 *     )
 * )
 */
//user subscriptions - create
Flight::route('POST /user-subscriptions', function(){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userSubscriptionsService()->createUserSubscription($data));
});

/**
 * @OA\Put(
 *     path="/user-subscriptions/{id}/status",
 *     summary="Update subscription status",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscription ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"active","expired","cancelled"}, example="active")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid status value"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscription not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Status update failed"
 *     )
 * )
 */
//user subscriptions - update status
Flight::route('PUT /user-subscriptions/@id/status', function($id){
    $data = Flight::request()->data->getData();
    Flight::json(Flight::userSubscriptionsService()->updateStatus($id, $data['status']));
});

/**
 * @OA\Get(
 *     path="/user-subscriptions/{id}/active",
 *     summary="Check if subscription is active",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscription ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription active status",
 *         @OA\JsonContent(
 *             @OA\Property(property="is_active", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid subscription ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscription not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Check failed"
 *     )
 * )
 */
//user subscriptions - check active
Flight::route('GET /user-subscriptions/@id/active', function($id){
    Flight::json(Flight::userSubscriptionsService()->isSubscriptionActive($id));
});

/**
 * @OA\Put(
 *     path="/user-subscriptions/{id}/cancel",
 *     summary="Cancel a subscription",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscription ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription cancelled successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid subscription ID"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Subscription not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Cancellation failed"
 *     )
 * )
 */
//user subscriptions - cancel
Flight::route('PUT /user-subscriptions/@id/cancel', function($id){
    Flight::json(Flight::userSubscriptionsService()->cancelSubscription($id));
});