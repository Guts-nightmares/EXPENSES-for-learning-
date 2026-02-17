<?php

class Database {
    private $pdo;

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';
        
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config['db']['host'],
            $config['db']['port'],
            $config['db']['dbname']
        );

        try {
            $this->pdo = new PDO($dsn, $config['db']['user'], $config['db']['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
