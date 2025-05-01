<?php
require_once 'BaseService.php';

class UserSubscriptionsService extends BaseService {
    private const VALID_STATUSES = ['active', 'expired', 'cancelled'];
    
    public function __construct() {
        parent::__construct('user_subscriptions');
    }

    //core methods    

    public function createUserSubscription(array $subscriptionData): int {
        $this->validateSubscriptionData($subscriptionData);
        
        // Set default status if not provided
        $subscriptionData['status'] = $subscriptionData['status'] ?? 'pending';
        
        try {
            return $this->dao->insert($subscriptionData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create user subscription: " . $e->getMessage());
        }
    }

    public function getByUserId(int $userId): array {
        $this->validateId($userId);
        
        try {
            return $this->dao->getByUserId($userId) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch user subscriptions");
        }
    }

    public function getActiveSubscriptions(int $userId): array {
        $this->validateId($userId);
        
        try {
            return $this->dao->getActiveSubscriptions($userId) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch active subscriptions");
        }
    }

    public function updateStatus(int $subscriptionId, string $newStatus): bool {
        $this->validateStatus($newStatus);
        
        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            return (bool) $this->dao->update($subscriptionId, $updateData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to update subscription status");
        }
    }

    //validation
    
    private function validateSubscriptionData(array $data): void {
        $requiredFields = ['user_id', 'subscription_id', 'start_date'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        // Validate IDs
        foreach (['user_id', 'subscription_id'] as $field) {
            if (!is_numeric($data[$field])) {
                throw new InvalidArgumentException("$field must be numeric");
            }
        }

        // Date validation
        if (isset($data['start_date']) && !strtotime($data['start_date'])) {
            throw new InvalidArgumentException("Invalid start date format");
        }

        if (isset($data['end_date']) && !strtotime($data['end_date'])) {
            throw new InvalidArgumentException("Invalid end date format");
        }

        // Status validation
        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }
    }

    private function validateStatus(string $status): void {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                "Invalid status. Valid values: " . implode(', ', self::VALID_STATUSES)
            );
        }
    }

    //business logic
        
    public function isSubscriptionActive(int $subscriptionId): bool {
        $subscription = $this->getById($subscriptionId);
        
        if (!$subscription) {
            throw new RuntimeException("Subscription not found");
        }

        return $subscription['status'] === 'active' && 
               (empty($subscription['end_date']) || strtotime($subscription['end_date']) > time());
    }

    public function cancelSubscription(int $subscriptionId): bool {
        return $this->updateStatus($subscriptionId, 'cancelled');
    }
}