<?php
require_once 'BaseService.php';

class SubscriptionsService extends BaseService {
    public function __construct() {
        parent::__construct('subscriptions');
    }

    //core methods    
    public function createSubscription(array $subscriptionData): int {
        $this->validateSubscriptionData($subscriptionData);
        
        try {
            return $this->dao->insert($subscriptionData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create subscription: " . $e->getMessage());
        }
    }

    public function getByPriceRange(float $minPrice, float $maxPrice): array {
        $this->validatePriceRange($minPrice, $maxPrice);
        
        try {
            return $this->dao->getByPriceRange($minPrice, $maxPrice) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch subscriptions by price range");
        }
    }

    public function getByDuration(int $durationMonths): array {
        $this->validateDuration($durationMonths);
        
        try {
            return $this->dao->getByDuration($durationMonths) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch subscriptions by duration");
        }
    }

    //validation    
    private function validateSubscriptionData(array $data): void {
        $requiredFields = ['name', 'price', 'duration'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            throw new InvalidArgumentException("Price must be a positive number");
        }

        $this->validateDuration($data['duration']);

        if (isset($data['name']) && strlen($data['name']) > 100) {
            throw new InvalidArgumentException("Name too long (max 100 chars)");
        }
    }

    private function validatePriceRange(float $min, float $max): void {
        if ($min < 0 || $max < 0) {
            throw new InvalidArgumentException("Prices cannot be negative");
        }
        
        if ($min > $max) {
            throw new InvalidArgumentException("Minimum price cannot exceed maximum price");
        }
    }

    private function validateDuration(int $durationMonths): void {
        if ($durationMonths <= 0) {
            throw new InvalidArgumentException("Duration must be at least 1 month");
        }
        
        if ($durationMonths > 36) {
            throw new InvalidArgumentException("Duration cannot exceed 36 months");
        }
    }

    //business logic    
    public function calculateMonthlyPrice(int $subscriptionId): float {
        $subscription = $this->getById($subscriptionId);
        
        if (!$subscription) {
            throw new RuntimeException("Subscription not found");
        }

        return round($subscription['price'] / $subscription['duration'], 2);
    }
}