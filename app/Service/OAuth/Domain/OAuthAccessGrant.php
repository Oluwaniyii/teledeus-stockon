<?php 

declare(strict_types=1);

namespace App\Service\OAuth\Domain;

use App\Service\Auth\Auth ;
use App\Service\OAuth\Domain\Authorize ;
use App\Http\Session;
use App\Service\OAuth\Repository\AppRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;
use App\Lib\Input;

//All this class does is accept deny or allow user grant and call appropriate OAuth class method

class OAuthAccessGrant extends Authorize {
    private $client_validated_uri;
    // Input::access  deny || allow

    public function __construct(){
        parent::__construct();

        $this->auth = new Auth();
        Session::init();
    }


    public function __invoke(Request $request, Response $response): Response {
       $data = (new Input($request->getParsedBody()))->extract(['access']);
       extract($data);

       $this->client_validated_uri = $this->get_client_validated_uri();

       if(is_null($this->client_validated_uri)){
            return $this->callOnsiteError(Authorize::INVALID_CLIENT_ERROR); //No auth request waspreviously validated
       }

        if(!$this->auth->isUserLoggedIn()) {
            return $this->callOnsiteError(Authorize::SOMETHING_WENT_WRONG_ERROR); //Sending request with no user responsible
        }

      if($access === "allow") return $this->approveClient();
      if($access === "deny") return $this->denyClient();

     }

      public function approveClient (){
          return parent::approveClient();
      }

      public function denyClient (){
        return parent::denyClient();
    }

}