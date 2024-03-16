<?php

namespace Database;

use Dotenv\Dotenv;
use PDO;
use PDOException;

class Database
{
    private static ?PDO $conn = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$conn === null) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $dotenv->load();

            $host = $_ENV['DB_HOST'];
            $username = $_ENV['DB_USERNAME'];
            $password = $_ENV['DB_PASSWORD'];
            $dbname = $_ENV['DB_DATABASE'];

            try {
                self::$conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Erro de conexão: ' . $e->getMessage();
            }
        }

        return self::$conn;
    }
}
