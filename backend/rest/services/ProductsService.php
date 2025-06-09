<?php
require_once 'BaseService.php';

class ProductsService extends BaseService {
    public function __construct() {
        parent::__construct('products');
    }

    public function getProductsByType($type) {
        try {
            $products = $this->dao->getAllByParam('type', $type);
            return ['data' => $products];
        } catch (Exception $e) {
            throw new Exception("Error fetching products by type: " . $e->getMessage());
        }
    }

    //core methods    
    public function createProduct(array $productData): int {
        $this->validateProductData($productData);
        
        try {
            return $this->dao->insert($productData);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to create product: " . $e->getMessage());
        }
    }

    public function updateStock(int $productId, int $quantityChange): bool {
        $this->validateId($productId);
        
        if (!is_numeric($quantityChange)) {
            throw new InvalidArgumentException("Quantity must be numeric");
        }

        try {
            $currentStock = $this->getCurrentStock($productId);
            $newStock = $currentStock + $quantityChange;
            
            if ($newStock < 0) {
                throw new RuntimeException("Insufficient stock available");
            }

            return $this->dao->update($productId, [
                'stock' => $newStock,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to update stock: " . $e->getMessage());
        }
    }

    public function searchByName(string $name): array {
        if (empty(trim($name))) {
            throw new InvalidArgumentException("Search term cannot be empty");
        }

        try {
            return $this->dao->searchByName($name) ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException("Product search failed: " . $e->getMessage());
        }
    }

    //validation    
    private function validateProductData(array $data): void {
        $requiredFields = ['name', 'price', 'stock'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("$field is required");
            }
        }

        if (!is_numeric($data['price']) || $data['price'] <= 0) {
            throw new InvalidArgumentException("Price must be a positive number");
        }

        if (!is_numeric($data['stock']) || $data['stock'] < 0) {
            throw new InvalidArgumentException("Stock must be a non-negative integer");
        }

        if (isset($data['name']) && strlen($data['name']) > 255) {
            throw new InvalidArgumentException("Product name too long (max 255 chars)");
        }
    }

    //helper methods    
    private function getCurrentStock(int $productId): int {
        try {
            $result = $this->dao->getStockById($productId);
            return (int) ($result['stock'] ?? 0);
        } catch (PDOException $e) {
            throw new RuntimeException("Failed to check current stock");
        }
    }
}