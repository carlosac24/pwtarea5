<?php

require_once __DIR__ . '/../app/Config/config.php';

// Simple Autoloader
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/../app/Core/',
        __DIR__ . '/../app/Controllers/',
        __DIR__ . '/../app/Models/'
    ];

    foreach ($paths as $path) {
        if (file_exists($path . $class . '.php')) {
            require_once $path . $class . '.php';
            return;
        }
    }
});

Auth::init();

$router = new Router();

// Define Routes
$router->add('GET', 'home', 'DashboardController', 'index');
$router->add('GET', 'login', 'AuthController', 'loginForm');
$router->add('POST', 'login', 'AuthController', 'login');
$router->add('GET', 'logout', 'AuthController', 'logout');

$router->add('GET', 'books', 'BookController', 'index');
$router->add('POST', 'books', 'BookController', 'store'); // Handle create/update

$router->add('GET', 'users', 'UserController', 'index');
$router->add('POST', 'users', 'UserController', 'store');

$router->add('GET', 'transactions', 'TransactionController', 'index');
$router->add('POST', 'transactions', 'TransactionController', 'store');

$router->add('GET', 'catalog', 'CatalogController', 'index');
$router->add('POST', 'catalog', 'CatalogController', 'index');

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
