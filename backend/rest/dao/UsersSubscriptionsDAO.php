<?php
require_once 'BaseDAO.php';

class UserSubscriptionsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('user_subscriptions');
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM user_subscriptions WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getActiveSubscriptions($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM user_subscriptions WHERE user_id = :user_id AND status = 'active'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}