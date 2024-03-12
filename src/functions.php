<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function dd(): void
{
    $args = func_get_args();
    foreach ($args as $var) {
        if (php_sapi_name() === 'cli') {
            print_r($var);
            echo PHP_EOL;
        } else {
            echo '<pre>';
            print_r($var);
            echo '</pre>';
        }
    }
    die();
}


function getObjectById($objects, $id) {
    foreach ($objects as $object) {
        if ($object->id == $id) {
            return $object;
        }
    }
    return null;
}