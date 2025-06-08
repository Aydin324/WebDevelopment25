<?php
require_once __DIR__ . "/../config.php";
class Database {
    // private static $host = 'localhost';

    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                $dsn = "mysql:host=" . Config::DB_HOST() . ";port=" . Config::DB_PORT() . ";dbname=" . Config::DB_SCHEME();
                error_log("Attempting to connect to database with DSN: " . $dsn);
                
                self::$connection = new PDO(
                    $dsn,
                    Config::DB_USERNAME(),
                    Config::DB_PASSWORD(),
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_FOUND_ROWS => true,
                        PDO::ATTR_EMULATE_PREPARES => false // Ensure native prepared statements
                    ]
                );
                error_log("Database connection successful");
            } catch (PDOException $e) {
                error_log("Database connection failed: " . $e->getMessage());
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }

    public static function JWT_SECRET(){
        return Config::JWT_SECRET();
    }
}
?>