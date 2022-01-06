<?php 

declare(strict_types=1);

namespace App\Service\OAuth\Domain;

use App\Lib\Input;
use App\Lib\HexGenerator;
use App\JWT ;
use App\Service\OAuth\Repository\OAuthRepository;
use App\Service\OAuth\Repository\AppRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class AccesTokenGrant{
    private $client_validated_uri;
    private $appRepository;
    private $oauthRepository;
    private $requestObject;
    private $tokenExpiry = (60 * 60 * 24 *30); //One month Long lifetime token

    protected const INVALID_REQUEST_ERROR = ['error'=>"invalid_request", "description" => ""];
    protected const INVALID_CLIENT_ERROR = ['error'=>"invalid_client", "description" => "invalid or missing client credentials"];
    protected const INVALID_GRANT_ERROR = ['error'=>"invalid_grant", "description" => "code is invalid"];
    protected const INVALID_URL_GRANT_ERROR = ['error'=>"invalid_grant", "description" => "provided url does not match auth url "];
    protected const EXPIRED_GRANT_ERROR = ['error'=>"invalid_grant", "description" => "code is expired"];
    protected const UNAUTHORIZED_CLIENT_ERROR = ['error'=>"unauthorized_client", "client is not to use specified grant type" => ""];
    protected const UNSUPPORTED_GRANT_ERROR = ['error'=>"unauthorized_client", "client is not to use specified grant type" => ""];
    protected const SOMETHING_WENT_ERROR = ['error'=>"server_error", "something went wrong on our end" => ""];
    // protected const INVALID_AUTH_CODE_ERROR = ['error'=>"invalid_grant", "description" => "provided url does not match auth url "];


    public function __construct(){
        $this->oauthRepository = new OAuthRepository();
        $this->appRepository = new AppRepository();
    }


    public function __invoke(Request $request, Response $response): Response {
       // Verify Client credentials
        $this->requestObject = $request;

       $requiredInputData = ["grant_type", "code", "redirect_url"];
       $data = (new Input($this->requestObject->getParsedBody()))->extract($requiredInputData);
       extract($data);
     
       $credentials = $this->getClientCredentials();
       $client_id = $credentials["client_id"];
       $client_secret = $credentials["client_secret"];

       if( empty($client_id) || empty($client_secret) ) return $this->callApiResponse(self::INVALID_CLIENT_ERROR, 401);
       $app = $this->findApp($client_id, $client_secret); //findAppWithCredentials

       if(empty($app)) return $this->callApiResponse(self::INVALID_CLIENT_ERROR, 401);
       
       if(strtolower($grant_type) !== "authorization_code")  return $this->callApiResponse(self::UNAUTHORIZED_CLIENT_ERROR, 400);

       // Get Code
      $codeData = $this->oauthRepository->getTokenCode($code);

      if(empty($codeData) ){
        return $this->callApiResponse(self::INVALID_GRANT_ERROR, 400);
      }

       $codeUniqueId = $codeData["unique_id"];
       $codeString = $codeData['code_string'];
       $codeClientId = $codeData["client_id"];
       $codeClientRedirectUrl = $codeData["client_redirect_url"];
       $codeUserId = $codeData['user_identity'];
       $codeIssuedAt = (int) $codeData["issued_at"];
       $codeExpirationTime = (int) $codeData["expiration_time"];
       $isCodeExpired = time() > ($codeIssuedAt + $codeExpirationTime) ;
 

      if($client_id !== $codeClientId)  return $this->callApiResponse(self::INVALID_GRANT_ERROR, 400);

      if ($redirect_url !== $codeClientRedirectUrl ) return $this->callApiResponse(self::INVALID_URL_GRANT_ERROR, 400);

      if( $isCodeExpired ) {
          //Mark token as invalid
        $updateToken["is_expired"] = 1 ;
        $this->oauthRepository->invalidateAuthCode($codeUniqueId, $updateToken);
        return $this->callApiResponse(self::EXPIRED_GRANT_ERROR, 400);
      }

      //If all goes well 

      //Issue Token
        $token_string = $this->generateAcessToken();
        $token_type = "Bearer";
        $user_id = $codeUserId;
        $issued_at = time();
        $expires = $this->tokenExpiry ;

        // send token to database
        $token['unique_id'] = uniqid();
        $token['token_string'] = $token_string;
        $token['client_id'] = $client_id;
        $token['user_identity'] = $user_id;
        $token['issued_at'] = $issued_at;
        $token['expiration_time'] = $expires;

        if($this->oauthRepository->saveAcessToken($token)){
             // Drop used auth code
            $this->oauthRepository->invalidateAuthCode($codeUniqueId);
             
            $data = [];
            $data["access_token"] = $token_string;
            $data["token_type"] = $token_type;
            $data["issued_at"] = $issued_at;
            $data["expires_in"] = $expires;

            $response->getBody()->write(json_encode($data));
            return $response
            ->withHeader("Content-Type", "application/json")
            ->withHeader("Cache-Control", "no-store");
        }

        else {
            return $this->callApiResponse(self::SOMETHING_WENT_ERROR);
        }
       
    }


    private function invalidateAuthCode($codeId){
         return $this->authRepository->invalidateAuthCode($codeId);
    }

    private function generateAcessToken(){
        $hex = HexGenerator::getToken(26);
        $uniq = uniqid();
        $tokenString = "TKN"; //Prefix

        for( $i=0; $i<strlen($hex); ){
            $tokenString .= $hex[$i];
            for($j=0; $j<strlen($uniq);){
               if( $i/2 == 0)
                 $tokenString .= $uniq[$j];
               $j++;
            }
            $i++;
        }
       
        return $tokenString;
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

   private function callApiResponse(array $errorDetails, $status) {
    $payload['error'] = $errorDetails['error'];
    $payload['error_description'] = $errorDetails['description'];

    $payload = json_encode($payload);
    return Responder::respond($payload, $status);
   }


}