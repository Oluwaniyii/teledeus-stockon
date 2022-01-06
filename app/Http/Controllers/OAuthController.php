<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\Auth\Auth;
use Twig\Loader\FilesystemLoader as TwigLoader;
use Twig\Environment as TwigEnv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Responder ;



class OAuthController 
{
    public function clientAuthorize(Request $request, Response $response): Response {
        return (new \App\Service\OAuth\Domain\Authorize())($request, $response) ; 
    }

    public function userAuthorizeGrant(Request $request, Response $response): Response {
        return (new \App\Service\OAuth\Domain\OAuthAccessGrant())($request, $response) ; 
    }

    public function accesTokenGrant(Request $request, Response $response): Response {
        return (new \App\Service\OAuth\Domain\AccesTokenGrant())($request, $response) ; 
    }

    public function getTokenInfo(Request $request, Response $response): Response {
        return (new \App\Service\OAuth\Domain\GetTokenInfo())($request, $response) ; 
    }
} 
