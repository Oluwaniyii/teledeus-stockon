<?php 

use Slim\App ;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\DeveloperController;
use App\Http\Controllers\APIController;
use App\Middleware\EnsureAuthenticatedClientMiddleware;
use App\Middleware\EnsureAuthenticatedUserMiddleware;
use App\Middleware\EnsureAuthenticatedDeveloperMiddleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;


return function (App $app)
{
    $app->get("/", [PagesController::class, 'home']);

    $app->get("/auth/login", [AuthController::class, 'getLogin']);
    $app->post("/auth/login", [AuthController::class, 'postLogin']);
    $app->get("/auth/logout", [AuthController::class, 'logout']);
    $app->get("/auth/register", [AuthController::class, 'getRegister']);
    $app->post("/auth/register", [AuthController::class, 'postRegister']);

    $app->get("/oauth/authorize", [OAuthController::class, 'clientAuthorize']);
    $app->post("/oauth/authorize", [OAuthController::class, 'userAuthorizeGrant']);
    $app->post("/oauth/token", [OAuthController::class, 'accesTokenGrant']);
    $app->post("/oauth/tokeninfo", [OAuthController::class, 'getTokenInfo']);
    
    $app->get("/oauth/error", function(Request $request, Response $response): Response {
        return Responder::view("autherror.twig.html");
    });

    $app->get("/profile", [PagesController::class, 'getUserProfile']);
    // ->add(EnsureAuthenticatedUserMiddleware::class);
    $app->post("/profile", [PagesController::class, 'postUserProfile']);
    // ->add(EnsureAuthenticatedUserMiddleware::class);
    $app->get("/profile/settings", [PagesController::class, 'getUserProfileSettings']);
    // ->add(EnsureAuthenticatedUserMiddleware::class);
    $app->get("/oauth/revokeaccess/{client_id}", [PagesController::class, 'userRevokeAppAccess']);
    // ->add(EnsureAuthenticatedUserMiddleware::class);

    //User fetch Api 
    $app->get("/api/user/{user_id}", [APIController::class, 'fetchUserData'])->add(EnsureAuthenticatedClientMiddleware::class);

     //Developer Console
    $app->get("/developer/auth/login", [DeveloperController::class, 'developerGetLogin']);
    $app->post("/developer/auth/login", [DeveloperController::class, 'developerPostLogin']);
    $app->get("/developer/auth/logout", [DeveloperController::class, 'developerLogout']);
    $app->get("/developer/auth/register", [DeveloperController::class, 'developerGetRegister']);
    $app->post("/developer/auth/register", [DeveloperController::class, 'developerPostRegister']);
    $app->get("/developer", [DeveloperController::class, 'developerHome'])->add(EnsureAuthenticatedDeveloperMiddleware::class);
    $app->get("/developer/app/create", [DeveloperController::class, 'developerGetCreateApp']);
    // ->add(EnsureAuthenticatedDeveloperMiddleware::class);
    $app->post("/developer/app/create", [DeveloperController::class, 'developerPostCreateApp']);
    // ->add(EnsureAuthenticatedDeveloperMiddleware::class);
    $app->get("/developer/app/view", [DeveloperController::class, 'developerSingleApp']);
    // ->add(EnsureAuthenticatedDeveloperMiddleware::class);
    $app->post("/developer/app/newcredentials", [DeveloperController::class, 'developerAppNewCredentials']);
    // ->add(EnsureAuthenticatedDeveloperMiddleware::class);
};