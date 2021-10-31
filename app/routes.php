<?php 

use Slim\App ;
use App\Service\User\UserController ;
use Psr\Http\Message\ResponseInterface as Response ;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Middleware\EnsureAuthenticatedMiddleware;


return function (App $app)
{
    $app->get("/", function(Request $request, Response $response){
        $response->getBody()->write(json_encode(
            [
                "status" => "success",
                "message" => "Welcome to Teledeus Ecommerce Api",
                "documentation" => "http://inthemaking.com",
            ]
        ));
        return $response
        ->withHeader('Content-Type', 'application/json');
    });
  
    $app->get("/users", [UserController::class, 'get']); // Enable safe mode
    $app->get("/users/{id}", [UserController::class, 'view'])->add(EnsureAuthenticatedMiddleware::class);
    $app->post("/users", [UserController::class, 'add']);
    $app->put("/users/{id}", [UserController::class, 'update'])->add(EnsureAuthenticatedMiddleware::class);
    $app->delete("/users/{id}", [UserController::class, 'remove'])->add(EnsureAuthenticatedMiddleware::class);
    $app->post("/users/login", [UserController::class, 'login']);
};