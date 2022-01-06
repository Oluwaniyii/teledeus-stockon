<?php

declare(strict_types=1);

namespace App\Http ;

use Slim\Psr7\Request;
use Slim\Routing\RouteContext ;

class Requester {
    public static $request = null ;
    
    /**
     * @method setRequestObject
     * @param object request
     */
    public static function setRequestObject($request) {
       self::$request = $request;
    }

    
    public static function getRouteParam($param) {
        self::requestLoadChecker();

        $routeContext =  RouteContext::fromRequest(self::$request);
        $route = $routeContext->getRoute();
        $paramValue = $route->getArgument($param);
        return $paramValue;
    }

    public static function getQueryParam($param) {
        self::requestLoadChecker();

        $queryParameters = self::$request->getQueryParams() ;

        if ( isset($queryParameters[$param]) )
            return $queryParameters[$param] ;
        else 
           return null;
    }

    public static function getPostData() {
        self::requestLoadChecker();

        return self::$request->getParsedBody();
    }


    private static function requestLoadChecker(){
        if(is_null(self::$request)) throw new \Exception("Request object not set");
    }

}