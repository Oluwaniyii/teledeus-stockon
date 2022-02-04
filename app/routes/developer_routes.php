<?php

use Slim\App ;
use App\Http\Controllers\DeveloperController;


return function(App $app) {
    // Developer auth
    $app->get("/developer/auth/login", [DeveloperController::class, 'developerGetLogin']);
    $app->post("/developer/auth/login", [DeveloperController::class, 'developerPostLogin']);
    $app->get("/developer/auth/logout", [DeveloperController::class, 'developerLogout']);
    $app->get("/developer/auth/register", [DeveloperController::class, 'developerGetRegister']);
    $app->post("/developer/auth/register", [DeveloperController::class, 'developerPostRegister']);
   
    // Developer console
    $app->get("/developer", [DeveloperController::class, 'developerHome']);
    $app->get("/developer/app/create", [DeveloperController::class, 'developerGetCreateApp']);
    $app->post("/developer/app/create", [DeveloperController::class, 'developerPostCreateApp']);
    $app->get("/developer/app/view", [DeveloperController::class, 'developerSingleApp']);
    $app->post("/developer/app/newcredentials", [DeveloperController::class, 'developerAppNewCredentials']);    
};
