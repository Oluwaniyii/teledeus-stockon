<?php 

declare(strict_types=1);

namespace App\Service\Auth\Domain;

use App\Service\Auth\Auth ;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;

// This class should be extending Auth class!
class GetLogin {
    // store REFERER in auth_initiator session variable
    // submit referer alongside post data
    // Return login form
    private $repository ;
    private $auth_initiator = null;
    private $success_redirect_path;

    public function __construct(){
        $this->auth = new Auth();
    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        // If referer parameter is specified, set referer session
        $referer = (Requester::getQueryParam("referer"));

        if(!is_null($referer)){
            $referer = $referer;
            Session::set("auth_initiator", $referer);
        }

        $this->success_redirect_path = Session::check("auth_initiator") ? Session::get('auth_initiator') : "/" ;


        // Check if user session is available'
        // if user session is set, redirect to referer or default page
        if ( $this->auth->isUserLoggedIn() ){
            return Responder::redirect( urldecode($this->success_redirect_path));
        }

        // if no user session, call on auth cookie login method
        // if auth cookie login method works, Redirect 
        if($this->auth->validateUserCookie()){
            return Responder::redirect(urldecode($this->success_redirect_path));
        }

        // if auth cookie login attempts fail, then display login form 
        $errorMessage = ""; 
        if(Session::check("login_error_messages")) {
            $errorMessage = (Session::get("login_error_messages"))[0];

            Session::unset("login_error_messages");
        }

        return Responder::view("login.twig.html", ["error_message"=>$errorMessage]);
    }

}