<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../dao/config-first.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthService extends BaseService {
   private $auth_dao;
   public function __construct() {
       $this->auth_dao = new AuthDao();
       parent::__construct('users');
   }


   public function get_user_by_email($email){
       return $this->auth_dao->get_user_by_email($email);
   }


   public function register($entity) {  
    if (empty($entity['email']) || empty($entity['password']) || empty($entity['username'])) {
        return ['success' => false, 'error' => 'Username, email and password are required.'];
    }

    $email_exists = $this->auth_dao->get_user_by_email($entity['email']);
    if ($email_exists) {
        return ['success' => false, 'error' => 'Email already registered.'];
    }

    $entity['password_hash'] = password_hash($entity['password'], PASSWORD_BCRYPT);
    unset($entity['password']);  

    $entity['role'] = Roles::USER;  // Use the constant from Roles class
    $entity['created_at'] = date('Y-m-d H:i:s');  
    $entity['updated_at'] = date('Y-m-d H:i:s');  

    error_log("Registering new user with role: " . $entity['role']);
    $result = parent::insert($entity);

    if (!$result) {
        return ['success' => false, 'error' => 'Database error creating record.'];
    }

    return ['success' => true, 'data' => $result];             
}



   public function login($entity) {  
       error_log("Login attempt for email: " . $entity['email']);
       
       if (empty($entity['email']) || empty($entity['password'])) {
           error_log("Login failed: Email or password missing");
           return ['success' => false, 'error' => 'Email and password are required.'];
       }

       $user = $this->auth_dao->get_user_by_email($entity['email']);
       if(!$user){
           error_log("Login failed: User not found");
           return ['success' => false, 'error' => 'Invalid username or password.'];
       }

       error_log("Found user: " . print_r($user, true));

       if(!$user || !password_verify($entity['password'], $user['password_hash'])) {
           error_log("Login failed: Password verification failed");
           return ['success' => false, 'error' => 'Invalid username or password.'];
       }

       unset($user['password']);
      
       $jwt_payload = [
            'user' => $user,
            'role' => $user['role'],       // Explicitly include role at top level
            'iat' => time(),
            'exp' => time() + (60 * 15) // valid for 15 minutes
       ];

       error_log("Creating JWT with payload: " . print_r($jwt_payload, true));

       $token = JWT::encode(
           $jwt_payload,
           Database::JWT_SECRET(),
           'HS256'
       );

       error_log("Login successful, token created");
       return ['success' => true, 'data' => array_merge($user, ['token' => $token])];             
   }
}
