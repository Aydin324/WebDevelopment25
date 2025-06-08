<?php

require_once 'config-first.php';

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

    //helper functions
    public function query($query, $params)
    {
        try {
            error_log("BaseDAO executing query: " . $query);
            error_log("With parameters: " . print_r($params, true));
            
            $stmt = $this->connection->prepare($query);
            if (!$stmt) {
                $error = $this->connection->errorInfo();
                error_log("Prepare failed: " . print_r($error, true));
                throw new PDOException("Prepare failed: " . $error[2]);
            }
            
            $success = $stmt->execute($params);
            if (!$success) {
                $error = $stmt->errorInfo();
                error_log("Execute failed: " . print_r($error, true));
                throw new PDOException("Execute failed: " . $error[2]);
            }
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Query returned " . count($result) . " rows");
            if (!empty($result)) {
                error_log("First row columns: " . implode(", ", array_keys($result[0])));
                error_log("First row values: " . print_r($result[0], true));
            }
            return $result;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            if (isset($stmt)) {
                error_log("SQL state: " . print_r($stmt->errorInfo(), true));
            }
            throw new PDOException("Database query failed: " . $e->getMessage());
        }
    }

    public function query_unique($query, $params)
    {
        $results = $this->query($query, $params);
        return reset($results);
    }

    // ----------------------------------------------------

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

    public function getAllByParam($column_name, $value){
        $stmt = $this->connection->prepare("SELECT * FROM " . $this->table . " WHERE " . $column_name . "= :value");
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function updateStatus($id, $status) {
        $primaryKey = $this->getPrimaryKey();
        $sql = "UPDATE " . $this->table . " SET status = :status WHERE " . $primaryKey . " = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'status' => $status
        ]);
    }

    public function delete($id) {
        $primaryKey = $this->getPrimaryKey();
        $stmt = $this->connection->prepare("DELETE FROM " . $this->table . " WHERE " . $primaryKey . " = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}

?>