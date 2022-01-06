<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\Service\Auth\Auth ;
use App\Service\OAuth\Repository\OAuthRepository ;
use App\Service\OAuth\Repository\AppRepository ;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;
use App\Service\User\Repository\User as UserRepository;

// This class should be extending Auth class!
class GetUserProfileSettings {
    // store REFERER in auth_initiator session variable
    // submit referer alongside post data
    // Return login form
    private $repository ;
    private $oauthRepository;
    private $appRepository;
    private $auth;
    private $homepage = "/";

    public function __construct(){
        $this->auth = new Auth();
        $this->repository = new UserRepository();
        $this->oauthRepository = new OAuthRepository();
        $this->appRepository = new AppRepository();
        
    }

    public function __invoke(Request $request, Response $response): Response {
        // Requester::setRequestObject($request);
        $userLogin = $this->auth->getLoggedinUser();
        $user = $this->repository->findById($userLogin);

        $appsWithAccess = [];
        $clientIdsWithActiveAccess =  $this->getActiveOAuthGrants($userLogin);

        foreach ($clientIdsWithActiveAccess as $clientId){
            $appsWithAccess[] = $this->findClientApp($clientId);
        }

        return Responder::view("userprofilesettings.twig.html", [ "apps"=>$appsWithAccess]);
    }


    // private function getAppsWithAccess(){}
    private function getActiveOAuthGrants($userLogin):array {
        return $this->oauthRepository->getUserActiveTokens($userLogin);
    }

    private function findClientApp($clientId):array {

        $app = $this->appRepository->findAppWithClientId($clientId, $safe=true);
        return $app;
    }


}