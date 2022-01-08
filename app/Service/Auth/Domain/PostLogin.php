<?php 

declare(strict_types=1);

namespace App\Service\Auth\Domain;

use App\Service\Auth\Auth ;
use App\Lib\Validator;
use App\Lib\Input;
use App\Http\Session;
use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class PostLogin {
    private $repository ;
    private $auth;
    private $success_redirect_path;
    private $error_redirect_path = "/auth/login";
    private $loginErrorMessages=[];
   
    public function __construct(){
        $this->repository = new UserRepository();
        $this->auth = new Auth();

        $this->success_redirect_path = Session::check("auth_initiator") ? Session::get('auth_initiator') : "/" ;
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
            Session::set("login_error_messages", $this->loginErrorMessages);
            return Responder::redirect($this->error_redirect_path);
        }else {
            // Credentials are valid 
            // Set auth session 
            $this->auth->setLoggedinUser($userData['unique_id']);

            // set auth cookie
            // $this->auth->setNewUserCookies($userData['unique_id']);+
            
            //Unset auth_initiator and redirect
            Session::unset("auth_initiator");
            return Responder::redirect(urldecode($this->success_redirect_path));
        }
        
    }

}