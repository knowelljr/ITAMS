<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    private static $instance;
    private $connection;

    private function __construct()
    {
        $driver = 'sqlsrv';
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? '1433';
        $database = $_ENV['DB_DATABASE'] ?? 'ITAMS';
        $username = $_ENV['DB_USERNAME'] ?? 'sa';
        $password = $_ENV['DB_PASSWORD'] ?? ''; 

        try {
            $dsn = "$driver:Server=$host,$port;Database=$database";
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Database Connection Error: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }
}