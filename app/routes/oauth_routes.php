<?php

use Slim\App ;
use App\Http\Controllers\OAuthController;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


return function(App $app) {
    $app->get("/oauth/authorize", [OAuthController::class, 'clientAuthorize']);
    $app->post("/oauth/authorize", [OAuthController::class, 'userAuthorizeGrant']);
    $app->post("/oauth/token", [OAuthController::class, 'accesTokenGrant']);
    $app->post("/oauth/tokeninfo", [OAuthController::class, 'getTokenInfo']);
    
    $app->get("/oauth/error", function(): Response {
        return Responder::view("autherror.twig.html");
    });
};
