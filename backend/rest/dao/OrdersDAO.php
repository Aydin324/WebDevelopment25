<?php
require_once 'BaseDAO.php';

class OrdersDAO extends BaseDAO {
    public function __construct() {
        parent::__construct('orders');
    }

    public function getByStatus($status) {
        $stmt = $this->connection->prepare("SELECT * FROM orders WHERE status = :status");
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus($order_id, $status) {
        $stmt = $this->connection->prepare("UPDATE orders SET status = :status WHERE order_id = :id");
        return $stmt->execute([':status' => $status, ':id' => $order_id]);
    }

    public function getByUserId($user_id) {
        try {
            // First, let's verify the connection
            if (!$this->connection) {
                error_log("Database connection is null!");
                throw new PDOException("No database connection");
            }

            $query = "SELECT o.*, 
                            CASE 
                                WHEN o.order_type = 'product' THEN p.name
                                WHEN o.order_type = 'subscription' THEN s.name
                            END as name,
                            CASE 
                                WHEN o.order_type = 'product' THEN p.price
                                WHEN o.order_type = 'subscription' THEN s.price
                            END as unit_price
                     FROM orders o
                     LEFT JOIN products p ON o.product_id = p.product_id
                     LEFT JOIN subscriptions s ON o.subscription_id = s.subscription_id
                     WHERE o.user_id = :user_id
                     ORDER BY o.created_at DESC";
            
            error_log("Executing order history query: " . $query);
            error_log("With user_id: " . $user_id);
            
            $result = $this->query($query, [':user_id' => $user_id]);
            
            // Log the first result for debugging
            if (!empty($result)) {
                error_log("First result item: " . print_r($result[0], true));
                error_log("Available columns: " . implode(", ", array_keys($result[0])));
            } else {
                error_log("No results found for user_id: " . $user_id);
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error in getByUserId: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("SQL state: " . $e->errorInfo[0] ?? 'N/A');
            throw $e;
        }
    }

    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
        return $this->connection->lastInsertId();
    }
}