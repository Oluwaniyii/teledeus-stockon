<?php 

declare(strict_types=1);

namespace App\Service\OAuth\Domain;

use App\Lib\Input;
use App\Service\OAuth\Repository\OAuthRepository;
use App\Service\OAuth\Repository\AppRepository;
use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class GetTokenInfo{
    private $appRepository;
    private $userRepository;
    private $oauthRepository;
    private $requestObject;

    // Error constants '
    private const INVALID_CLIENT_ERROR = ['error'=>"invalid_client", "description" => "invalid client dredentials"];
    private const UNAUTHENTICATED_CLIENT_ERROR = ['error'=>"invalid_client", "description" => "client is not the right owner of provided token"];
    private const NO_TOKEN_FOUND_RESPONSE = ["error"=>"unregistered_token", "description"=>"Token is either incorrect or have been emptied by the server,"];


    public function __construct(){
        $this->oauthRepository = new OAuthRepository();
        $this->appRepository = new AppRepository();
        $this->userRepository = new UserRepository();
    }

    public function __invoke(Request $request, Response $response): Response {
    
       // Verify Client credentials
       $this->requestObject = $request ;
       $requiredInputData = ["token"];
       $inputData = (new Input($this->requestObject->getParsedBody()))->extract($requiredInputData);
       extract($inputData);

     
       $credentials = $this->getClientCredentials();
       $client_id = $credentials["client_id"];
       $client_secret = $credentials["client_secret"];

       if( empty($client_id) || empty($client_secret) ) return $this->callApiResponse(self::INVALID_CLIENT_ERROR, 401);
       $app = $this->findApp($client_id, $client_secret); //findAppWithCredentials

       if(empty($app)) return $this->callApiResponse(self::INVALID_CLIENT_ERROR, 401);
       

       // Get Code
      $tokeneData = $this->oauthRepository->getAccessToken($token);

      if(empty($tokeneData) ){
        return $this->callApiResponse(self::NO_TOKEN_FOUND_RESPONSE, 403);
      }


       $tokenUniqueId = $tokeneData["unique_id"];
       $tokenString = $tokeneData['token_string'];
       $tokenClientId = $tokeneData["client_id"];
       $tokenUserId = $tokeneData['user_identity'];
       $tokenIssuedAt = (int) $tokeneData["issued_at"];
       $tokenExpirationTime = (int) $tokeneData["expiration_time"];
       $tokenIsExpired = (int) $tokeneData["is_expired"];
       $tokenIsRevoked = (int) $tokeneData["revoked"];
       $isTokenExpired = time() > ($tokenIssuedAt + $tokenExpirationTime);

       $tokenClientUsername = ($this->userRepository->findById($tokenUserId))['username'];

      if($client_id !== $tokenClientId)  return $this->callApiResponse(self::UNAUTHENTICATED_CLIENT_ERROR, 401);

      //If all goes well, Return Token Info
             
        $tokenInfo = [];
        $tokenInfo["access_token"] = $tokenString;
        $tokenInfo["client_id"] = $tokenClientId;
        $tokenInfo["user_username"] = $tokenClientUsername;
        $tokenInfo["valid"] = $tokenIsExpired == 0 ? true : false ;
        $tokenInfo["expiration_time"] = $tokenIssuedAt + $tokenExpirationTime ;
        $tokenInfo["is_expired"] = $isTokenExpired ;
        $tokenInfo["revoked"] = $tokenIsRevoked != 0 ? true : false ; 


        $response->getBody()->write(json_encode($tokenInfo));
        return $response
        ->withHeader("Content-Type", "application/json")
        ->withHeader("Cache-Control", "no-store");
       
    }


    private function callApiResponse(array $errorDetails, $status) {
        $payload['error'] = $errorDetails['error'];
        $payload['error_description'] = $errorDetails['description'];
    
        $payload = json_encode($payload);
        return Responder::respond($payload, $status);
       }


    private function getClientCredentials(){
        if($this->issetCredentialsHeader())
            $credentials = $this->getCredentialsHeader();
        else 
            $credentials = $this->getCredentialsFormdata();

        return $credentials;
    }

    private function issetCredentialsHeader(){
        return isset($_SERVER['HTTP_AUTHORIZATION']);
    }

    private function getCredentialsHeader(){
        $client_credentials = $_SERVER['HTTP_AUTHORIZATION'];
        $client_credentials = base64_decode( str_replace('Basic ', '', $client_credentials) );
        $client_credentials = explode(':', $client_credentials);

        $credentials['client_id'] = $client_credentials[0];
        $credentials['client_secret'] = $client_credentials[1];

        return $credentials;
    }

    private function getCredentialsFormdata(){
        $requiredData = [  "client_id",  "client_secret"];
        $credentials = (new Input($this->requestObject->getParsedBody()) )->extract($requiredData);

        return $credentials;
    }
    
    private function findApp($client_id, $client_secret){
        return $this->appRepository->findAppWithCredentials($client_id, $client_secret);
    }

}