<?php 

declare(strict_types=1);

use DI\Container;
use DI\Bridge\Slim\Bridge as SlimAppFactory;
use App\Middleware\TestMiddleware;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container;

$app = SlimAppFactory::create($container);

$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true,true,true);


// Routes
(require __DIR__ . '/../app/routes.php')($app);

$app->run();