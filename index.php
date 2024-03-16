<?php

require_once 'src/functions.php';

spl_autoload_register(function ($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    require_once __DIR__ . '/src/' . $className . '.php';
});

$routes = include 'routes.php';

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');

$uri = [];
foreach ($routes as $route => $value) {
    $route = str_replace('/', '\/', $route);
    $route = preg_replace('/\{[a-z]+\}/', '([a-z0-9-]+)', $route);
    $route = '/^' . $route . '$/';

    if (preg_match($route, $requestUri, $matches)) {
        $uri = [
            'controller' => $value['controller'],
            'action' => $value['action'],
            'params' => $matches
        ];

        break;
    }
}

if (empty($uri)) {
    $uri = [
        'controller' => 'SiteController',
        'action' => 'notFound',
        'params' => []
    ];
}

$controller = 'Controllers\\' . $uri['controller'];
$action = $uri['action'];
$params = array_slice($uri['params'], 1);

$controller = new $controller();
$controller->$action(...$params);