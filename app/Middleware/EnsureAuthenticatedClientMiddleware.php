<?php 

declare(strict_types=1);

namespace App\Middleware ;

use App\Service\OAuth\OAuth;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler ;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteContext ;
use Slim\Psr7\Response;

class EnsureAuthenticatedClientMiddleware {
    public function __invoke (Request $request, RequestHandler $handler): Response {
        // Want to ensure that only the logged in account is accessible by the same app
         $oauth = new OAuth;
        // Although RouteContext is like using a sledge hammer to kill a mosquito, it's the only dependable solution so far ;(
         $routeContext =  RouteContext::fromRequest($request);
         $route = $routeContext->getRoute();
         $getClient = $request->getQueryParams()['client_id'];
         $postClient = $request->getParsedBody()['client_id'] ? $request->getParsedBody()['client_id'] : '' ;
         
         $client_id = $getClient ? $getClient : $postClient ;
         $user_id = $route->getArgument('user_id');


        # Grab existing response stream to safe
        $response = $handler->handle($request);
        $existingContent =  (string) $response->getBody();

        # Check for authentication
        if ( !$this->checkAuthHeader() ) return $this->respond401();
        if( empty($client_id) ) return $this->respond401();

        $token = $this->getAuthToken();
        $tokenData = $oauth->getAccessToken($token);
        
        //If tokenData is empty
        if(empty($tokenData)) return $this->respond401();

        $token_id =  $tokenData['unique_id'];
        $token_string =  $tokenData['token_string'];
        $token_user_identity = $tokenData['user_identity'];
        $token_client_id = $tokenData['client_id'];
        $token_issued_at = (int) $tokenData['issued_at'];
        $token_expiration_time = (int) $tokenData['expiration_time'];
        $is_token_expired = time() > ( $token_issued_at + $token_expiration_time );

        if($client_id !== $token_client_id) return $this->respond401();
        if($user_id !== $token_user_identity) return $this->respond401();
        if( $is_token_expired )  {
            //invalidate Access Token
            $oauth->invalidateAccessToken($token_id);
            return $this->respond401();
        }
        
        return $this->respond($existingContent);
    }

    private function checkAuthHeader(){
        return isset($_SERVER['HTTP_AUTHORIZATION']) ? true : false ;
    }

    private function getAuthToken(){
      return isset($_SERVER['HTTP_AUTHORIZATION']) ? str_replace('Bearer ', '', $_SERVER['HTTP_AUTHORIZATION']) : null ;
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