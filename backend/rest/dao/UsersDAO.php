<?php 

require_once 'BaseDAO.php';

class UsersDAO extends BaseDAO{
    public function __construct(){
        parent::__construct('users');
    }

    //private helper method - may be useful later
    /*
    private function getByField(string $field, $value): ?array {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE $field = :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }
    */

    //get methods (getById already implemented in parent)
    public function getByEmail($email) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }
    public function getByUsername($username) {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch();
    }

    
}

?>