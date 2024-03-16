<?php

namespace Classes;

use ArrayIterator;
use Countable;
use Database\Database;
use IteratorAggregate;
use PDO;
use Traits\CollectionFunction;
use Traversable;

class Collection implements IteratorAggregate, Countable
{
    use CollectionFunction;

    private array $itens = [];
    protected PDO $conn;

    public function __construct($initial = [])
    {
        $this->conn = Database::getConnection();

        if ($initial) {
            $this->itens = $initial;
        }
    }

    public function update(array $data): bool
    {
        $primaryKey = $this->get(0)->primaryKey;
        $table = $this->get(0)->table;
        $ids = implode(',', $this->map(fn($item) => $item->{$primaryKey})->toArray());

        $fields = '';

        foreach (array_keys($data) as $key) {
            $fields .= "$key=:$key, ";
        }

        $fields = rtrim($fields, ', ');
        $stmt = $this->conn->prepare("UPDATE $table SET $fields WHERE $primaryKey IN ($ids)");

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $exec = $stmt->execute();

        if ($exec) {
            foreach ($this->itens as $item) {
                foreach ($data as $key => $value) {
                    $item->{$key} = $value;
                }
            }
        }

        return $exec;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->itens);
    }
}