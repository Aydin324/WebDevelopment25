<?php

Flight::register('productsService', 'ProductsService');

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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
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
    Flight::auth_middleware()->authorizeRole(Roles::USER);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::productsService()->updateStock($id, $data['quantity']));
});

/**
 * @OA\Post(
 *     path="/products",
 *     summary="Create a new product",
 *     tags={"Products"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","price","stock"},
 *             @OA\Property(property="name", type="string", example="Premium Widget"),
 *             @OA\Property(property="price", type="number", format="float", example=19.99),
 *             @OA\Property(property="stock", type="integer", example=100),
 *             @OA\Property(property="description", type="string", example="High-quality widget for all your needs", nullable=true),
 *             @OA\Property(property="category_id", type="integer", example=1, nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product created successfully",
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
 *         description="Product creation failed"
 *     )
 * )
 */
//products - create product
Flight::route('POST /products', function(){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::productsService()->createProduct($data));
});

/**
 * @OA\Put(
 *     path="/products/{id}",
 *     summary="Update a product",
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
 *             @OA\Property(property="name", type="string", example="Updated Widget Name"),
 *             @OA\Property(property="price", type="number", format="float", example=24.99),
 *             @OA\Property(property="stock", type="integer", example=50),
 *             @OA\Property(property="description", type="string", example="Updated description", nullable=true),
 *             @OA\Property(property="category_id", type="integer", example=2, nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated successfully",
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
 *         description="Product not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Update failed"
 *     )
 * )
 */
//products - update
Flight::route('PUT /products/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::productsService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/products/{id}",
 *     summary="Delete a product",
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
 *         description="Product deleted successfully",
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
 *         description="Product not found"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Deletion failed"
 *     )
 * )
 */
//products - delete
Flight::route('DELETE /products/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::productsService()->delete($id));
});

/**
 * @OA\Get(
 *     path="/products/type/{type}",
 *     summary="Get products by type",
 *     tags={"Products"},
 *     @OA\Parameter(
 *         name="type",
 *         in="path",
 *         required=true,
 *         description="Product type",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of products of specified type",
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
//anyone can access this route
Flight::route('GET /products/type/@type', function($type){
    try {
        $products = Flight::productsService()->getProductsByType($type);
        Flight::json(['data' => $products]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});