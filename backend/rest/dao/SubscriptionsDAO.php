<?php
require_once 'BaseDAO.php';

class SubscriptionsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('subscriptions');
    }

    public function getByPriceRange($min, $max) {
        $stmt = $this->connection->prepare("SELECT * FROM subscriptions WHERE price BETWEEN :min AND :max");
        $stmt->bindParam(':min', $min);
        $stmt->bindParam(':max', $max);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDuration($duration) {
        $stmt = $this->connection->prepare("SELECT * FROM subscriptions WHERE duration = :duration");
        $stmt->bindParam(':duration', $duration);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}