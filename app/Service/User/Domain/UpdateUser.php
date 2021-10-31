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

class UpdateUser {
    private $repository ;
    private $userID ;

    public function __construct(){
         $this->repository = new UserRepository();
    }

    public function __invoke(Request $request, Response $response, $id){
        # Logic goes in here
        # Make sure you are either returning response or something that returns response
        // Check if user exists

        if( !count($this->repository->findById($id)) ) return Responder::respondErr("User Not Found", 404);
        
        $this->userID = $id ;

        $requiredData = [  "username",  "password",  "email", "phone", 
                                        "address_building", "address_city", "address_state",
                                        "address_zipcode"];

        $inputData = (new Input($request->getParsedBody()) )->extract($requiredData);

        // Input Validation
        $validatorSchema = [
                "username" => [
                        "name" => "username",
                        "min" => 3,
                        "max" => 30,
                        "required"=>true,
                ],
                "email" => [
                        "name" => "email",
                        "max" => 30,
                        "required" => true
                ],

            ];


        Validator::validate($inputData, $validatorSchema);

        if(!Validator::validated()) return Responder::respondErr((Validator::errorrs())[0], 400);

        if(!$this->updateUser($inputData)) return Responder::respondErr("Something Went Wrong", 500);

        // Respond with 200 and user data ;
        $data = [];
        $user = $this->repository->formatUsers($this->repository->findById($id)) ;
        $data['user'] = $user ;
        $data['message'] = "User Updated" ;

        return Responder::respondSuccess($data);

        exit();
    }  
    
    public function updateUser($userData) {
        if($userData['password']){
            $user_hashed_password = password_hash($userData['password'], PASSWORD_DEFAULT);
            $userData["password"] = $user_hashed_password;
        }

        return $this->repository->update($this->userID, $userData) ? true : false ;
    }

}
