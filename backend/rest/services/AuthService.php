<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/AuthDao.php';
require_once __DIR__ . '/../dao/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class AuthService extends BaseService {
   private $auth_dao;
   public function __construct() {
       $this->auth_dao = new AuthDao();
       parent::__construct(new AuthDao);
   }


   public function get_user_by_email($email){
       return $this->auth_dao->get_user_by_email($email);
   }


   public function register($entity) {  
       if (empty($entity['email']) || empty($entity['password'])) {
           return ['success' => false, 'error' => 'Email and password are required.'];
       }


       $email_exists = $this->auth_dao->get_user_by_email($entity['email']);
       if($email_exists){
           return ['success' => false, 'error' => 'Email already registered.'];
       }


       $entity['password'] = password_hash($entity['password'], PASSWORD_BCRYPT);


       $entity = parent::add($entity);


       unset($entity['password']);


       return ['success' => true, 'data' => $entity];             
   }


   public function login($entity) {  
       if (empty($entity['email']) || empty($entity['password'])) {
           return ['success' => false, 'error' => 'Email and password are required.'];
       }


       $user = $this->auth_dao->get_user_by_email($entity['email']);
       if(!$user){
           return ['success' => false, 'error' => 'Invalid username or password.'];
       }


       if(!$user || !password_verify($entity['password'], $user['password_hash']))
           return ['success' => false, 'error' => 'Invalid username or password.'];


       unset($user['password']);
      
       $jwt_payload = [
            'user' => $user,
            'role' => $user['role'],       // Explicitly include role at top level ← IMPORTANT
            'iat' => time(),
            'exp' => time() + (60 * 15) // valid for 15 minutes
       ];


       $token = JWT::encode(
           $jwt_payload,
           Database::JWT_SECRET(),
           'HS256'
       );


       return ['success' => true, 'data' => array_merge($user, ['token' => $token])];             
   }
}
