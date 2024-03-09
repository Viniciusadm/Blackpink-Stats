<?php

namespace Classes;

class Data
{
    public function __construct($object)
    {
        foreach ($object as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function formatNumber(string $key): string
    {
        return number_format($this->{$key}, 0, ',', '.');
    }

    public function formatDate(string $key): string
    {
        return date('d/m/Y', strtotime($this->{$key}));
    }
}