<?php 

declare(strict_types=1);

namespace App\Service\Auth\Domain;

use App\Service\Auth\Auth ;
use App\Lib\Validator;
use App\Lib\Input;
use App\Http\Session;
use App\Service\Auth\Repositories\User as UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class PostRegister {
    private $userInputData ;
    private $userRepository ;
    private $auth;
    private $user;
    private $success_redirect_path = "/auth/login";
    private $error_redirect_path = "/auth/register";
    private $regErrorMessages=[];

    public function __construct(){
        $this->userRepository = new UserRepository();
        $this->auth = new Auth();
    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        $requiredData = [  "username", "email", "password", "phone"];
        $InputData = ( new Input(Requester::getPostData()) )->extract($requiredData);
        extract($InputData);

        if( empty($username) 
            || empty($email)
            || empty($password)
            || empty($phone)
            ) {
            $this->regErrorMessages[] = "Please fill all input fields";
        }

        $isEmailAvailable = $this->checkEmail($email) ;
        $isUsernameAvailable = $this->checkUsername($username) ;

        if(!$isUsernameAvailable)
            $this->regErrorMessages[] = "Username is already taken, try something else";

        if(!$isEmailAvailable)
            $this->regErrorMessages[] = "Error registering email";

        if(count($this->regErrorMessages)){
            // redirect to register with error messages
            Session::set("register_error_messages", $this->regErrorMessages);
            return Responder::redirect($this->error_redirect_path);
        }
        else {
            // Credentials are valid 
             $user = [];
             $user["unique_id"] = "uid" . uniqid() ;
             $user["password"] = password_hash($password, PASSWORD_DEFAULT) ;
             $user["username"] = $username ;
             $user["email"] = $email ;
             $user["phone"] = $phone ;

             $isUserSaved = $this->saveUser($user) ;

            if($isUserSaved)
                return Responder::redirect($this->success_redirect_path);
            else{
                $this->regErrorMessages[] = "Something went wrong from our end, please try again later";
                return Responder::redirect($this->error_redirect_path);
             }
        }

           
    }


    private function saveUser($user){
        return $this->userRepository->add($user);
    }

    private function checkEmail($email){
        $data = $this->userRepository->findByEmail($email);
        return empty($data) ? true : false ;
    }

    private function checkUsername($username){
        $data = $this->userRepository->findByUsername($username);
        return empty($data) ? true : false ;
    }

} 