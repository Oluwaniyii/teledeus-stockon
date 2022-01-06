<?php 

declare(strict_types=1);

namespace App\Service\OAuth;

use App\Http\Session;
use App\Service\OAuth\Repository\OAuthRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;
use App\Lib\Input;

//All this class does is accept deny or allow user grant and call appropriate OAuth class method

class OAuth {

   public function __construct(){
       $this->repository = new OAuthRepository();
   }

   public function getAccessToken($token){
       return( $this->repository->getAccessToken($token));
   }
  
   public function invalidateAccessToken($tokenId){
        return( $this->repository->invalidateAcessToken($tokenId));
   }

   public function revokeAppAccess($clientId, $userId){
        return( $this->repository->revokeMany($clientId, $userId));
 }

   private function revokeAccessToken($tokenId){
       
  }
}