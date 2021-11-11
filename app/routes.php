<?php 

use Slim\App ;
use App\Service\Auth\ClientAuthController ;
use App\Service\User\UserController ;
use App\Middleware\EnsureAuthenticatedMiddleware;
use App\Middleware\EnsureRegisteredClientMiddleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response ;


return function (App $app)
{

    $app->group(
    '',
    function() use ($app) {
        
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
    
        $app->get("/users", [UserController::class, 'get']);
        $app->get("/users/{id}", [UserController::class, 'view']); // ->add(EnsureAuthenticatedMiddleware::class);
        $app->post("/users", [UserController::class, 'add']);
        $app->put("/users/{id}", [UserController::class, 'update'])->add(EnsureAuthenticatedMiddleware::class);
        $app->delete("/users/{id}", [UserController::class, 'remove'])->add(EnsureAuthenticatedMiddleware::class);
        // User Auth
        $app->post("/users/login", [UserController::class, 'login']);

    }
    )->add(EnsureRegisteredClientMiddleware::class);

    //Client Auth
    $app->post("/auth/client",  [ClientAuthController::class, 'credentials']);

};