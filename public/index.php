<?php

session_start();


spl_autoload_register(function($class){

    $prefix = 'Ecommerce\\Shop\\';

    if (str_starts_with($class, $prefix)) {

        $class = str_replace($prefix, '', $class);

        $file = __DIR__ . '/../app/' . str_replace('\\', '/', $class) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }

});


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';


use Ecommerce\Shop\Core\Database;


$db = Database::getInstance()->getConnection();


$router = require_once __DIR__ . '/../config/routes.php';


$router->dispatch(
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD']
);