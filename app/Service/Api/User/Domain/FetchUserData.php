<?php 

declare(strict_types=1);

namespace App\Service\Api\User\Domain;

use App\Service\Api\User\Repository\User as UserRepository;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response ;
use App\Http\Responder;

class FetchUserData {
    public function __invoke(Request $request, Response $response, $user_id){
        # Logic goes in here
        # Make sure you are either returning response or something that returns response
        $repository  = new UserRepository();
        
        // return with 404 response if no user is found
        if(!$repository->isUserAvailable($user_id)) {
            return Responder::respondErr("User Not Found", 404);
        }
       
        // there is a user
        $user = $repository->format($repository->findById($user_id)) ;
        return Responder::respondSuccess($user) ;
    }
}
