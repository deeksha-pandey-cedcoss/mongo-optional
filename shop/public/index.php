<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Config;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Logger;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader

require_once "../vendor/autoload.php";


$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces(array(

    'MyApp\Controllers' => "../app/controllers/",

));
$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);
$container->set(
    'logger',
    function () {

        $adapter1 = new Phalcon\Logger\Adapter\Stream(APP_PATH . '/storage/logs/login.log');
        $adapter2 = new Phalcon\Logger\Adapter\Stream(APP_PATH . '/storage/logs/error.log');
        $logger =   new Logger(
            'messages',
            [
                'login' => $adapter1,
                'error' => $adapter2,
            ]
        );

        return $logger;
    }
);

$container->set(
    'mongo',
    function () {
        $mongo = new MongoDB\Client(
            'mongodb+srv://deekshapandey:Deeksha123@cluster0.whrrrpj.mongodb.net/?retryWrites=true&w=majority'
        );

        return $mongo->bookstore;
    },
    true
);
$container->set(
    'dispatcher',
    function () {
        $dispatcher = new Dispatcher();

        $dispatcher->setDefaultNamespace(
            'MyApp\Controllers'
        );

        return $dispatcher;
    }
);
$application = new Application($container);


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
