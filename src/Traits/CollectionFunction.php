<?php

namespace Traits;

use Classes\Collection;
use Models\Model;

trait CollectionFunction
{
    public function get($index): Model
    {
        return $this->itens[$index];
    }

    public function push($item): void
    {
        $this->itens[] = $item;
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
        return new Collection(array_map($callback, $this->itens));
    }

    public function filter($callback): Collection
    {
        return new Collection(array_filter($this->itens, $callback));
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