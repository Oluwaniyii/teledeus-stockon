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
class GetRegister {
    // store REFERER in auth_initiator session variable
    // submit referer alongside post data
    // Return login form
    private $repository ;
    private $auth_initiator = null;
    private $homepage = "/";

    public function __construct(){
        $this->auth = new Auth();
    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        // Check if user session is available'
        // if user session is set, redirect to referer or default page
        if ( $this->auth->isUserLoggedIn() ){
            return Responder::redirect( urldecode($this->homepage));
        }

        // if auth cookie login attempts fail, then display login form with errors
        $errorMessage = ""; 
        if(Session::check("register_error_messages")) {
            $errorMessage = (Session::get("register_error_messages"))[0];

            Session::unset("register_error_messages");
        }

        return Responder::view("register.twig.html", ["error_message"=>$errorMessage]);
    }

}