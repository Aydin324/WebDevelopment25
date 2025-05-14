<?php

require_once 'BaseService.php';

class OrdersService extends BaseService {
    private const VALID_STATUSES = ['pending', 'completed', 'cancelled'];
    private const VALID_ORDER_TYPES = ['subscription', 'product'];
    
    public function __construct() {
        parent::__construct('orders');
    }

    public function getByStatus(string $status): array {
        $this->validateStatus($status);
        
        try {
            return $this->dao->getByStatus($status) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch orders: " . $e->getMessage());
        }
    }

    public function updateStatus(int $orderId, string $newStatus): bool {
        $this->validateStatus($newStatus);
        
        try {
            $result = $this->dao->updateStatus($orderId, $newStatus);
            if ($result === false) {
                throw new RuntimeException("Order update failed or order doesn't exist");
            }
            return $result;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error updating order");
        }
    }

    public function getByUserId(int $userId): array {
        $this->validateId($userId);
        
        try {
            return $this->dao->getByUserId($userId) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch user orders: " . $e->getMessage());
        }
    }

    // Create new order (with full validation)
    public function createOrder(array $orderData): int {
        $this->validateOrderData($orderData);
        
        try {
            return $this->dao->insert($orderData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create order: " . $e->getMessage());
        }
    }

    //validation methods    
    private function validateOrderData(array $data): void {
        $requiredFields = [
            'user_id', 
            'order_type', 
            'total_price', 
            'status'
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                throw new InvalidArgumentException("$field is required");
            }
        }

        //numeric fields validation
        foreach (['user_id', 'quantity', 'total_price'] as $field) {
            if (isset($data[$field]) && !is_numeric($data[$field])) {
                throw new InvalidArgumentException("$field must be numeric");
            }
        }

        //nalidate against enum-like values
        if (isset($data['order_type'])) {
            $this->validateOrderType($data['order_type']);
        }
        
        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }

        //subscription-specific validation
        if ($data['order_type'] === 'subscription' && empty($data['subscription_id'])) {
            throw new InvalidArgumentException("subscription_id is required for subscription orders");
        }
    }

    private function validateStatus(string $status): void {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("Invalid status. Valid values: " . implode(', ', self::VALID_STATUSES));
        }
    }

    private function validateOrderType(string $type): void {
        if (!in_array($type, self::VALID_ORDER_TYPES, true)) {
            throw new InvalidArgumentException("Invalid order type. Valid values: " . implode(', ', self::VALID_ORDER_TYPES));
        }
    }
}