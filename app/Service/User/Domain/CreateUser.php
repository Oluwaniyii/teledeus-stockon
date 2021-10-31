<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\JWT ;
use App\Lib\Validator;
use App\Lib\Input;
use App\Service\User\Http\Responder;
use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ResponseInterface as Response ;
use Psr\Http\Message\StreamInterface  ;
use Psr\Http\Message\ServerRequestInterface as Request ;

class CreateUser {
    private $repository ;
    private $userID ;

    public function __construct(){
         $this->repository = new UserRepository();
    }

    public function __invoke(Request $request, Response $response){
        # Logic goes in here
        # Make sure you are either returning response or something that returns response
        $requiredData = [  "username",  "password",  "email", "phone", 
                                        "address_building", "address_city", "address_state",
                                        "address_zipcode"];

        $userData = (new Input($request->getParsedBody()) )->extract($requiredData);

        // Input Validation
        $validatorSchema = [
                "username" => [
                        "name" => "username",
                        "required" => true,
                        "min" => 3,
                        "max" => 30,
                ],

                "password" => [
                        "name" => "password",
                        "required" => true,
                        "min" => 8,
                        "max" => 15,
                ],

                "email" => [
                        "name" => "email",
                        "required" => true,
                        "max" => 30,
                ],

            ];

        Validator::validate($userData, $validatorSchema);

        if(!Validator::validated()) return Responder::respondErr((Validator::errorrs())[0], 400);

        if( count($this->repository->findByEmail($userData['email'])) ) return Responder::respondErr("Could Not Create User", 400);

        if(!$this->createUser($userData)) return Responder::respondErr("Something Went Wrong", 500);

        // Respond with 200 and user data ;
        // Auto generate Token
        $data = [];
        $user = $this->repository->formatUsers($this->repository->findById($this->userID)) ;
        $data['user'] = $user ;
        $data['message'] = "User Created" ;
        $data['token'] = (JWT::generateToken([ "uid"=>$user['unique_id'] ]))['token'];

        return Responder::respondSuccess($data);
    }  
    
    public function createUser($userData) {
        $user_unique_id = uniqid('uid');
        $user_hashed_password = password_hash($userData['password'], PASSWORD_DEFAULT);
        $userData["unique_id"] = $user_unique_id;
        $userData["password"] = $user_hashed_password;

        $this->userID = $user_unique_id;

        if($this->repository->add($userData)) {
            return true ;
        } 
        return false ;
    }

}
