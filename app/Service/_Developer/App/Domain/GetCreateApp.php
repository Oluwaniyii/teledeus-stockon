<?php 

declare(strict_types=1);

namespace App\Service\_Developer\App\Domain ;


use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;

// This class should be extending Auth class!
class GetCreateApp {
    private $repository ;
    private $auth_initiator = null;
    private $success_redirect_path;

    private const SESSION_ERROR_MESSAGE = "developer_create_app_error_messages";


    public function __construct(){
        Session::init();
    }

    public function __invoke(Request $request, Response $response): Response {
        // if auth cookie login attempts fail, then display login form 
        $errorMessage = ""; 
        if(Session::check(self::SESSION_ERROR_MESSAGE)) {
            $errorMessage = (Session::get(self::SESSION_ERROR_MESSAGE))[0];

            Session::unset(self::SESSION_ERROR_MESSAGE);
        }

        return Responder::view("console/createapp.twig.html", ["error_message"=>$errorMessage]);
    }

}