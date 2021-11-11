<?php 

declare(strict_types=1);

namespace App\Service\Auth\Domain;

use App\JWT ;
use App\Lib\Validator;
use App\Lib\Input;
use App\Service\Auth\Repository\ClientAuth as ClientAuthRepository;
use App\Service\User\Http\Responder;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response ;

class ClientAuthorize {
    private $repository ;
    private $requestObject;
    
    public function __construct(){
        $this->repository = new ClientAuthRepository();
    }

    public function __invoke(Request $request, Response $response): Response {
        $this->requestObject = $request;

        $credentials = $this->getClientCredentials();
        extract($credentials);

        if( empty($client_id) || empty($client_secret) ) return Responder::respondeErr("Invalid Credentials", 400);  // Bad crdentials

        $app = $this->findApp($client_id, $client_secret); // getAppByCredentials

        if(!count($app)) return Responder::respondErr("Invalid Credentials!", 400);  // Bad crdentials

        // Send back access token, voila
        $token = [];
        $token['type'] = "Basic";
        $token['access_token'] = ( JWT::generateToken($app))['token'];
        $token['expires_in'] = "3600";
        $data['token']=$token;
      
        return Responder::respondSuccess($data);
    }

    private function findApp($client_id, $client_secret){
        return $this->repository->getAppByCredentials($client_id, $client_secret);
    }

    private function getClientCredentials(){
        if($this->issetCredentialsHeader()){
            $credentials = $this->getCredentialsHeader();
        }
        else {
            $credentials = $this->getCredentialsFormdata();
        }

        return $credentials;
    }

    private function issetCredentialsHeader(){
        return isset($_SERVER['HTTP_AUTHORIZATION']);
    }

    private function getCredentialsHeader(){
        $client_credentials = $_SERVER['HTTP_AUTHORIZATION'];
        $client_credentials = base64_decode( str_ireplace('Basic ', '', $client_credentials) );
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
}

