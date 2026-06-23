<?php
// Linking to your configuration file
require_once __DIR__ . '/../config/config.php';

class Database {
    // Hold the class instance (Singleton)
    private static $instance = null;
    private $conn;

    // Private constructor prevents creating multiple connections
    private function __construct() {
        // Details from image_1e7c71.png
        $host = DB_HOST; 
        $db   = DB_NAME;
        $user = DB_USER;
        $pass = DB_PASS;
        $port = '3306'; // Specified in image_1e7c71.png
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Shows SQL errors clearly
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Returns data as easy-to-use arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Better security for SQL injections
        ];

        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            // If the connection fails, this will tell you exactly why
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    // Static method to get the single instance of this class
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Method to get the actual connection object
    public function getConnection() {
        return $this->conn;
    }
}