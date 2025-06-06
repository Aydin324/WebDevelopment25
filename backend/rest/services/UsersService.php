<?php
require_once 'BaseService.php';

class UsersService extends BaseService {
    protected $allowedFields = [
        'username', 
        'email', 
        'password_hash',  
        'role',         
        'created_at',    //auto
        'updated_at'     //auto
    ];
    
    public function __construct() {
        parent::__construct('users');
    }

    //passwords    
    private function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }

    //validation
    private function validateRegistrationData(array $data): void {
        $required = ['username', 'email', 'password'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format");
        }

        if (strlen($data['password']) < 8) {
            throw new InvalidArgumentException("Password must be at least 8 characters");
        }
    }

    //error handling
    private function handleDuplicateEntryError(PDOException $e): void {
        if (strpos($e->getMessage(), '1062') !== false) {
            if (strpos($e->getMessage(), 'username') !== false) {
                throw new RuntimeException("Username already taken");
            }
            if (strpos($e->getMessage(), 'email') !== false) {
                throw new RuntimeException("Email already registered");
            }
        }
    }

    //timestamp management
    public function updateLastLogin(int $userId): void {
        $this->dao->update($userId, [
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    //helper - get by email
    public function getByEmail($email){
        return $this->dao->getByParam('email', $email);
    }
}