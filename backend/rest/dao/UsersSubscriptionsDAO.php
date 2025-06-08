<?php
require_once 'BaseDAO.php';

class UserSubscriptionsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('orders');
    }

    public function getUserSubscriptionsWithDetails($user_id) {
        try {
            $query = "SELECT o.*, 
                            s.name, 
                            s.price, 
                            s.duration,
                            o.created_at as start_date,
                            DATE_ADD(o.created_at, INTERVAL s.duration MONTH) as next_billing_date
                     FROM orders o
                     LEFT JOIN subscriptions s ON o.subscription_id = s.subscription_id
                     WHERE o.user_id = :user_id 
                     AND o.order_type = 'subscription'
                     ORDER BY o.created_at DESC";
            
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
            $query = "SELECT o.*, 
                            s.name, 
                            s.price, 
                            s.duration,
                            o.created_at as start_date,
                            DATE_ADD(o.created_at, INTERVAL s.duration MONTH) as next_billing_date
                     FROM orders o
                     LEFT JOIN subscriptions s ON o.subscription_id = s.subscription_id
                     WHERE o.user_id = :user_id 
                     AND o.order_type = 'subscription'
                     AND o.status = 'active'
                     ORDER BY o.created_at DESC";
            
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
}