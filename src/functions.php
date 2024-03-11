<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn]
function dd(): void
{
    $args = func_get_args();
    foreach ($args as $var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
    die();
}