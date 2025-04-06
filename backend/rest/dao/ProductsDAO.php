<?php
require_once './BaseDAO.php';

class ProductsDao extends BaseDao {
    public function __construct() {
        parent::__construct("products");
    }

    public function searchByName($name) {
        $stmt = $this->connection->prepare("SELECT * FROM products WHERE name LIKE :name");
        $stmt->execute([':name' => "%$name%"]);
        return $stmt->fetchAll();
    }

    public function getStockById($id) {
        $stmt = $this->connection->prepare("SELECT stock FROM orders WHERE product_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>