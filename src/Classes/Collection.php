<?php

namespace Classes;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Models\Model;
use Traversable;

class Collection implements IteratorAggregate, Countable {
    private array $itens = [];

    public function __construct($initial = [])
    {
        if ($initial) {
            $this->itens = $initial;
        }
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->itens);
    }

    public function get($index): Model
    {
        return $this->itens[$index];
    }

    public function push($item): void
    {
        if (get_class($item) === get_class($this->itens[0])) {
            $this->itens[] = $item;
        }
    }

    public function remove($index): void
    {
        if (isset($this->itens[$index])) {
            unset($this->itens[$index]);
        }
    }

    public function count(): int
    {
        return count($this->itens);
    }

    public function toArray(): array
    {
        return $this->itens;
    }

    public function first(): Model
    {
        return $this->itens[0];
    }

    public function last(): Model
    {
        return $this->itens[count($this->itens) - 1];
    }

    public function map($callback): Collection
    {
        $this->itens = array_map($callback, $this->itens);
        return $this;
    }

    public function filter($callback): Collection
    {
        $this->itens = array_filter($this->itens, $callback);
        return $this;
    }

    public function find($callback): Model
    {
        return array_filter($this->itens, $callback)[0];
    }

    public function findIndex($callback): int
    {
        $index = array_filter($this->itens, $callback);
        return array_keys($index)[0];
    }

    public function reduce($callback, $initial = null)
    {
        return array_reduce($this->itens, $callback, $initial);
    }

    public function sort(string $key, string $order = 'asc'): Collection
    {
        usort($this->itens, function ($a, $b) use ($key, $order) {
            if ($order === 'asc') {
                return $a->{$key} <=> $b->{$key};
            } else {
                return $b->{$key} <=> $a->{$key};
            }
        });

        return $this;
    }
}