<?php
require_once __DIR__ . '/../dao/BaseDAO.php';

class BaseService {
    protected $dao;
    protected $allowedFields = []; // Override in child classes
    protected $table;

    public function __construct($table) {
        $this->dao = new BaseDAO($table);
        $this->table = $table;
    }

    public function getAll() {
        try {
            $results = $this->dao->getAll();
            if($this->table=="users"){
                $results = array_map(function($user){   
                    unset($user['password_hash']);
                    return $user;
                }, $results);
            }
            return $results;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error fetching records");
        }
    }

    public function getById($id) {
        $this->validateId($id);
        try {
            $result = $this->dao->getById($id);
            if (!$result) {
                throw new RuntimeException("Record not found");
            }
            if($this->table == "users"){
                unset($result['password_hash']);
            }
            return $result;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error fetching record");
        }
    }

    public function insert($data) {
        $data = $this->preprocessData($data);
        $this->validateInsertData($data);
        
        try {
            return $this->dao->insert($data);
        } catch (PDOException $e) {
            throw new RuntimeException("Database error creating record");
        }
    }

    public function update($id, $data) {
        $this->validateId($id);
        $data = $this->preprocessData($data);
        $this->validateUpdateData($data);
        
        try {
            $affectedRows = $this->dao->update($id, $data);
            if ($affectedRows === 0) {
                throw new RuntimeException("No records updated - record may not exist");
            }
            return $affectedRows;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error updating record");
        }
    }

    public function delete($id) {
        $this->validateId($id);
        
        try {
            $affectedRows = $this->dao->delete($id);
            if ($affectedRows === 0) {
                throw new RuntimeException("No records deleted - record may not exist");
            }
            return $affectedRows;
        } catch (PDOException $e) {
            throw new RuntimeException("Database error deleting record");
        }
    }

    // helper methods

    protected function validateId($id) {
        if (empty($id) || !is_numeric($id)) {
            throw new InvalidArgumentException("Invalid ID format");
        }
    }

    protected function preprocessData(&$data) {
        if (!is_array($data) || empty($data)) {
            throw new InvalidArgumentException("No data provided");
        }

        if (!empty($this->allowedFields)) {
            $data = array_intersect_key($data, array_flip($this->allowedFields));
        }

        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }

    protected function validateInsertData($data) {
        // Base validation - override in child classes
    }

    protected function validateUpdateData($data) {
        // Base validation - override in child classes
    }
}