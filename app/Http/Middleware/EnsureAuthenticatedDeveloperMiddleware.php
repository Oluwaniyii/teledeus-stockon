<?php 

declare(strict_types=1);

namespace App\Middleware ;

use App\Service\_Developer\Auth\DeveloperAuth ;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler ;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext ;
use Slim\Psr7\Response;
use App\Http\Responder;


class EnsureAuthenticatedDeveloperMiddleware {
    public function __invoke (Request $request, RequestHandler $handler): Response {
       
        $auth = new DeveloperAuth();
        $referer = $_SERVER['REQUEST_URI'];

        # Grab existing response stream to safe
        $response = $handler->handle($request);
        $existingContent =  (string) $response->getBody();

        if(!$auth->isUserLoggedIn())
            return Responder::redirect("/developer/auth/login?referer=" . urlencode($referer));

        return $this->respond($existingContent);
    }

    private function respond($data){ // return existing request data
        $response = new Response ;
        $response->getBody()->write($data);
        return $response ->withHeader("Content-Type", "text/html");
    }
}