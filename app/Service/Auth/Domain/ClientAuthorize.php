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
    
    public function __construct(){
        $this->repository = new ClientAuthRepository();
    }

    public function __invoke(Request $request, Response $response): Response {
        $requiredData = [  "client_id",  "client_secret"];
        $credentials = (new Input($request->getParsedBody()) )->extract($requiredData);

        $validatorSchema = [
            "client_id" => [
                    "name"=>"Client id",
                    "required"=>true,
            ],
            "client_secret" => [
                "name"=>"Client secret",
                "required"=>true,
            ],
        ];

        Validator::validate($credentials, $validatorSchema);

        if(!Validator::validated()) return Responder::respondErr("Invalid Credentials!", 400);

        // getAppByCredentials
        $app = $this->findApp($credentials['client_id'], $credentials['client_secret']);

        if(!count($app)) // Bad crdentials
            return Responder::respondErr("Invalid Credentials!", 400);

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
}

