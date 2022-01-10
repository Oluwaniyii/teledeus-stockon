<?php 

declare(strict_types=1);

namespace App\Service\_Developer\App\Domain;

use App\Service\_Developer\Auth\DeveloperAuth ;
use App\Lib\HexGenerator;
use App\Lib\Validator;
use App\Lib\Input;
use App\Http\Session;
use App\Service\_Developer\App\Repository\AppRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Http\Requester;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\Response as Responsepr;
use App\Http\Responder;


class PostCreateApp {
    private $auth ;
    private $repository ;
    private $success_redirect_path = "/developer";
    private $error_redirect_path = "/developer/app/create";
    private $createErrorMessages=[];
    private const LOGGED_IN_USER = "logged_in_developer";
    private const SESSION_ERROR_MESSAGE = "developer_create_app_error_messages";

    public function __construct(){
        $this->auth = new DeveloperAuth();
        $this->repository = new AppRepository();
       Session::init();
    }

    public function __invoke(Request $request, Response $response): Response {

        if(!$this->auth->isUserLoggedIn()){
            return Responder::redirect("/developer");
        }

        Requester::setRequestObject($request);

        $requiredData = [  "app_name", "app_description", "app_type", "success_redirect_url", "error_redirect_url"];
        $InputData = ( new Input(Requester::getPostData()) )->extract($requiredData);
        extract($InputData);

        //Make sure all data is available
        if(empty($app_name)
            ||empty($app_description)
            ||empty($app_type)
            ||empty($success_redirect_url)
        )
        $this->createErrorMessages[]="App name, App description, App type, and success redirect url are compulsory";

        //makesure urls are in url format 
        if(!$this->urlValidate($success_redirect_url))
            $this->createErrorMessages[]="Success redirect must be a valid and active url";

        if(!empty($error_redirect_url)){
            if(!$this->urlValidate($error_redirect_url))
                 $this->createErrorMessages[]="Error redirect must be a valid and active url";
        }else{
            $error_redirect_url = $success_redirect_url;
        }

        if(count($this->createErrorMessages)){
            // redirect to create app with error messages
            Session::set(self::SESSION_ERROR_MESSAGE, $this->createErrorMessages);
            return Responder::redirect($this->error_redirect_path);
        }

        //Data is Valid 
        $app = [];
        $app["app_name"] = $app_name;
        $app["app_description"] = $app_description;
        $app["app_type"] = $app_type;
        $app["success_redirect_url"] = $success_redirect_url;
        $app["error_redirect_url"] = $error_redirect_url;
        
       
        if(!$this->createApp($app)){
            // return Responder::respondErr("Could not register app", 500);
            $this->createErrorMessages[]="Could not create app at the moment, please retry later";
            return Responder::redirect($this->error_redirect_path);
        }
        else {
            return Responder::redirect("/developer");
            exit;

            return Responder::redirect($this->success_redirect_path);
        }

            return Responder::redirect($this->success_redirect_path);

    }

    private function createApp($appData){
        $appData['client_id'] = HexGenerator::getRandomBytes();
        $appData['client_secret'] = HexGenerator::getToken(64);

        $app_id  = uniqid();
        $appData['unique_id'] = $app_id ;
        $appData['account_id'] = $this->auth->getLoggedInUser();

        return $this->repository->add($appData);
    } 

    private function urlValidate($url){
        if(!$url || !is_string($url)){
            return false;
        }

        if( ! preg_match('/^http(s)?:\/\/[a-z0-9-]+(\.[a-z0-9-]+)*(:[0-9]+)?(\/.*)?$/i', $url) ){
            return false;
        }

        if($this->getHttpResponseCode_using_curl($url) != 200){
            return false;
        }
        // all good!
        return true;
        
    }

    private function getHttpResponseCode_using_curl($url, $followredirects = true){
        if(! $url || ! is_string($url)){
            return false;
        }
        $ch = @curl_init($url);
        if($ch === false){
            return false;
        }
        @curl_setopt($ch, CURLOPT_HEADER         ,true);   
        @curl_setopt($ch, CURLOPT_NOBODY         ,true);   
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);

        if($followredirects){
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
            @curl_setopt($ch, CURLOPT_MAXREDIRS      ,10);  
        }else{
            @curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,false);
        }

        @curl_exec($ch);
        if(@curl_errno($ch)){   // should be 0
            @curl_close($ch);
            return false;
        }
        $code = @curl_getinfo($ch, CURLINFO_HTTP_CODE); // note: php.net documentation shows this returns a string, but really it returns an int
        @curl_close($ch);
        return $code;
    }
    

}