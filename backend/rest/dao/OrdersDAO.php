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

    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($data);
        return $this->connection->lastInsertId();
    }
}