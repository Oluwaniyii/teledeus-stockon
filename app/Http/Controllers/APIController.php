<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\Auth\Auth;
use Twig\Loader\FilesystemLoader as TwigLoader;
use Twig\Environment as TwigEnv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Responder ;



class APIController 
{
    public function fetchUserData(Request $request, Response $response, $user_id): Response {
        return (new \App\Service\Api\User\Domain\FetchUserData())($request, $response, $user_id) ; 
    }

} 
