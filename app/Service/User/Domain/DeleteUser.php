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

class DeleteUser {
    private $repository ;
    private $userID ;

    public function __construct(){
         $this->repository = new UserRepository();
    }

    public function __invoke(Request $request, Response $response, $id){
        if( !count($this->repository->findById($id)) ) return Responder::respondErr("User Not Found", 404);

        if(!$this->deleteUser($id)) return Responder::respondErr("Something Went Wrong", 500);

        // Respond with 200 and user data ;
        $data = [];
        $data['message'] = "User Removed" ;

        return Responder::respondSuccess($data);
    }  
    
    public function deleteUser($id) {
        return $this->repository->drop($id) ? true : false ;
    }

}
