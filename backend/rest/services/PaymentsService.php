<?php
require_once 'BaseService.php';

class PaymentsService extends BaseService {
    private const VALID_STATUSES = ['pending', 'completed', 'failed'];
    private const VALID_METHODS = ['credit_card', 'cash'];
    
    public function __construct() {
        parent::__construct('payments');
    }

    public function getByOrder(int $orderId): ?array {
        $this->validateId($orderId);
        
        try {
            return $this->dao->getByOrder($orderId) ?: null;
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to fetch payment: " . $e->getMessage());
        }
    }

    public function createPayment(array $paymentData): int {
        $this->validatePaymentData($paymentData);
        
        try {
            return $this->dao->insert($paymentData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create payment: " . $e->getMessage());
        }
    }

    public function updateStatus(int $paymentId, string $newStatus): bool {
        $this->validateStatus($newStatus);
        
        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            $result = $this->dao->update($paymentId, $updateData);
            if ($result === false) {
                throw new RuntimeException("Payment update failed or payment doesn't exist");
            }
            return $result;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error updating payment");
        }
    }

    //valdiation
    private function validatePaymentData(array $data): void {
        $requiredFields = [
            'order_id',
            'user_id',
            'amount',
            'payment_method',
            'status'
        ];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        // Numeric validation
        foreach (['order_id', 'user_id'] as $field) {
            if (!is_numeric($data[$field])) {
                throw new InvalidArgumentException("$field must be numeric");
            }
        }

        if (!is_numeric($data['amount']) || $data['amount'] <= 0) {
            throw new InvalidArgumentException("Amount must be a positive number");
        }

        // Validate enums
        if (isset($data['payment_method'])) {
            $this->validatePaymentMethod($data['payment_method']);
        }
        
        if (isset($data['status'])) {
            $this->validateStatus($data['status']);
        }
    }

    private function validateStatus(string $status): void {
        if (!in_array($status, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException(
                "Invalid payment status. Valid values: " . implode(', ', self::VALID_STATUSES)
            );
        }
    }

    private function validatePaymentMethod(string $method): void {
        if (!in_array($method, self::VALID_METHODS, true)) {
            throw new InvalidArgumentException(
                "Invalid payment method. Valid values: " . implode(', ', self::VALID_METHODS)
            );
        }
    }
}