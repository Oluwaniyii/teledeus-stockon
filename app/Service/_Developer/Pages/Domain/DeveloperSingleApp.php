<?php 

declare(strict_types=1);

namespace App\Service\_Developer\Pages\Domain;

use App\Service\_Developer\Pages\Repository\AppRepository;
use App\Service\_Developer\Auth\DeveloperAuth ;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;

// This class should be extending Auth class!
class DeveloperSingleApp {
    private $repository ;
    private $userRepository ;
    private $appRepository ;

    public function __construct(){
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->auth = new DeveloperAuth();
        $this->appRepository = new AppRepository();
    }


    public function __invoke(Request $request, Response $response): Response {
             // Protected by middleware
            Requester::setRequestObject($request);
            
            if(!$this->auth->isUserLoggedIn())
                return Responder::redirect("/developer");

            $appId = Requester::getQueryParam("app_id");
            $credentials = Requester::getQueryParam("credentials");

            $userId = $this->auth->getLoggedinUser();
            // Fetch logged in user details and apps
            $user = $this->auth->getLoggedinUserData();
            //Fetch User apps
            $app = [];
            $connectedUsers = [];

            if($credentials && ($credentials == "true")){
                $app = $this->appRepository->findAppById($appId, false);
            }
            else {
                $app = $this->appRepository->findAppById($appId, true);
            }

            $connectedUsers = $this->appRepository->findUsersConnectedToApp($appId);

            return Responder::view("console/singleapp.twig.html", 
            ["user"=>$user,
               "app"=>$app, 
               "connectedUsers"=>$connectedUsers, 
               "credentials"=>$credentials
            ]);
    }

}


