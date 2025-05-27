<?php

Flight::register('subscriptionsService', 'SubscriptionsService');

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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::subscriptionsService()->calculateMonthlyPrice($id));
});

/**
 * @OA\Post(
 *     path="/subscriptions",
 *     summary="Create a new subscription",
 *     tags={"Subscriptions"},
 *     @OA\RequestBody(
 *         description="Subscription data to create",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Subscription")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="ID of the newly created subscription",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input data"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to create subscription"
 *     )
 * )
 */
//subscriptions - create
Flight::route('POST /subscriptions', function(){
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::subscriptionsService()->createSubscription($data));
});

/**
 * @OA\Put(
 *     path="/subscriptions/{id}",
 *     summary="Update an existing subscription",
 *     tags={"Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscription ID to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         description="Subscription data to update",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/Subscription")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Number of affected rows",
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
 *         description="Failed to update subscription"
 *     )
 * )
 */
//subscriptions - update
Flight::route('PUT /subscriptions/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::subscriptionsService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/subscriptions/{id}",
 *     summary="Delete a subscription",
 *     tags={"Subscriptions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Subscription ID to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Number of affected rows",
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
 *         description="Failed to delete subscription"
 *     )
 * )
 */
//subscriptions - delete
Flight::route('DELETE /subscriptions/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::subscriptionsService()->delete($id));
});