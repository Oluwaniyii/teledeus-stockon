<?php 

declare(strict_types=1);

use DI\Container;
use Dotenv\Dotenv;
use DI\Bridge\Slim\Bridge as SlimAppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container;
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$app = SlimAppFactory::create($container);

// Global Middlewares
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(false,true,true);

// Routes
(require __DIR__ . '/../app/routes.php')($app);

$app->run();