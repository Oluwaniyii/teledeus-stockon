<?php

use Slim\App ;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\UserController;


return function(App $app){
    
    $app->get("/", [UserController::class, 'home']);

    // User Authentication
    $app->get("/auth/login", [UserAuthController::class, 'getLogin']);
    $app->post("/auth/login", [UserAuthController::class, 'postLogin']);
    $app->get("/auth/logout", [UserAuthController::class, 'logout']);
    $app->get("/auth/register", [UserAuthController::class, 'getRegister']);
    $app->post("/auth/register", [UserAuthController::class, 'postRegister']);

    // User Profile
    $app->get("/profile", [UserController::class, 'getUserProfile']);
    $app->post("/profile", [UserController::class, 'postUserProfile']);
    $app->get("/profile/settings", [UserController::class, 'getUserProfileSettings']);

    $app->get("/oauth/revokeaccess/{client_id}", [UserController::class, 'userRevokeAppAccess']);

};