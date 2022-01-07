<?php 

declare(strict_types=1);

namespace App\Service\OAuth\Domain;

use App\Lib\HexGenerator;
use App\JWT ;
use App\Service\Auth\Auth ;
use App\Http\Session;
use App\Service\OAuth\Repository\OAuthRepository;
use App\Service\OAuth\Repository\AppRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;

// This class should be extending Auth class!
class Authorize {
    //Onsite errors
    protected const INVALID_CLIENT_ERROR = "INVALID_CLIENT";
    protected const INVALID_CLIENT_URL_ERROR = "INVALID_CLIENT_URL";
    protected const SOMETHING_WENT_WRONG_ERROR = "SOMETHING_WENT_WRONG";

    //Api errors
    protected const REQUEST_DENIED_ERROR = ['error'=>"", "description" => "the+user+denied+access"];
    protected const INVALID_RESPONSE_ERROR = ['error'=>"invalid_response", "description" => "response+specified+is+not+supported+by+the+auth+server"];
    protected const API_SERVER_ERROR = ['error'=>"server_error", "description" => "something+wnt+wrong+on+our+end"];

    private $auth_initiator = null;
    private $oauth_auth_request_uri;
    private $oauthRepository;
    private $appRepository;
    private $response_error;
    private $response_code;
    private $client_url;
    private $client_error_redirect_url;


    public function __construct(){
        $this->auth = new Auth();
        $this->oauth_auth_request_uri = $_SERVER['REQUEST_URI'];
        $this->appRepository = new AppRepository();
        $this->oauthRepository = new OAuthRepository();
    }


    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);
        
        $response_type = Requester::getQueryParam('response_type'); 
        $client_id = Requester::getQueryParam('client_id'); 
        $redirect_url = Requester::getQueryParam('redirect_uri'); 
        $state = Requester::getQueryParam('state'); 

        // Validate client id and load client_details
        if(!$this->validateClient($client_id))
            return $this->callOnsiteError(Authorize::INVALID_CLIENT_ERROR);
        else 
            $this->loadClient($client_id);
        
        // If redirect uri is specified, confirm redirect url
        if($redirect_url){
            if ($redirect_url !== $this->client_url){
                return $this->callOnsiteError(Authorize::INVALID_CLIENT_URL_ERROR);
            }
            else $this->client_url = urldecode($redirect_url);
        } 

        //Flag error if request "response_type" is not set to code
        if (!$this->validateResponseType($response_type)){
            return $this->callApiError(Authorize::INVALID_RESPONSE_ERROR);
        }

        // Strore uri data in session
        $client_validated_uri['client_id'] = $client_id;
        $client_validated_uri['client_redirect_url'] = $this->client_url;
        $client_validated_uri['state'] = $state;

        Session::set('client_validated_uri', $client_validated_uri);

       #User Grant Authorization
       //Require User Login
        if(!$this->auth->isUserLoggedIn())
            return Responder::redirect("/auth/login?referer=" . urlencode($this->oauth_auth_request_uri));
        else 
           return Responder::view("userAuthorize.twig.html");

        // User Grant Authorization
        return Responder::view("userAuthorize.twig.html");
    }


    private function loadClient($clientID){
        $client_app = $this->appRepository->findAppWithClientId($clientID);
        $this->client_url = $client_app['success_redirect_url'];
        $this->client_error_redirect_url = $client_app['error_redirect_url'];
    }


    private function validateClient($client_id){
      if(is_null($client_id))
        return false;
    
      if( empty($this->appRepository->findAppWithClientId($client_id)) )
        return false;

        return true;
    }

    private function validateResponseType($response_type){
        if(is_null($response_type))
           return false;

        if ($response_type !== "code")
            return false;

       return true;
    }

    private function validateClientURL($redirect_url, $client_url){
        if(!$redirect_url !== $client_url)
            return false;

       return true;
    }

    //    private function callApiError(){}

   private function callOnsiteError($error_type){
          Session::set('oauth_error', $error_type);
          return Responder::redirect('/oauth/error');
   }

   private function callApiError($errorConst){
     $error_redirect_uri = $this->client_error_redirect_url ? $this->client_error_redirect_url : $this->client_url;
     $location = $error_redirect_uri . "?error=" . $errorConst['error'] . "&error_description=" . $errorConst['description'];

    return Responder::redirect($location);
   }

   protected function get_client_validated_uri(){
        if(Session::check('client_validated_uri'))
            return Session::get('client_validated_uri');
        else 
            return null;
   }

    protected function approveClient(){
        $client_validated_uri = $this->get_client_validated_uri();
        
        $client_id = $client_validated_uri['client_id'] ;
        $client_redirect_url = $client_validated_uri['client_redirect_url'] ;
        $user_identity = $this->auth->getLoggedinUser() ;
        $issued_at = time() ;
        $expiration_time = 60*2 ;
        $state = $client_validated_uri['state'] ;
        $code =  $this->generateCode();

        //Generate code
        $tokenData = [];
        $tokenData['unique_id'] = uniqid();
        $tokenData['code_string'] = $code;
        $tokenData['client_id'] = $client_id;
        $tokenData['client_redirect_url'] = $client_redirect_url;
        $tokenData['user_identity'] = $user_identity;
        $tokenData['issued_at'] = $issued_at;
        $tokenData['expiration_time'] = $expiration_time;

        //store In Database
        if($this->saveTokenToDb($tokenData)){
            $location = urldecode($client_redirect_url) . "?code={$code}&expires_in={$expiration_time}&state={$state}";
            return Responder::redirect($location);
        }
        else {
            return $this->callOnsiteError(Authorize::API_SERVER_ERROR);
        }
    }

    protected function denyClient(){
        $client_validated_uri = $this->get_client_validated_uri();

        $client_redirect_url = $client_validated_uri['client_redirect_url'] ;
        $location = urldecode($client_redirect_url) . "?error=access_denied" .  "&error_discrption=the+user+deneid+the+request";
        return Responder::redirect($location);
    }


    private function saveTokenToDb($tokenCode){
        return $this->oauthRepository->saveTokenCode($tokenCode);
    }

    private function generateCode(){
        $hex = HexGenerator::getToken(26);
        $uniq = uniqid();
        $codeString = "CD";

        for( $i=0; $i<strlen($hex); ){
            $codeString .= $hex[$i];
            for($j=0; $j<strlen($uniq);){
               if( $i/2 == 0)
                 $codeString .= $uniq[$j];
               $j++;
            }
            $i++;
        }
       
        return $codeString;
    }

}
