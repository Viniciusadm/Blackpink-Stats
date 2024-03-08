<?php

spl_autoload_register(function ($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . '/src/' . $className . '.php';
});

$routes = [
    '/' => ['controller' => 'SiteController', 'action' => 'home'],
];

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');

if (array_key_exists($requestUri, $routes)) {
    $route = $routes[$requestUri];
    $controllerName = $route['controller'];
    $action = $route['action'];
} else {
    $controllerName = 'SiteController';
    $action = 'notFound';
}

$controllerClassName = 'Controllers\\' . $controllerName;
$controller = new $controllerClassName();
$controller->$action();
