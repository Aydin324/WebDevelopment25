<?php
require_once 'BaseDAO.php';

class ReviewsDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('reviews');
    }

    public function getAllWithDetails() {
        return $this->query(
            "SELECT r.*, 
                    u.username as user_name, 
                    p.name as product_name,
                    s.name as subscription_name,
                    COALESCE(p.name, s.name) as item_name,
                    CASE 
                        WHEN p.name IS NOT NULL THEN 'product'
                        WHEN s.name IS NOT NULL THEN 'subscription'
                        ELSE 'unknown'
                    END as review_type
             FROM reviews r 
             LEFT JOIN users u ON r.user_id = u.user_id 
             LEFT JOIN products p ON r.product_id = p.product_id 
             LEFT JOIN subscriptions s ON r.subscription_id = s.subscription_id
             ORDER BY r.created_at DESC",
            []
        );
    }

    public function getByProductId($product_id) {
        return $this->query(
            "SELECT r.*, u.username 
             FROM reviews r 
             LEFT JOIN users u ON r.user_id = u.user_id 
             WHERE r.product_id = :product_id
             ORDER BY r.created_at DESC",
            [':product_id' => $product_id]
        );
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