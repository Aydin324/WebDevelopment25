<?php
require_once 'BaseDAO.php';

class UsersSubscriptionsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('users_subscriptions');
    }

    public function getAll() {
        try {
            $query = "SELECT us.*, 
                            s.name, 
                            s.price, 
                            s.duration,
                            us.created_at as start_date,
                            DATE_ADD(us.created_at, INTERVAL s.duration MONTH) as next_billing_date,
                            u.username,
                            u.email
                     FROM users_subscriptions us
                     LEFT JOIN subscriptions s ON us.subscription_id = s.subscription_id
                     LEFT JOIN users u ON us.user_id = u.user_id
                     ORDER BY us.created_at DESC";
            
            return $this->query($query, []);
        } catch (PDOException $e) {
            error_log("Error in getAll: " . $e->getMessage());
            throw $e;
        }
    }

    public function getUserSubscriptionsWithDetails($user_id) {
        try {
            $query = "SELECT us.*, 
                            s.name, 
                            s.price, 
                            s.duration,
                            us.created_at as start_date,
                            DATE_ADD(us.created_at, INTERVAL s.duration MONTH) as next_billing_date,
                            u.username,
                            u.email
                     FROM users_subscriptions us
                     LEFT JOIN subscriptions s ON us.subscription_id = s.subscription_id
                     LEFT JOIN users u ON us.user_id = u.user_id
                     WHERE us.user_id = :user_id 
                     ORDER BY us.created_at DESC";
            
            error_log("Executing subscription query: " . $query);
            error_log("With user_id: " . $user_id);
            
            $result = $this->query($query, [':user_id' => $user_id]);
            error_log("Query result count: " . count($result));
            error_log("Query result: " . print_r($result, true));
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getUserSubscriptionsWithDetails: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("SQL state: " . $e->errorInfo[0]);
            throw $e;
        }
    }

    public function getActiveSubscriptionsWithDetails($user_id) {
        try {
            $query = "SELECT us.*, 
                            s.name, 
                            s.price, 
                            s.duration,
                            us.created_at as start_date,
                            DATE_ADD(us.created_at, INTERVAL s.duration MONTH) as next_billing_date,
                            u.username,
                            u.email
                     FROM users_subscriptions us
                     LEFT JOIN subscriptions s ON us.subscription_id = s.subscription_id
                     LEFT JOIN users u ON us.user_id = u.user_id
                     WHERE us.user_id = :user_id 
                     AND us.status = 'active'
                     ORDER BY us.created_at DESC";
            
            error_log("Executing active subscriptions query: " . $query);
            error_log("With user_id: " . $user_id);
            
            $result = $this->query($query, [':user_id' => $user_id]);
            error_log("Active subscriptions result count: " . count($result));
            error_log("Active subscriptions result: " . print_r($result, true));
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getActiveSubscriptionsWithDetails: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("SQL state: " . $e->errorInfo[0]);
            throw $e;
        }
    }

    public function updateStatus($subscription_id, $status) {
        $stmt = $this->connection->prepare("UPDATE users_subscriptions SET status = :status WHERE user_subscription_id = :id");
        return $stmt->execute([':status' => $status, ':id' => $subscription_id]);
    }

    public function insert($data) {
        try {
            error_log("Attempting to insert user subscription with data: " . print_r($data, true));
            
            $columns = implode(", ", array_keys($data));
            $placeholders = ":" . implode(", :", array_keys($data));
            $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($placeholders)";
            
            error_log("SQL Query: " . $sql);
            error_log("With parameters: " . print_r($data, true));
            
            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute($data);
            
            if ($result) {
                $id = $this->connection->lastInsertId();
                error_log("Successfully inserted user subscription with ID: " . $id);
                return $id;
            } else {
                error_log("Failed to insert user subscription. Error info: " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in insert: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
}