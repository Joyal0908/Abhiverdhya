<?php
// Database configuration for Abhiverdhya Admin Panel
// Auto-detect if we're accessing from localhost or network
function getDBHost() {
    $server_ip = $_SERVER['SERVER_ADDR'] ?? 'localhost';
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'localhost';
    
    // If client is accessing from same machine, use localhost
    if ($client_ip === '127.0.0.1' || $client_ip === '::1' || $client_ip === $server_ip) {
        return 'localhost';
    }
    // If accessing from network, use server IP
    return $server_ip;
}

define('DB_HOST', getDBHost());
define('DB_PORT', '3306');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'abhiverdhya_admin');

class Database {
    private $connection;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USERNAME,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function close() {
        $this->connection = null;
    }
}

// Initialize database connection
function getDBConnection() {
    $db = new Database();
    return $db->getConnection();
}
?>
