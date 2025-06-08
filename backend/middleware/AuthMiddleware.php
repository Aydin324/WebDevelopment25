<?php

require_once __DIR__ . '/../rest/dao/config-first.php';
require_once __DIR__ . '/../data/roles.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\SignatureInvalidException;

class AuthMiddleware {
   public function verifyToken($token){
       error_log("Starting token verification");
       error_log("Received token: " . ($token ? substr($token, 0, 20) . '...' : 'null'));
       
       if (!$token) {
           error_log("No token provided");
           Flight::halt(401, "Missing authentication header");
       }
       
       try {
           error_log("Attempting to decode token with secret key: " . substr(Database::JWT_SECRET(), 0, 5) . '...');
           $decoded_token = JWT::decode($token, new Key(Database::JWT_SECRET(), 'HS256'));
           error_log("Token decoded successfully. Full token data: " . print_r($decoded_token, true));
           
           if (!isset($decoded_token->role)) {
               error_log("No role found in token");
               throw new Exception("Invalid token structure: no role found");
           }
           
           error_log("Token role: " . $decoded_token->role);
           error_log("Token user: " . print_r($decoded_token->user, true));
           
           Flight::set('user', $decoded_token->user);
           Flight::set('role', $decoded_token->role ? strtolower($decoded_token->role) : null);
           Flight::set('jwt_token', $token);
           
           error_log("Token verification complete. Set role: " . $decoded_token->role);
           error_log("Stored role in Flight: " . Flight::get('role'));
           return true;
       } catch (ExpiredException $e) {
           error_log("Token expired: " . $e->getMessage());
           Flight::halt(401, "Token expired");
       } catch (SignatureInvalidException $e) {
           error_log("Invalid token signature: " . $e->getMessage());
           Flight::halt(401, "Invalid token signature");
       } catch (BeforeValidException $e) {
           error_log("Token not valid yet: " . $e->getMessage());
           Flight::halt(401, "Token not valid yet");
       } catch (Exception $e) {
           error_log("Token validation failed: " . $e->getMessage());
           Flight::halt(401, "Invalid token");
       }
   }

   public function authorizeRole($requiredRole) {
       error_log("Starting role authorization check");
       $user = Flight::get('user');
       $role = Flight::get('role');
       
       error_log("Retrieved from Flight - User: " . print_r($user, true));
       error_log("Retrieved from Flight - Role: " . ($role ?? 'null'));
       
       if (!$role) {
           error_log("No role found in session");
           Flight::halt(403, 'Access denied: no role found');
       }
       
       $normalizedRole = strtolower($role);
       $normalizedRequiredRole = strtolower($requiredRole);
       
       error_log("Required role (normalized): " . $normalizedRequiredRole);
       error_log("User role (normalized): " . $normalizedRole);
       error_log("User data: " . print_r($user, true));
       
       // If we require USER role, both user and admin roles should be allowed
       if ($normalizedRequiredRole === strtolower(Roles::USER)) {
           error_log("Checking for USER role access");
           if ($normalizedRole !== strtolower(Roles::USER) && $normalizedRole !== strtolower(Roles::ADMIN)) {
               error_log("Access denied - User role required but got: " . $normalizedRole);
               error_log("Roles comparison - Required: '" . strtolower(Roles::USER) . "', Got: '" . $normalizedRole . "'");
               Flight::halt(403, 'Access denied: insufficient privileges');
           }
           error_log("USER role access granted");
       }
       // If we require ADMIN role, only admin should be allowed
       else if ($normalizedRequiredRole === strtolower(Roles::ADMIN)) {
           error_log("Checking for ADMIN role access");
           if ($normalizedRole !== strtolower(Roles::ADMIN)) {
               error_log("Access denied - Admin role required but got: " . $normalizedRole);
               Flight::halt(403, 'Access denied: insufficient privileges');
           }
           error_log("ADMIN role access granted");
       }
       error_log("Role authorization successful");
   }

   public function authorizeRoles($roles) {
       $role = Flight::get('role');
       if (!$role) {
           Flight::halt(403, 'Access denied: no role found');
       }
       
       $normalizedRole = strtolower($role);
       $normalizedRoles = array_map('strtolower', $roles);
       
       if (!in_array($normalizedRole, $normalizedRoles)) {
           Flight::halt(403, 'Forbidden: role not allowed');
       }
   }

   public function authorizePermission($permission) {
       $user = Flight::get('user');
       if (!$user || !isset($user['permissions']) || !in_array($permission, $user['permissions'])) {
           Flight::halt(403, 'Access denied: permission missing');
       }
   }
}
