<?php

Flight::register('ordersService', 'OrdersService');

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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    error_log("Processing POST /orders request");
    error_log("Current role from Flight: " . (Flight::get('role') ?? 'null'));
    error_log("Current user from Flight: " . print_r(Flight::get('user'), true));
    
    try {
        Flight::auth_middleware()->authorizeRole(Roles::USER);
        $data = Flight::request()->data->getData();
        error_log("Order data received: " . print_r($data, true));
        $orderId = Flight::ordersService()->createOrder($data);
        Flight::json(['order_id' => $orderId]);
    } catch (Exception $e) {
        error_log("Error in POST /orders: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        Flight::halt(500, "Order creation failed: " . $e->getMessage());
    }
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ordersService()->updateStatus($id, $data['status']));
});

/**
 * @OA\Get(
 *     path="/orders/user/{user_id}",
 *     summary="Get orders by user ID",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of user's orders",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Order")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user ID"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch orders"
 *     )
 * )
 */
//orders - get by user id
Flight::route('GET /orders/user/@user_id', function($user_id){
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::ordersService()->getByUserId($user_id));
});

/**
 * @OA\Put(
 *     path="/orders/{id}",
 *     summary="Update order details",
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
 *             @OA\Property(property="order_type", type="string", enum={"subscription","product"}, example="product"),
 *             @OA\Property(property="total_price", type="number", format="float", example=25.99),
 *             @OA\Property(property="status", type="string", enum={"pending","completed","cancelled"}, example="completed"),
 *             @OA\Property(property="subscription_id", type="integer", example=1, nullable=true),
 *             @OA\Property(property="product_id", type="integer", example=1, nullable=true),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Order updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input data"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Order not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Update failed"
 *     )
 * )
 */
//orders - update
Flight::route('PUT /orders/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::ordersService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/orders/{id}",
 *     summary="Delete an order",
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
 *         description="Order deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
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
 *         description="Deletion failed"
 *     )
 * )
 */
//orders - delete
Flight::route('DELETE /orders/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::ordersService()->delete($id));
});

/**
 * @OA\Get(
 *     path="/orders/check-purchase",
 *     summary="Check if user has purchased a specific product",
 *     tags={"Orders"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="product_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Purchase check result",
 *         @OA\JsonContent(
 *             @OA\Property(property="has_purchased", type="boolean")
 *         )
 *     )
 * )
 */
Flight::route('GET /orders/check-purchase', function() {
    $user_id = Flight::request()->query->user_id;
    $product_id = Flight::request()->query->product_id;
    
    if (!$user_id || !$product_id) {
        Flight::json(['error' => 'Missing parameters'], 400);
        return;
    }

    try {
        $result = Flight::ordersService()->checkPurchaseHistory($user_id, $product_id);
        Flight::json(['has_purchased' => $result]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});