<?php 

declare(strict_types=1);

namespace App\Service\Auth\Domain;

use App\Service\Auth\Auth ;
use App\Http\Session;
use App\Service\User\Repository\User as UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


class Logout {
    private $userInputData ;
    private $repository ;
    private $auth;
    private $user;
    private $success_redirect_path;
    private $loginErrorMessages=[];

    public function __construct(){
        $this->repository = new UserRepository();
        $this->auth = new Auth();
    }

    public function __invoke(Request $request, Response $response): Response {
            Requester::setRequestObject($request);
            $this->auth->unsetUserCookie();
            $this->auth->unsetLoggedinUser();
                
            return Responder::redirect(urldecode("/"));
        }
        
    }