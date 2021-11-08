<?php 

declare(strict_types=1);

namespace App\Service\Auth ;

use Psr\Http\Message\ResponseInterface as Response ;
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\StreamInterface  ;
use Slim\Exception\HttpNotFoundException;

class ClientAuthController {
    // The Controller only takes request, calls the appropriate domain function with credentials or parameters and return the response;
    public function credentials(Request $request, Response $response) {
      return (new \App\Service\Auth\Domain\ClientAuthorize())($request, $response) ; 
    }

}

