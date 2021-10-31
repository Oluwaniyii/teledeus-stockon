<?php 

declare(strict_types=1);

namespace App\Middleware ;

use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler ;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class TestMiddleware {
    public function __invoke (Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        $existingContent =  $response->getBody();

        $response = new Response ;
        $response->getBody()->write("Process Middleware" . $existingContent);
        return $response->withStatus(200)->withHeader("Content-Type", "application/json");
    }
}