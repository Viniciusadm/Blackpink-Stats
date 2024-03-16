<?php

namespace Traits;

use Models\Model;

trait ModelFunctions
{
    public function formatNumber(string $key): string
    {
        return number_format($this->{$key}, 0, ',', '.');
    }

    public function formatDate(string $key): string
    {
        return date('d/m/Y', strtotime($this->{$key}));
    }

    public function set(string $key, $value): void
    {
        $this->{$key} = $value;
    }
}