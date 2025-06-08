<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/ReviewsDAO.php';

class ReviewsService extends BaseService {
    private const MIN_RATING = 1;
    private const MAX_RATING = 5;
    
    public function __construct() {
        parent::__construct('reviews');
        $this->dao = new ReviewsDAO();
    }

    public function getAll() {
        try {
            return $this->dao->getAllWithDetails();
        } catch (PDOException $e) {
            throw new Exception("Error fetching reviews: " . $e->getMessage());
        }
    }

    //core methods    
    public function createReview(array $reviewData): int {
        $this->validateReviewData($reviewData);
        
        try {
            return $this->dao->insert($reviewData);
        } catch (PDOException $e) {
            $this->handleDuplicateReviewError($e);
            throw new RuntimeException("Failed to create review: " . $e->getMessage());
        }
    }

    public function getByProductId(int $productId): array {
        $this->validateId($productId);
        
        try {
            return $this->dao->getByProductId($productId) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch product reviews");
        }
    }

    public function getByRating(int $rating): array {
        $this->validateRating($rating);
        
        try {
            return $this->dao->getByRating($rating) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch reviews by rating");
        }
    }

    public function getByUserId(int $userId): array {
        $this->validateId($userId);
        
        try {
            return $this->dao->getByUserId($userId) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch user reviews");
        }
    }

    //validation    
    private function validateReviewData(array $data): void {
        $requiredFields = ['user_id', 'product_id', 'rating'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        // Numeric validation
        foreach (['user_id', 'product_id', 'subscription_id'] as $field) {
            if (isset($data[$field]) && !is_numeric($data[$field])) {
                throw new InvalidArgumentException("$field must be numeric");
            }
        }

        // Rating validation
        if (isset($data['rating'])) {
            $this->validateRating($data['rating']);
        }

        // Comment length check
        if (isset($data['comment']) && strlen($data['comment']) > 1000) {
            throw new InvalidArgumentException("Comment too long (max 1000 chars)");
        }
    }

    private function validateRating(int $rating): void {
        if ($rating < self::MIN_RATING || $rating > self::MAX_RATING) {
            throw new InvalidArgumentException(
                sprintf("Rating must be between %d and %d", self::MIN_RATING, self::MAX_RATING)
            );
        }
    }

    //error handling    
    private function handleDuplicateReviewError(PDOException $e): void {
        if (strpos($e->getMessage(), '1062') !== false) {
            if (strpos($e->getMessage(), 'user_id') !== false) {
                throw new RuntimeException("User already reviewed this product");
            }
        }
    }
}