<?php 

declare(strict_types=1);

namespace App\Middleware ;

use App\JWT ;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler ;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext ;
use Slim\Psr7\Response;

class EnsureAuthenticatedMiddleware {
    public function __invoke (Request $request, RequestHandler $handler): Response {
        // Want to ensure that only the logged in account is accessible by the same user

        // Although RouteContext is like using a sledge hammer to kill a mosquito, it's the only dependable solution so far ;(
         $routeContext =  RouteContext::fromRequest($request);
         $route = $routeContext->getRoute();
         $id = $route->getArgument('id');

        # Grab existing response stream to safe
        $response = $handler->handle($request);
        $existingContent =  (string) $response->getBody();

        # Check for authentication
        if ( !$this->checkAuthHeader() ) return $this->respond401();

        $token = $this->getAuthToken();

        if ( !JWT::verifyTokenPayload($token, ["uid"=>$id]) ) return $this->respond401() ;
        else return $this->respond($existingContent); #next();
    }

    private function checkAuthHeader(){
        return isset($_SERVER['HTTP_AUTHORIZATION']) ? true : false ;
    }

    private function getAuthToken(){
      return isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null ;
    }

    private function verifyAuthToken($token){
        return JWT::verifyToken($token);
    }

    private function respond401(){
        $response = new Response ;
        $response->getBody()->write("Unauthorised");
        return $response->withStatus(401)
        ->withHeader("Content-Type", "text/plain");
        exit();
    }

    private function respond($data){ // return existing request data
        $response = new Response ;
        $response->getBody()->write($data);
        return $response ->withHeader("Content-Type", "application/json");
    }
}