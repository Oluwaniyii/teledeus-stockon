<?php

use Slim\App ;
use App\Http\Controllers\OAuthController;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;
use App\Http\Middleware\EnsureAuthenticatedClientMiddleware;



return function(App $app) {
     //User fetch Api 
     $app->get("/api/user/{user_id}", [APIController::class, 'fetchUserData'])->add(EnsureAuthenticatedClientMiddleware::class);
};
