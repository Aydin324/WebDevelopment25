<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/UsersSubscriptionsDAO.php';

class UsersSubscriptionsService extends BaseService {
    private const VALID_STATUSES = ['active', 'pending', 'completed', 'cancelled'];
    protected $dao;
    
    public function __construct() {
        parent::__construct('orders');
        $this->dao = new UserSubscriptionsDAO();
    }

    //core methods    

    public function createUserSubscription(array $subscriptionData): int {
        $this->validateSubscriptionData($subscriptionData);
        
        // Set default status if not provided
        $subscriptionData['status'] = $subscriptionData['status'] ?? 'pending';
        $subscriptionData['order_type'] = 'subscription';
        
        try {
            return $this->dao->insert($subscriptionData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create user subscription: " . $e->getMessage());
        }
    }

    public function getByUserId(int $userId): array {
        $this->validateId($userId);
        
        try {
            return $this->dao->getUserSubscriptionsWithDetails($userId) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch user subscriptions");
        }
    }

    public function getActiveSubscriptions(int $userId): array {
        $this->validateId($userId);
        
        try {
            return $this->dao->getActiveSubscriptionsWithDetails($userId) ?: [];
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
        $requiredFields = ['user_id', 'subscription_id', 'quantity'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        // Validate IDs
        foreach (['user_id', 'subscription_id'] as $field) {
            if (!is_numeric($data[$field])) {
                throw new InvalidArgumentException("$field must be numeric");
            }
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

    protected function validateId($id): void {
        if (!is_numeric($id) || $id <= 0) {
            throw new InvalidArgumentException("Invalid ID format");
        }
    }

    //business logic
        
    public function isSubscriptionActive(int $subscriptionId): bool {
        $subscription = $this->dao->getById($subscriptionId);
        
        if (!$subscription) {
            throw new RuntimeException("Subscription not found");
        }

        return $subscription['status'] === 'active';
    }

    public function cancelSubscription(int $subscriptionId): bool {
        return $this->updateStatus($subscriptionId, 'cancelled');
    }
}