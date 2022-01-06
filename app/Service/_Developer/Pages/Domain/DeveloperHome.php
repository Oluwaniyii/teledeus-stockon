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
class DeveloperHome {
    private $repository ;
    private $appRepository ;
    private $auth_initiator = null;
    private $success_redirect_path;
    private $loginRoute = "/developer/auth/login";
    private $requestUri ;

    public function __construct(){
        session::init();
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->auth = new DeveloperAuth();
        $this->appRepository = new AppRepository();
    }


    public function __invoke(Request $request, Response $response): Response {
        if(!$this->auth->getLoggedinUser())
            return Responder::redirect($this->loginRoute . "?referer=" . $this->requestUri);

            $userId = $this->auth->getLoggedinUser();
            // Fetch logged in user details and apps
            $user = $this->auth->getLoggedinUserData();
            //Fetch User apps
            $flashMessage = Session::flash();
            $apps = $this->appRepository->findApps($userId);
            $appsCount = count($apps);

            return Responder::view("console/home.twig.html",
             ["user"=>$user, 
                "apps"=>$apps, 
                "appsCount"=>$appsCount,
                "flashMessage"=>$flashMessage,
            ]);
    }

}