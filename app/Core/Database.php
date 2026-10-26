<?php

namespace App\Core;

use PDO;
use PDOException;

class Database {
    private static $instances = [];

    public static function connect($name = 'core') {
        if (!isset(self::$instances[$name])) {
            $config = require __DIR__ . '/../../config/database.php';
            if (!isset($config[$name])) {
                throw new \Exception("Database configuration for '$name' not found.");
            }

            $dbConfig = $config[$name];
            try {
                $dsn = "mysql:host=" . $dbConfig['host'] . ";dbname=" . $dbConfig['db_name'] . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instances[$name] = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
            } catch (PDOException $e) {
                die("Connection failed for '$name': " . $e->getMessage());
            }
        }
        return self::$instances[$name];
    }
}
