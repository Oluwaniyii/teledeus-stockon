<?php 

declare(strict_types=1);

namespace App\Service\_Developer\Auth\Domain;

use App\Lib\Validator;
use App\Lib\Input;
use App\Http\Session;
use App\Service\_Developer\Auth\DeveloperAuth ;
use App\Service\_Developer\Auth\Repository\AccountRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class PostLogin {
    private $repository ;
    private $auth;
    private $success_redirect_path;
    private $error_redirect_path = "/developer/auth/login";
    private $loginErrorMessages=[];
    private const AUTH_INITIATOR = "developer_auth_initiator";
    private const LOGGED_IN_USER = "logged_in_developer";
    private const SESSION_ERROR_MESSAGE = "developer_login_error_messages";


    public function __construct(){
        $this->repository = new AccountRepository();
        $this->auth = new DeveloperAuth();
       Session::init();


        $this->success_redirect_path = Session::check(self::AUTH_INITIATOR) ? Session::get((self::AUTH_INITIATOR)) : "/developer" ;
    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        $requiredData = [  "email",  "password"];
        $InputData = ( new Input(Requester::getPostData()) )->extract($requiredData);
        extract($InputData);

        if(!$email || !$password) {
            $this->loginErrorMessages[] = "Fill all input fields";
        }

        // Grab data with given email;
        $userData = $this->repository->findByEmail($email);

        // No entry found
        if(empty($userData)){
            $this->loginErrorMessages[] = "Invalid Credentials";
        }
        else {
            if( !password_verify($password, $userData['password']) ){
                 $this->loginErrorMessages[] = "Invalid Credentials";
            }
        }


        if(count($this->loginErrorMessages)){
            //Validation Error
            // redirect to login with error messages
            Session::set(self::SESSION_ERROR_MESSAGE, $this->loginErrorMessages);
            return Responder::redirect($this->error_redirect_path);
        }else {
            // Credentials are valid 
            // Set auth session 
            $this->auth->setLoggedinUser($userData['unique_id']);

            // set auth cookie
            // $this->auth->setNewUserCookies($userData['unique_id']);+
            
            //Unset auth_initiator and redirect
            Session::unset(self::AUTH_INITIATOR);
            return Responder::redirect(urldecode($this->success_redirect_path));
        }
        
    }

}