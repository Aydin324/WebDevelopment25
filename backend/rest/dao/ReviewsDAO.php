<?php
require_once 'BaseDAO.php';

class ReviewsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('reviews');
    }

    public function getByProductId($product_id) {
        $stmt = $this->connection->prepare("SELECT * FROM reviews WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByRating($rating) {
        $stmt = $this->connection->prepare("SELECT * FROM reviews WHERE rating = :rating");
        $stmt->bindParam(':rating', $rating);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($user_id) {
        $stmt = $this->connection->prepare("SELECT * FROM reviews WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}