<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\Auth\Auth;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Responder ;


class UserAuthController 
{
    public function getLogin(Request $request, Response $response) {
      return (new \App\Service\Auth\Domain\GetLogin())($request, $response) ; 
    }

    public function postLogin(Request $request, Response $response) {
      return (new \App\Service\Auth\Domain\PostLogin())($request, $response) ; 
    }

    public function logout(Request $request, Response $response): Response {
      // Destroy sessions, cookies and redirect to login
      $auth = new Auth();
      $auth->unsetLoggedinUser();
      $auth->deleteUserAuthCookies();

      return Responder::redirect("/auth/login");
    }

    // Registration routes
    public function getRegister(Request $request, Response $response) {
      return (new \App\Service\Auth\Domain\GetRegister())($request, $response) ; 
    }

    public function postRegister(Request $request, Response $response) {
      return (new \App\Service\Auth\Domain\PostRegister())($request, $response) ; 
    }
}