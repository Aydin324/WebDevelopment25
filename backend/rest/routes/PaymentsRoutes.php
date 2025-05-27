<?php

Flight::register('paymentsService', 'PaymentsService');

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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::paymentsService()->updateStatus($id, $data['status']));
});

/**
 * @OA\Get(
 *     path="/payments/user/{user_id}",
 *     summary="Get payments by user ID",
 *     tags={"Payments"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of user's payments",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Payment")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid user ID"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch payments"
 *     )
 * )
 */
//payments - get by user id
Flight::route('GET /payments/user/@user_id', function($user_id){
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::paymentsService()->getByUserId($user_id));
});

/**
 * @OA\Get(
 *     path="/payments/{id}",
 *     summary="Get payment by ID",
 *     tags={"Payments"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Payment ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment details",
 *         @OA\JsonContent(ref="#/components/schemas/Payment")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID format"
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
//payments - get by id
Flight::route('GET /payments/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::paymentsService()->getById($id));
});

/**
 * @OA\Put(
 *     path="/payments/{id}",
 *     summary="Update payment details",
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
 *             @OA\Property(property="amount", type="number", format="float", example=25.99),
 *             @OA\Property(property="payment_method", type="string", enum={"credit_card","cash"}, example="credit_card"),
 *             @OA\Property(property="status", type="string", enum={"pending","completed","failed"}, example="completed"),
 *             @OA\Property(property="transaction_id", type="string", example="txn_123456", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment updated successfully",
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
 *         description="Payment not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Update failed"
 *     )
 * )
 */
//payments - update
Flight::route('PUT /payments/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::paymentsService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/payments/{id}",
 *     summary="Delete a payment",
 *     tags={"Payments"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Payment ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment deleted successfully",
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
 *         description="Payment not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Deletion failed"
 *     )
 * )
 */
//payments - delete
Flight::route('DELETE /payments/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::paymentsService()->delete($id));
});