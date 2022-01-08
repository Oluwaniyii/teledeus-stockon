<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\Service\Auth\Auth ;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;
use App\Service\User\Repository\User as UserRepository;

// This class should be extending Auth class!
class GetUserProfile {
    // store REFERER in auth_initiator session variable
    // submit referer alongside post data
    // Return login form
    private $repository ;
    private $referer ;
    private $auth;
    private $homepage = "/";

    public function __construct(){
        $this->auth = new Auth();
        $this->repository = new UserRepository();
        $this->referer = $_SERVER['REQUEST_URI'];

    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        if(!$this->auth->isUserLoggedIn())
        return Responder::redirect("/auth/login?referer=" . urlencode($this->referer));

        $userLogin = $this->auth->getLoggedinUser();
        $user = $this->repository->findById($userLogin);

        $errorMessage = ""; 
        if(Session::check("profile_error_messages")) {
            $errorMessage = (Session::get("profile_error_messages"))[0];

            Session::unset("profile_error_messages");
        }
        return Responder::view("userprofile.twig.html", [ "user"=>$user, "error_message"=>$errorMessage ]);
    }

}