<?php

require_once 'config.php';

class BaseDAO {
    protected $table;
    protected $connection;
    private $primaryKeys = [
        'orders' => 'order_id',
        'products' => 'product_id',
        'reviews' => 'review_id',
        'subscriptions' => 'subscription_id',
        'payments' => 'payment_id',
        'users_subscriptions' => 'user_subscription_id',
        'users' => 'user_id',
    ];

    public function __construct($table) {
        $this->table = $table;
        $this->connection = Database::connect();
    }

    private function getPrimaryKey() {
        //maps primary key name using column name
        return $this->primaryKeys[$this->table] ?? 'id';
    }

    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $primaryKey = $this->getPrimaryKey();
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table . " WHERE " . $primaryKey . " = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function getByParam($column_name, $value){
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table . " WHERE " . $column_name . "= :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function insert($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO " . $this->table . " ($columns) VALUES ($placeholders)";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "$key = :$key, ";
        }
        $fields = rtrim($fields, ", ");
        $primaryKey = $this->getPrimaryKey();
        $sql = "UPDATE " . $this->table . " SET $fields WHERE " . $primaryKey . " = :id";
        $stmt = $this->connection->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id) {
        $primaryKey = $this->getPrimaryKey();
        $stmt = $this->connection->prepare("DELETE FROM " . $this->table . " WHERE " . $primaryKey . " = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

?>