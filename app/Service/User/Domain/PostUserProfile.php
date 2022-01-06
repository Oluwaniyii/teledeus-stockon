<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\Service\Auth\Auth ;
use App\Lib\Validator;
use App\Lib\Input;
use App\Http\Session;
use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class PostUserProfile {
    private $userInputData ;
    private $userRepository ;
    private $auth;
    private $redirect_path = "/profile";
    private $updErrorMessages=[];

    public function __construct(){
        $this->userRepository = new UserRepository();
        $this->auth = new Auth();
    }

    public function __invoke(Request $request, Response $response): Response {
        Requester::setRequestObject($request);

        $userLogin = $this->auth->getLoggedinUser();

        $userFormerData = $this->userRepository->findById($userLogin);

        $requiredData = [  "username", "email", "phone", 
                                        "address_building", "address_city", "address_state", "address_zipcode"
                                    ];

        $InputData = ( new Input(Requester::getPostData()) )->extract($requiredData);

        extract($InputData);


        if( empty($username) 
            || empty($email)
            || empty($phone)
            ) {
            $this->updErrorMessages[] = "username, email and phone are important details, be sure to keep them filled";
        }

        $isEmailAvailable = $this->checkEmail($email) ;
        $isUsernameAvailable = $this->checkUsername($username) ;


        if(!$isUsernameAvailable) {
            if( $username !== $userFormerData['username']) //only allow if email is previously for thisuser
                $this->updErrorMessages[] = "Username is already taken, try something else";
        }

        if(!$isEmailAvailable){
             if($username !== $userFormerData['username'])
                $this->updErrorMessages[] = "Error registering email";
        }

        if(count($this->updErrorMessages)){
            // redirect to register with error messages
            Session::set("profile_error_messages", $this->updErrorMessages);
            return Responder::redirect($this->redirect_path);
        }
        else {
            // Inputs are valid 
             $user = [];
             $user["username"] = $username ;
             $user["email"] = $email ;
             $user["phone"] = $phone ;
             $user["address_building"] = $address_building ;
             $user["address_city"] = $address_city ;
             $user["address_state"] = $address_state ;
             $user["address_zipcode"] = $address_zipcode ;

             $isUserUpdated = $this->userRepository->update($userLogin, $user) ;

            if($isUserUpdated)
                return Responder::redirect($this->redirect_path);
            else{
                $this->updErrorMessages[] = "Something went wrong from our end, please try again later";
                return Responder::redirect($this->redirect_path);
             }
        }

        return Responder::redirect($this->redirect_path);
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