<?php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Create a new mysqli connection
        $this->conn = new mysqli('localhost', 'root', '', 'attmgsystem');

        // Check connection
        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}

// Get the database connection
$db = Database::getInstance();
$conn = $db->getConnection();
?>