<?php
require_once __DIR__ . '/../config-first.php';
require_once __DIR__ . '/../../../data/roles.php';

try {
    $connection = Database::connect();
    
    // Update all user roles to use the correct case
    $stmt = $connection->prepare("UPDATE users SET role = :new_role WHERE LOWER(role) = LOWER(:old_role)");
    
    // Update 'user' roles
    $stmt->execute([
        ':new_role' => Roles::USER,
        ':old_role' => 'user'
    ]);
    
    // Update 'admin' roles
    $stmt->execute([
        ':new_role' => Roles::ADMIN,
        ':old_role' => 'admin'
    ]);
    
    echo "Successfully updated user roles\n";
} catch (PDOException $e) {
    echo "Error updating roles: " . $e->getMessage() . "\n";
} 