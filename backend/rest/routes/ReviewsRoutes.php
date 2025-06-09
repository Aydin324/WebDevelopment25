<?php

Flight::register('reviewsService', 'ReviewsService');

// ==== REVIEWS ====

/**
 * @OA\Get(
 *     path="/reviews",
 *     summary="Get all reviews",
 *     tags={"Reviews"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all reviews",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/Review")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch reviews"
 *     )
 * )
 */
//reviews - get all
Flight::route('GET /reviews', function(){
    try {
        $reviews = Flight::reviewsService()->getAll();
        Flight::json(['data' => $reviews]);
    } catch (Exception $e) {
        error_log("Error fetching reviews: " . $e->getMessage());
        Flight::json(['error' => 'Failed to fetch reviews'], 500);
    }
});

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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reviewsService()->createReview($data));
});

/**
 * @OA\Get(
 *     path="/reviews/{id}",
 *     summary="Get a single review by ID",
 *     tags={"Reviews"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review details",
 *         @OA\JsonContent(ref="#/components/schemas/Review")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID format"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch review"
 *     )
 * )
 */
//reviews - get single
Flight::route('GET /reviews/@id', function($id){
    Flight::json(Flight::reviewsService()->getById($id));
});

/**
 * @OA\Put(
 *     path="/reviews/{id}",
 *     summary="Update a review",
 *     tags={"Reviews"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5, example=4),
 *             @OA\Property(property="comment", type="string", example="Updated review text")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review updated successfully",
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
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Update failed"
 *     )
 * )
 */
//reviews - update
Flight::route('PUT /reviews/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::reviewsService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/reviews/{id}",
 *     summary="Delete a review",
 *     tags={"Reviews"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Review ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Review deleted successfully",
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
 *         description="Review not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Deletion failed"
 *     )
 * )
 */
//reviews - delete
Flight::route('DELETE /reviews/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::USER);
    Flight::json(Flight::reviewsService()->delete($id));
});