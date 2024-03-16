<?php

namespace Models;

require 'vendor/autoload.php';

use Classes\Collection;
use Database\Database;
use PDO;
use Traits\ModelFunctions;

date_default_timezone_set('America/Sao_Paulo');

class Model
{
    use ModelFunctions;

    public string $table = '';
    public string $primaryKey = 'id';
    protected PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getConnection();
    }

    /**
     * @param string $select
     * @return Collection
     */
    public function all(string $select = "*"): Collection
    {
        $array = $this->query("SELECT $select FROM $this->table");

        return new Collection($array);
    }

    public function find(int $id): Model|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        $model = new static();

        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }

        return $model;
    }

    public function first(string $where = ''): Model|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table $where ORDER BY id LIMIT 1");
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        $model = new static();

        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }

        return $model;
    }

    public function last($where): Model|null
    {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table $where ORDER BY id DESC LIMIT 1");
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            return null;
        }

        $result = $stmt->fetch(PDO::FETCH_OBJ);

        $model = new static();

        foreach ($result as $key => $value) {
            $model->{$key} = $value;
        }

        return $model;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function limit(int $limit, int $offset = 0): Collection
    {
        $array = $this->query("SELECT * FROM $this->table LIMIT $limit OFFSET $offset");

        return new Collection($array);
    }

    public function create(array $data): Model|null
    {
        $columns = implode(', ', array_map(function ($column) {
            return "`$column`";
        }, array_keys($data)));

        $values = ':' . implode(', :', array_keys($data));

        $stmt = $this->conn->prepare("INSERT INTO $this->table ($columns) VALUES ($values)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $exec = $stmt->execute();

        if ($exec) {
            $id = $this->conn->lastInsertId();
            return $this->find($id);
        }

        return null;
    }

    public function update(array $data): bool
    {
        $primaryKey = $this->getPrimaryKey();
        $id = $this->{$primaryKey};

        $fields = '';

        foreach (array_keys($data) as $key) {
            $fields .= "$key=:$key, ";
        }

        $fields = rtrim($fields, ', ');
        $stmt = $this->conn->prepare("UPDATE $this->table SET $fields WHERE $primaryKey = :id");
        $stmt->bindValue(':id', $id);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $exec = $stmt->execute();

        if ($exec) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        return $exec;
    }

    public function delete(): bool
    {
        $primaryKey = $this->getPrimaryKey();
        $id = $this->{$primaryKey};

        $stmt = $this->conn->prepare("DELETE FROM $this->table WHERE $primaryKey = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteAll(string $where): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM $this->table $where");
        return $stmt->execute();
    }

    private function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    /**
     * @param string $query
     * @return array
     */
    private function query(string $query): array
    {
        $stmt = $this->conn->prepare($query);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_OBJ);

        $objects = [];

        foreach ($result as $object) {
            $model = new static();

            foreach ($object as $key => $value) {
                $model->{$key} = $value;
            }

            $objects[] = $model;
        }

        return $objects;
    }
}
