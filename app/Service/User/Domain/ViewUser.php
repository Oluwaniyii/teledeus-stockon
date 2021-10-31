<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ResponseInterface as Response ;
use App\Service\User\Http\Responder;

class ViewUser {
    public function __invoke(Response $response, $id){
        # Logic goes in here
        # Make sure you are either returning response or something that returns response
        $repository  = new UserRepository();
        
        // return with 404 response if no user is found
        if(!$repository->isUserAvailable($id)) {
            return Responder::respondErr(404, "User Not Found");
        }
       
        // there is a user
        $user = $repository->formatUsers($repository->findById($id)) ;
        return Responder::respondSuccess($user) ;
    }
}
