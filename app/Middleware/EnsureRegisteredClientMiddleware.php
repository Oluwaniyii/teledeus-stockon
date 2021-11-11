<?php 

declare(strict_types=1);

namespace App\Middleware ;

use App\JWT ;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler ;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;

class EnsureRegisteredClientMiddleware {
    public function __invoke (Request $request, RequestHandler $handler): Response {
        $response = $handler->handle($request);
        $existingContent =  (string) $response->getBody();

        # Check for authentication
        if ( !$this->checkAuthHeader() ) return $this->respond403();

        $token = $this->getAuthToken();

        if ( !JWT::verifyToken($token, '')) return $this->respond403() ;
        else return $this->respond($existingContent); #next();
    }


    private function checkAuthHeader(){
    return isset($_SERVER['HTTP_X_API_KEY']) ? true : false ;
    }

    private function getAuthToken(){
    return isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : null ;
    }

    private function verifyAuthToken($token){
    return JWT::verifyToken($token);
    }

    private function respond403(){
    $response = new Response ;
    $response->getBody()->write("Forbidden");
    return $response->withStatus(403)
    ->withHeader("Content-Type", "text/plain");
    exit();
    }

    private function respond($data){ // return existing request data
    $response = new Response ;
    $response->getBody()->write($data);
    return $response ->withHeader("Content-Type", "application/json");
    }
}


        