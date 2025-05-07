<?php
/**
 * @OA\Info(
 *     title="Hercafe API",
 *     description="API for managing Hercafe",
 *     version="1.0",
 *     @OA\Contact(
 *         email="hercafe@gmail.com",
 *         name="Hercafe"
 *     )
 * )
 */

/**
 * @OA\Server(
 *     url="http://localhost/WebDevelopment25/backend",
 *     description="API server"
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */

/** ================== SCHEMA DEFINITIONS ================== **/

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "username", "email", "role"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="username", type="string", example="john_doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role", type="string", example="admin")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     required={"id", "user_id", "status", "total"},
 *     @OA\Property(property="id", type="integer", example=101),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="status", type="string", example="completed"),
 *     @OA\Property(property="total", type="number", format="float", example=49.99)
 * )
 */

/**
 * @OA\Schema(
 *     schema="Payment",
 *     type="object",
 *     required={"id", "order_id", "amount", "status"},
 *     @OA\Property(property="id", type="integer", example=501),
 *     @OA\Property(property="order_id", type="integer", example=101),
 *     @OA\Property(property="amount", type="number", format="float", example=49.99),
 *     @OA\Property(property="status", type="string", example="successful"),
 *     @OA\Property(property="method", type="string", example="Credit Card")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     required={"id", "name", "price"},
 *     @OA\Property(property="id", type="integer", example=301),
 *     @OA\Property(property="name", type="string", example="Latte"),
 *     @OA\Property(property="description", type="string", example="A creamy espresso-based drink."),
 *     @OA\Property(property="price", type="number", format="float", example=4.50),
 *     @OA\Property(property="category", type="string", example="Beverages")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Review",
 *     type="object",
 *     required={"id", "user_id", "product_id", "rating", "comment"},
 *     @OA\Property(property="id", type="integer", example=401),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="product_id", type="integer", example=301),
 *     @OA\Property(property="rating", type="integer", example=5),
 *     @OA\Property(property="comment", type="string", example="Amazing coffee!")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Subscription",
 *     type="object",
 *     required={"id", "name", "price", "duration"},
 *     @OA\Property(property="id", type="integer", example=601),
 *     @OA\Property(property="name", type="string", example="Premium Coffee Plan"),
 *     @OA\Property(property="price", type="number", format="float", example=19.99),
 *     @OA\Property(property="duration", type="string", example="1 month")
 * )
 */

/**
 * @OA\Schema(
 *     schema="UserSubscription",
 *     type="object",
 *     required={"id", "user_id", "subscription_id", "start_date", "end_date"},
 *     @OA\Property(property="id", type="integer", example=701),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="subscription_id", type="integer", example=601),
 *     @OA\Property(property="start_date", type="string", format="date", example="2025-04-01"),
 *     @OA\Property(property="end_date", type="string", format="date", example="2025-05-01")
 * )
 */