<?php 

declare(strict_types=1);

namespace App\Service\_Developer\Auth\Domain;


use App\Lib\Validator;
use App\Lib\Input;
use App\Service\_Developer\Auth\Repository\AccountRepository;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class PostRegister {
    private $repository ;
    private $success_redirect_path = "/developer/auth/login";
    private $error_redirect_path = "/developer/auth/register";
    private $regErrorMessages=[];
    private const AUTH_INITIATOR = "developer_auth_initiator";
    private const LOGGED_IN_USER = "logged_in_developer";
    private const SESSION_ERROR_MESSAGE = "developer_register_error_messages";

    public function __construct(){
        $this->repository = new AccountRepository();
       Session::init();
    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        $requiredData = [  "firstname", "lastname", "email", "password", "confirm_password"];
        $InputData = ( new Input(Requester::getPostData()) )->extract($requiredData);
        extract($InputData);

        // Input Validation
        $validatorSchema = [
            "firstname" => [
                    "name" => "firstname",
                    "required" => true,
                    "min" => 2,
                    "max" => 30,
            ],

            "lastname" => [
                "name" => "lastname",
                "required" => true,
                "min" => 2,
                "max" => 30,
            ],

            "email" => [
                    "name" => "email",
                    "required" => true,
                    "max" => 60,
            ],

            "password" => [
                "name" => "password",
                "required" => true,
                "min" => 8,
                "max" => 15,
            ],

            "confirm_password" => [
                "name" => "confirm password",
                "required" => true,
                "min" => 8,
                "max" => 15,
            ],

        ];


        Validator::validate($InputData, $validatorSchema);

        if(!Validator::validated()) 

        $this->regErrorMessages[] = (Validator::errors())[0] ;

            if($confirm_password !== $password)
                 $this->regErrorMessages[] = "Passwords do not match";

            $isEmailAvailable = $this->checkEmail($email) ;

            if(!$isEmailAvailable)
                $this->regErrorMessages[] = "Error registering email";
            
            if(count($this->regErrorMessages)){
                // redirect to register with error messages
                Session::set(self::SESSION_ERROR_MESSAGE, $this->regErrorMessages);
                return Responder::redirect($this->error_redirect_path);
            }
            else {
                // Credentials are valid 
                $user = [];
                $user["unique_id"] = "uid" . uniqid() ;
                $user["firstname"] = $firstname ;
                $user["lastname"] = $lastname ;
                $user["email"] = $email ;
                $user["password"] = password_hash($password, PASSWORD_DEFAULT) ;

                $isUserSaved = $this->saveUser($user) ;

                if($isUserSaved)
                    return Responder::redirect($this->success_redirect_path);
                else{
                    $this->regErrorMessages[] = "Something went wrong from our end, please try again later";
                    Session::set(self::SESSION_ERROR_MESSAGE, $this->regErrorMessages);
                    return Responder::redirect($this->error_redirect_path);
                }
            }

        }

        private function saveUser($user){
            return $this->repository->add($user);
        }

        private function checkEmail($email){
            $data = $this->repository->findByEmail($email);
            return empty($data) ? true : false ;
        }

        private function checkUsername($username){
            $data = $this->repository->findByUsername($username);
            return empty($data) ? true : false ;
        }

} 