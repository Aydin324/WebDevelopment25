<?php
require_once 'BaseService.php';
require_once __DIR__ . '/../dao/UsersSubscriptionsDAO.php';

class UsersSubscriptionsService extends BaseService {
    private const VALID_STATUSES = ['active', 'pending', 'completed', 'cancelled'];
    protected $dao;
    
    public function __construct() {
        $this->dao = new UsersSubscriptionsDAO();
        $this->table = 'users_subscriptions';
    }

    public function getAll(): array {
        try {
            return $this->dao->getAll() ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch subscriptions: " . $e->getMessage());
        }
    }

    public function createUserSubscription($data) {
        error_log("UsersSubscriptionsService: Creating user subscription with data: " . print_r($data, true));
        
        try {
            // Validate required fields
            if (!isset($data['user_id']) || !isset($data['subscription_id'])) {
                error_log("UsersSubscriptionsService: Missing required fields");
                throw new InvalidArgumentException("user_id and subscription_id are required");
            }

            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 'active';
            }

            // Set default quantity if not provided
            if (!isset($data['quantity'])) {
                $data['quantity'] = 1;
            }

            // Set created_at if not provided
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            error_log("UsersSubscriptionsService: Attempting to insert with validated data");
            $result = $this->dao->insert($data);
            error_log("UsersSubscriptionsService: Insert result: " . ($result ? "success with ID: $result" : "failed"));
            
            return $result;
        } catch (Exception $e) {
            error_log("UsersSubscriptionsService Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
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
        
        try {
            return $this->dao->updateStatus($subscriptionId, $newStatus);
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