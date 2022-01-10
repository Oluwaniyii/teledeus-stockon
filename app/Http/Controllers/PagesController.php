<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\Auth\Auth;
use Twig\Loader\FilesystemLoader as TwigLoader;
use Twig\Environment as TwigEnv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Responder ;
use App\Service\User\Repository\User;



class PagesController 
{
     public function home(Request $request, Response $response): Response {
         $auth = new Auth();
         $userRepository = new User();

         if( $auth->isUserLoggedIn() ){
          $userLogin = null;
          $userLogin = $auth->getLoggedinUser();
          $user = $userRepository->findById($userLogin);
          
          return Responder::view("home.twig.html", ["username" => $user['username'], "uid" => $user['unique_id'] ]);
         }

         return Responder::redirect("/auth/login");
    }


    public function getUserProfile(Request $request, Response $response): Response {
            return (new \App\Service\User\Domain\GetUserProfile())($request, $response) ; 
      }

    public function postUserProfile(Request $request, Response $response): Response {
        return (new \App\Service\User\Domain\PostUserProfile())($request, $response) ; 
    }

    public function getUserProfileSettings(Request $request, Response $response): Response {
      return (new \App\Service\User\Domain\GetUserProfileSettings())($request, $response) ; 
    }

  public function userRevokeAppAccess(Request $request, Response $response, $client_id): Response {
    return (new \App\Service\User\Domain\UserRevokeAccess())($request, $response, $client_id) ; 
  }

}
