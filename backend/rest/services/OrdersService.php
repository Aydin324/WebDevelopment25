<?php

require_once 'BaseService.php';
require_once __DIR__ . '/../dao/OrdersDAO.php';

class OrdersService extends BaseService {
    private const VALID_STATUSES = ['pending', 'completed', 'cancelled'];
    private const VALID_ORDER_TYPES = ['subscription', 'product'];
    protected $dao;
    
    public function __construct() {
        $this->dao = new OrdersDAO();
        $this->table = 'orders';
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
        
        // Set default values if not provided
        if (!isset($orderData['status'])) {
            $orderData['status'] = 'pending';
        }

        // Ensure quantity is set and valid
        if (!isset($orderData['quantity']) || !is_numeric($orderData['quantity']) || $orderData['quantity'] < 1) {
            $orderData['quantity'] = 1; // Default to 1 if not set or invalid
        }

        // Handle product orders
        if ($orderData['order_type'] === 'product' && !isset($orderData['total_price'])) {
            try {
                $product = Flight::productsService()->getById($orderData['product_id']);
                if ($product) {
                    $orderData['total_price'] = $product['price'] * $orderData['quantity'];
                }
            } catch (PDOException $e) {
                throw new RuntimeException("Failed to get product price: " . $e->getMessage());
            }
        }

        // Handle subscription orders
        if ($orderData['order_type'] === 'subscription') {
            try {
                $subscription = Flight::subscriptionsService()->getById($orderData['subscription_id']);
                if ($subscription) {
                    // For trial subscriptions (IDs 2,4,6,8,10), price should be 0
                    $trialIds = [2,4,6,8,10];
                    $basePrice = in_array($orderData['subscription_id'], $trialIds) ? 0 : $subscription['price'];
                    $orderData['total_price'] = $basePrice * $orderData['quantity'];
                }
            } catch (PDOException $e) {
                throw new RuntimeException("Failed to get subscription price: " . $e->getMessage());
            }
        }
        
        try {
            return $this->dao->insert($orderData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create order: " . $e->getMessage());
        }
    }

    //validation methods    
    private function validateOrderData(array $data): void {
        $requiredFields = ['user_id', 'order_type', 'status'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        $this->validateOrderType($data['order_type']);
        $this->validateStatus($data['status']);

        // Product order validation
        if ($data['order_type'] === 'product') {
            if (empty($data['product_id'])) {
                throw new InvalidArgumentException("product_id is required for product orders");
            }
        }

        // Subscription order validation
        if ($data['order_type'] === 'subscription' && !isset($data['subscription_id'])) {
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

    public function checkPurchaseHistory($user_id, $product_id) {
        $query = "SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id AND product_id = :product_id AND status = 'completed'";
        $stmt = $this->dao->connection->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
}