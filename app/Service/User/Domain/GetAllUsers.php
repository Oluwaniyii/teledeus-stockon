<?php 

declare(strict_types=1);

namespace App\Service\User\Domain;

use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ResponseInterface as Response ;
use Psr\Http\Message\StreamInterface  ;
use App\Service\User\Http\Responder;

class GetAllUsers {
    public function __invoke(Response $response){
        # Logic goes in here
        # Make sure you are either returning response object or something that returns the response object
        $repository  = new UserRepository();
        $users = $repository->formatUsers($repository->findAll());

       return Responder::respondSuccess($users);
    }
}
