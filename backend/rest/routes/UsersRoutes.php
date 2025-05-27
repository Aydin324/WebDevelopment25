<?php

Flight::register('usersService', 'UsersService');

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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::usersService()->getById($id));
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
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    $data = Flight::request()->data->getData();
    Flight::json(Flight::usersService()->update($id, $data));
});

/**
 * @OA\Delete(
 *     path="/users/{id}",
 *     summary="Delete a user account",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="affected_rows", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid ID format"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized (if you add auth)"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden (if implementing role checks)"
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
//users - delete
Flight::route('DELETE /users/@id', function($id){
    Flight::auth_middleware()->authorizeRole(Roles::ADMIN);
    Flight::json(Flight::usersService()->delete($id));
});