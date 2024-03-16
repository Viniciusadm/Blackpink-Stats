<?php

namespace Models;

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use PDO;
use PDOException;
use stdClass;

date_default_timezone_set('America/Sao_Paulo');

class Model
{
    protected string $table = '';
    protected PDO $conn;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $host = $_ENV['DB_HOST'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];
        $dbname = $_ENV['DB_DATABASE'];

        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Erro de conexão: ' . $e->getMessage();
        }
    }

    public function all($select = "*"): array
    {
        $stmt = $this->conn->prepare("SELECT $select FROM $this->table");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function find(int $id): stdClass|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function first($where): stdClass|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table $where ORDER BY id LIMIT 1");
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function last($where): stdClass|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table $where ORDER BY id DESC LIMIT 1");
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function limit(int $limit, int $offset = 0): array
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table LIMIT $limit OFFSET $offset");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function create(array $data): bool
    {
        $columns = implode(', ', array_map(function ($column) {
            return "`$column`";
        }, array_keys($data)));

        $values = ':' . implode(', :', array_keys($data));

        $stmt = $this->conn->prepare("INSERT INTO $this->table ($columns) VALUES ($values)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        return $stmt->execute();
    }

    public function update(int $id, array $data): bool
    {
        $fields = '';
        foreach (array_keys($data) as $key) {
            $fields .= "$key=:$key, ";
        }
        $fields = rtrim($fields, ', ');
        $stmt = $this->conn->prepare("UPDATE $this->table SET $fields WHERE id = :id");
        $stmt->bindValue(':id', $id);
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteAll(string $where): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table $where");
        return $stmt->execute();
    }
}
