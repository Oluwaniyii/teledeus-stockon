<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\Service\OAuth\OAuth ;
use App\Service\Auth\Auth ;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;
use App\Service\User\Repository\User as UserRepository;

// This class should be extending Auth class!
class UserRevokeAccess {
    // store REFERER in auth_initiator session variable
    // submit referer alongside post data
    // Return login form
    private $repository ;
    private $auth;
    private $oauth;
    private $homepage = "/";
    private $referer ;

    public function __construct(){
        $this->auth = new Auth();
        $this->oauth = new OAuth();
        $this->repository = new UserRepository();
        $this->referer = $_SERVER['REQUEST_URI'];
    }

    public function __invoke(Request $request, Response $response, $client_id): Response {
        
        if(!$this->auth->isUserLoggedIn())
        return Responder::redirect("/auth/login?referer=" . urlencode($this->referer));

        $userLogin = $this->auth->getLoggedinUser();

        $this->oauth->revokeAppAccess($client_id, $userLogin);
        return Responder::redirect("/profile/settings");

    }

}