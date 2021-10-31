<?php 

declare(strict_types=1);

namespace App\Service\User ;

use Psr\Http\Message\ResponseInterface as Response ;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\StreamInterface  ;
use Slim\Exception\HttpNotFoundException;

class UserController {
    // The Controller only takes request, calls the appropriate domain function with credentials or parameters and return the response;
    public function get(Request $request, Response $response) {
      return (new \App\Service\User\Domain\GetAllUsers())($response) ; 
    }

    public function view(Request $request, Response $response, $id=null) {
        return (new \App\Service\User\Domain\ViewUser())($response, $id) ; 
      }

    public function add(Request $request, Response $response) {
      return (new \App\Service\User\Domain\CreateUser())($request, $response) ; 
    }
  
    public function update(Request $request, Response $response, $id) {
      return (new \App\Service\User\Domain\UpdateUser())($request, $response, $id) ; 
    }

    public function remove(Request $request, Response $response, $id) {
      return (new \App\Service\User\Domain\DeleteUser())($request, $response, $id) ; 
    }

    public function login(Request $request, Response $response) {
      return (new \App\Service\User\Domain\LoginUser())($request, $response) ; 
    }  


}

