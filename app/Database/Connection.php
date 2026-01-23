<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        try {
            $host = $_ENV['DB_HOST'] ?? 'localhost';
            $port = $_ENV['DB_PORT'] ?? 1433;
            $database = $_ENV['DB_DATABASE'] ?? 'itams';
            $username = $_ENV['DB_USERNAME'] ?? 'sa';
            $password = $_ENV['DB_PASSWORD'] ?? 'afh@1234';

            $dsn = "sqlsrv:Server=$host,$port;Database=$database;TrustServerCertificate=yes";
            
            $this->connection = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );

            // Test connection
            $this->connection->query("SELECT 1");
            
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage() . "\n" . 
                "DSN: sqlsrv: Server=$host,$port;Database=$database\n" .
                "Username: $username");
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self:: $instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
?>