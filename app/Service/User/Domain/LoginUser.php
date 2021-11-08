<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\JWT ;
use App\Lib\Validator;
use App\Lib\Input;
use App\Service\User\Repository\User as UserRepository;
use App\Service\User\Http\Responder;
use Psr\Http\Message\ResponseInterface as Response ;
use Psr\Http\Message\ServerRequestInterface as Request ;

class LoginUser {
    private $repository ;

    public function __construct(){
         $this->repository = new UserRepository();
    }
    public function __invoke(Request $request, Response $response): Response {
         # Logic goes in here
        # Make sure you are either returning response or something that returns response
        $requiredData = [  "email",  "password"];
        $InputData = (new Input($request->getParsedBody()) )->extract($requiredData);

        extract($InputData);

        if(!$email || !$password) return Responder::respondErr("Empty Credentials", 400);

        // Grab data with given email;
        $userData = $this->repository->findByEmail($email);

        // No entry found
        if(empty($userData)) return Responder::respondErr("Invalid Credentials", 400) ;

        // Verify password
        $isPasswordCorrect = password_verify($password, $userData['password']) ;
        if(!$isPasswordCorrect) return Responder::respondErr("Invalid Credentials", 400) ;

        // Credentials are valid ;
        $data = [];
        $data['token'] = (JWT::generateToken([ "uid"=>$userData['unique_id'] ]))['token'];

        return Responder::respondSuccess($data);
    }
}