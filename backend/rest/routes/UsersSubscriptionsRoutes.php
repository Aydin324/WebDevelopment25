<?php 

Flight::register('usersSubscriptionsService', 'UsersSubscriptionsService');

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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::usersSubscriptionsService()->getByUserId($user_id));
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::usersSubscriptionsService()->getActiveSubscriptions($user_id));
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersSubscriptionsService()->createUserSubscription($data));
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersSubscriptionsService()->updateStatus($id, $data['status']));
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::usersSubscriptionsService()->isSubscriptionActive($id));
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::usersSubscriptionsService()->cancelSubscription($id));
});

/**
 * @OA\Put(
 *     path="/user-subscriptions/{id}",
 *     summary="Update a user subscription",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User Subscription ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="subscription_id", type="integer", example=2),
 *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
 *             @OA\Property(property="end_date", type="string", format="date", example="2023-12-31"),
 *             @OA\Property(property="status", type="string", enum={"active","expired","cancelled"}, example="active")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription updated successfully",
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
 *         description="Subscription not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Update failed"
 *     )
 * )
 */
//user subscription - update
Flight::route('PUT /user-subscriptions/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersSubscriptionsService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/user-subscriptions/{id}",
 *     summary="Delete a user subscription",
 *     tags={"User Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User Subscription ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Subscription deleted successfully",
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
 *         description="Subscription not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Deletion failed"
 *     )
 * )
 */
//user subscription - delete
Flight::route('DELETE /user-subscriptions/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::usersSubscriptionsService()->delete($id));
});