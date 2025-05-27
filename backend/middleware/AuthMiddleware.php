<?php

require_once 'rest/dao/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware {
   public function verifyToken($token){
       if (!$token)
           Flight::halt(401, "Missing authentication header");
       
       try {
           $decoded_token = JWT::decode($token, new Key(Database::JWT_SECRET(), 'HS256'));
       } catch (ExpiredException $e) {
           Flight::halt(401, "Token expired");
       } catch (SignatureInvalidException $e) {
           Flight::halt(401, "Invalid token signature");
       } catch (BeforeValidException $e) {
           Flight::halt(401, "Token not valid yet");
       } catch (Exception $e) {
           Flight::halt(401, "Invalid token");
       }
       
       Flight::set('user', $decoded_token->user);
       Flight::set('jwt_token', $token);
       return true;
   }

   public function authorizeRole($requiredRole) {
       $user = Flight::get('user');
       if ($user->role !== $requiredRole && $user->role !== ROLES::ADMIN) {
           Flight::halt(403, 'Access denied: insufficient privileges');
       }
   }

   public function authorizeRoles($roles) {
       $user = Flight::get('user');
       if (!in_array($user->role, $roles)) {
           Flight::halt(403, 'Forbidden: role not allowed');
       }
   }

   public function authorizePermission($permission) {
       $user = Flight::get('user');
       if (!in_array($permission, $user->permissions)) {
           Flight::halt(403, 'Access denied: permission missing');
       }
   }
}
