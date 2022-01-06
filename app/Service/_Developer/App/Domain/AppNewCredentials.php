<?php 

declare(strict_types=1);

namespace App\Service\_Developer\App\Domain;

use App\Lib\HexGenerator;
use App\Service\_Developer\Pages\Repository\AppRepository;
use App\Service\_Developer\Auth\DeveloperAuth ;
use App\Http\Flash;
use App\Http\Session;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use App\Http\Responder;

// This class should be extending Auth class!
class AppNewCredentials {
    private $repository ;
    private const REDIRECT_PATH = "/developer";
    private const ERROR_MESSAGE = "Could not issue new credentials to your app";
    private const SUCCESS_MESSAGE = "New credentials successfully issued to your app";

    public function __construct(){
        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->auth = new DeveloperAuth();
        $this->repository = new AppRepository();
        session::init();
    }


    public function __invoke(Request $request, Response $response): Response {
             // Protected by middleware
            Requester::setRequestObject($request);
            
            $app_id = (Requester::getPostData())['app_id'];
            $userId = $this->auth->getLoggedinUser();

            $app = $this->repository->findAppById($app_id);
    

            if(empty($app)){
                Session::flash(self::ERROR_MESSAGE);
                return Responder::redirect(self::REDIRECT_PATH);
            }
    
            if ( $userId !== $app['account_id'] ) {
                Session::flash(self::ERROR_MESSAGE);
                return Responder::redirect(self::REDIRECT_PATH);
            }

            // proceed to generate new creentails
            $appData['client_id'] = HexGenerator::getRandomBytes();
            $appData['client_secret'] = HexGenerator::getToken(64);
    
            if(!$this->updateAppCredentials($app_id, $appData)){
                Session::flash(self::ERROR_MESSAGE);
                return Responder::redirect(self::REDIRECT_PATH);
            }
            else{
                Session::flash(self::SUCCESS_MESSAGE);
                return Responder::redirect(self::REDIRECT_PATH);
            }
    
        }

         private function updateAppCredentials($appID, $newCredentials){
            return $this->repository->update($appID, $newCredentials);
        }
}
