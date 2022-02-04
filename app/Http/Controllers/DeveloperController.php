<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\_Developer\Auth\DeveloperAuth ;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Responder ;
use App\Service\User\Repository\User;



class DeveloperController 
{
    //Dveloper Auth
    public function developerHome(Request $request, Response $response): Response {
            return (new \App\Service\_Developer\Pages\Domain\DeveloperHome())($request, $response) ; 
    }
   
    public function developerGetLogin(Request $request, Response $response): Response {
         return (new \App\Service\_Developer\Auth\Domain\GetLogin())($request, $response) ; 
    }

   public function developerPostLogin(Request $request, Response $response): Response {
      return (new \App\Service\_Developer\Auth\Domain\PostLogin())($request, $response) ; 
   }

   public function developerGetRegister(Request $request, Response $response): Response {
      return (new \App\Service\_Developer\Auth\Domain\GetRegister())($request, $response) ; 
   }

   public function developerPostRegister(Request $request, Response $response): Response {
         return (new \App\Service\_Developer\Auth\Domain\PostRegister())($request, $response) ; 
   }

   public function developerLogout(Request $request, Response $response): Response {
         // Destroy sessions, cookies and redirect to login
         $auth = new DeveloperAuth();
         $auth->unsetLoggedinUser();

         return Responder::redirect("/developer/auth/login");
   }

   // Developer Console
   public function developerGetCreateApp(Request $request, Response $response): Response {
         return (new \App\Service\_Developer\App\Domain\GetCreateApp())($request, $response) ; 
   }

   public function developerPostCreateApp(Request $request, Response $response): Response {
         return (new \App\Service\_Developer\App\Domain\PostCreateApp())($request, $response) ; 
   }


   public function developerSingleApp(Request $request, Response $response): Response {
        return (new \App\Service\_Developer\Pages\Domain\DeveloperSingleApp())($request, $response) ; 
   }

   public function developerAppNewCredentials(Request $request, Response $response): Response {
        return (new \App\Service\_Developer\App\Domain\AppNewCredentials())($request, $response) ; 
   }

}
